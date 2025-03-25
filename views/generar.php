<?php
require_once 'config/database.php';
require_once 'views/fpdf/fpdf.php';

// Crear una clase personalizada para el PDF
class PDF extends FPDF
{
    // Cabecera del documento
    function Header()
    {
        // Logo de la empresa
        $this->Image("../../assets/img/banner.jpeg", 10, 10, 30, 30, 'PNG');

        // Título del documento
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Reporte de Venta'), 0, 1, 'C');

        // Información de la empresa
        $this->SetFont('Arial', '', 10);
        if (!empty($this->datosEmpresa) && is_array($this->datosEmpresa)) {
            $this->Cell(0, 5, utf8_decode('Nombre de la Empresa: ' . ($this->datosEmpresa['nombre'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Teléfono: ' . ($this->datosEmpresa['telefono'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Dirección: ' . ($this->datosEmpresa['direccion'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Correo: ' . ($this->datosEmpresa['email'] ?? 'No disponible')), 0, 1, 'L');
        } else {
            $this->Cell(0, 5, utf8_decode(''), 0, 1, 'L');
        }

        // Mostrar la fecha de la venta
        if (!empty($this->datosFecha)) {
            $this->Cell(0, 5, utf8_decode('Fecha de Venta: ' . $this->datosFecha), 0, 1, 'L');
        }

        // Espacio después de la cabecera
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    // Datos de la empresa
    public $datosEmpresa;

    // Fecha de la venta
    public $datosFecha;

    // Datos del cliente
    public $datosCliente;

    // Detalles de la venta
    public $detallesVenta;
}

// Crear el PDF
$pdf = new PDF('P', 'mm', 'letter');
$pdf->AliasNbPages(); // Habilitar el número de páginas
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetTitle("Ventas");

// Obtener datos de la empresa
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datosEmpresa = mysqli_fetch_assoc($config);
$pdf->datosEmpresa = $datosEmpresa;

// Obtener datos del cliente
$idcliente = $_GET['cl'];
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosCliente = mysqli_fetch_assoc($clientes);
$pdf->datosCliente = $datosCliente;

// Obtener la fecha de la venta
$id = $_GET['v'];
$venta = mysqli_query($conexion, "SELECT fecha FROM ventas WHERE id = $id");
$fechaVenta = mysqli_fetch_assoc($venta)['fecha'];
$pdf->datosFecha = $fechaVenta;

// Obtener detalles de la venta
$ventas = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion 
                                   FROM detalle_venta d 
                                   INNER JOIN producto p ON d.id_producto = p.codproducto 
                                   WHERE d.id_venta = $id");
$detallesVenta = [];
$totalVenta = 0;
$tipo_pago = ''; // Variable para almacenar el tipo de pago

while ($row = mysqli_fetch_assoc($ventas)) {
    $detallesVenta[] = $row;
    $totalVenta += $row['cantidad'] * $row['precio'];

    // Capturar el tipo de pago (solo una vez, ya que es el mismo para todos los detalles)
    if (empty($tipo_pago)) {
        $tipo_pago = $row['tipo_pago'];
    }
}
$pdf->detallesVenta = $detallesVenta;

// Encabezado de los detalles del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Datos del Cliente'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Identificacion: ' . $datosCliente['identificacion']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Nombre: ' . $datosCliente['nombre']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Apellido: ' . $datosCliente['apellido']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Teléfono: ' . $datosCliente['telefono']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Dirección: ' . $datosCliente['direccion']), 0, 1, 'L');
$pdf->Ln(10);

// Mostrar el tipo de pago
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Tipo de Pago'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Método de pago: ' . $tipo_pago), 0, 1, 'L');
$pdf->Ln(10); // Espacio antes de la tabla de productos

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Fecha de venta'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Fecha: ' . $fechaVenta), 0, 1, 'L');
$pdf->Ln(10); // Espacio antes de la tabla de productos


// Encabezado de la tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Detalle de la Venta'), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(59, 89, 152); // Color de fondo del encabezado
$pdf->SetTextColor(255, 255, 255); // Color del texto del encabezado
$pdf->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', true);
$pdf->Cell(100, 10, utf8_decode('Descripción'), 1, 0, 'L', true);
$pdf->Cell(25, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Precio', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Sub Total', 1, 1, 'C', true);

// Detalles de los productos
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0); // Restaurar color del texto
$contador = 1;

foreach ($detallesVenta as $row) {
    $pdf->Cell(10, 10, $contador, 1, 0, 'C');
    $pdf->Cell(100, 10, utf8_decode($row['descripcion']), 1, 0, 'L');
    $pdf->Cell(25, 10, $row['cantidad'], 1, 0, 'C');
    $pdf->Cell(25, 10, '$' . number_format($row['precio'], 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($row['cantidad'] * $row['precio'], 2, '.', ','), 1, 1, 'C');
    $contador++;
}

// Total de la venta
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, 'Total de la Venta:', 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($totalVenta, 2, '.', ','), 1, 1, 'C');

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode('Gracias por su compra.'), 0, 1, 'C');

// Generar el PDF
$pdf->Output("ventas.pdf", "I");
?>