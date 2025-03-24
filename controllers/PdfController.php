<?php
// Controller/PdfController.php
require_once 'Model/PdfModel.php';

class PdfController {
    private $model;
    
    public function __construct() {
        $this->model = new PdfModel();
    }
    
    public function generarPdf($params = []) {
        // Validar parámetros
        if (!isset($params['cl']) || !isset($params['v']) || 
            $params['cl'] === 'undefined' || $params['v'] === 'undefined') {
            die('Parámetros incorrectos para generar el PDF');
        }
        
        $clienteId = $params['cl'];
        $ventaId = $params['v'];
        
        // Obtener datos
        $datosEmpresa = $this->model->obtenerDatosEmpresa();
        $datosCliente = $this->model->obtenerDatosCliente($clienteId);
        $fechaVenta = $this->model->obtenerFechaVenta($ventaId);
        $detallesVenta = $this->model->obtenerDetallesVenta($ventaId);
        
        // Calcular total
        $totalVenta = 0;
        $tipo_pago = '';
        foreach ($detallesVenta as $row) {
            $totalVenta += $row['cantidad'] * $row['precio'];
            if (empty($tipo_pago)) {
                $tipo_pago = $row['tipo_pago'];
            }
        }
        
        // Crear PDF
        $pdf = new PDF('P', 'mm', 'letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Ventas");
        
        // Asignar datos al PDF
        $pdf->datosEmpresa = $datosEmpresa;
        $pdf->datosCliente = $datosCliente;
        $pdf->datosFecha = $fechaVenta;
        $pdf->detallesVenta = $detallesVenta;
        
        // Generar contenido del PDF
        $this->generarContenido($pdf, $datosCliente, $fechaVenta, $tipo_pago, $detallesVenta, $totalVenta);
        
        // Salida del PDF
        $pdf->Output("ventas.pdf", "I");
        exit; // Importante para evitar que el enrutador siga procesando
    }
    
    
    private function generarContenido($pdf, $datosCliente, $fechaVenta, $tipo_pago, $detallesVenta, $totalVenta) {
        // Datos del cliente
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Datos del Cliente'), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Identificacion: ' . $datosCliente['identificacion']), 0, 1, 'L');
        $pdf->Cell(0, 5, utf8_decode('Nombre: ' . $datosCliente['nombre']), 0, 1, 'L');
        $pdf->Cell(0, 5, utf8_decode('Apellido: ' . $datosCliente['apellido']), 0, 1, 'L');
        $pdf->Cell(0, 5, utf8_decode('Teléfono: ' . $datosCliente['telefono']), 0, 1, 'L');
        $pdf->Cell(0, 5, utf8_decode('Dirección: ' . $datosCliente['direccion']), 0, 1, 'L');
        $pdf->Ln(10);
        
        // Tipo de pago
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Tipo de Pago'), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Método de pago: ' . $tipo_pago), 0, 1, 'L');
        $pdf->Ln(10);
        
        // Fecha de venta
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Fecha de venta'), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, utf8_decode('Fecha: ' . $fechaVenta), 0, 1, 'L');
        $pdf->Ln(10);
        
        // Tabla de productos
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Detalle de la Venta'), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(59, 89, 152);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C', true);
        $pdf->Cell(100, 10, utf8_decode('Descripción'), 1, 0, 'L', true);
        $pdf->Cell(25, 10, 'Cantidad', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Precio', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Sub Total', 1, 1, 'C', true);
        
        // Detalles de productos
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $contador = 1;
        foreach ($detallesVenta as $row) {
            $pdf->Cell(10, 10, $contador, 1, 0, 'C');
            $pdf->Cell(100, 10, utf8_decode($row['descripcion']), 1, 0, 'L');
            $pdf->Cell(25, 10, $row['cantidad'], 1, 0, 'C');
            $pdf->Cell(25, 10, '$' . number_format($row['precio'], 2, '.', ','), 1, 0, 'C');
            $pdf->Cell(30, 10, '$' . number_format($row['cantidad'] * $row['precio'], 2, '.', ','), 1, 1, 'C');
            $contador++;
        }
        
        // Total
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(160, 10, 'Total de la Venta:', 1, 0, 'R');
        $pdf->Cell(30, 10, '$' . number_format($totalVenta, 2, '.', ','), 1, 1, 'C');
        
        // Pie de página
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, utf8_decode('Gracias por su compra.'), 0, 1, 'C');
    }
}

// Punto de entrada (generar.php)
require_once 'Controller/PdfController.php';

if (isset($_GET['cl']) && isset($_GET['v'])) {
    $clienteId = $_GET['cl'];
    $ventaId = $_GET['v'];
    
    $controller = new PdfController($conexion);
    $controller->generarPdf($clienteId, $ventaId);
} else {
    die('Parámetros incorrectos');
}
?>