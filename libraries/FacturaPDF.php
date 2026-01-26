<?php
require_once 'fpdf/fpdf.php';

class FacturaPDF extends FPDF {
    public $datosEmpresa;
    public $datosCliente;
    public $detallesVenta;
    public $fechaVenta;
    public $tipoPago;
    public $totalVenta;
    public $idVenta;
    
    function Header() {
        // Número de venta a la izquierda
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(60, 10, $this->encodeText('N° de Venta: ' . $this->idVenta), 0, 0, 'L');
        
        // Título principal
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->encodeText('Reporte de Venta'), 0, 1, 'C');
        
        // --- CONTENIDO SOLO PARA LA PRIMERA PÁGINA ---
        if ($this->PageNo() == 1) {
            // Logo adicional solicitado (puedes ajustar las coordenadas aquí)
            $this->Image("assets/img/logito.png", 90, 30, 100, 100, 'PNG');
            $this->Ln(20); // Espacio extra para el logo

            // Datos de la empresa
            $this->dibujarDatosEmpresa();
            
            // Datos del cliente
            $this->dibujarDatosCliente();
            
            // Tipo de pago
            $this->dibujarTipoPago();
            
            // Fecha de venta
            $this->dibujarFechaVenta();

        } else { // --- CONTENIDO PARA PÁGINAS SIGUIENTES ---
            $this->Ln(20); // Espacio para separar del encabezado principal
        }
        
        // --- CONTENIDO PARA TODAS LAS PÁGINAS ---
        // Encabezado de la tabla de productos
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Detalle de la Venta'), 0, 1, 'L');
        $this->SetFillColor(59, 89, 152);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(10, 10, '#', 1, 0, 'C', true);
        $this->Cell(100, 10, $this->encodeText('Descripción'), 1, 0, 'L', true);
        $this->Cell(25, 10, $this->encodeText('Cantidad'), 1, 0, 'C', true);
        $this->Cell(25, 10, $this->encodeText('Precio'), 1, 0, 'C', true);
        $this->Cell(30, 10, $this->encodeText('Subtotal'), 1, 1, 'C', true);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        // Usamos {nb} para el número total de páginas, que se reemplaza al final
        $this->Cell(0, 10, $this->encodeText('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
    
    protected function encodeText($text) {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
    
    public function generarContenido() {
        $this->AddPage();
        $this->AliasNbPages(); // Para el número total de páginas

        // El contenido principal ahora es solo la tabla y los totales
        $this->tablaProductos();
        $this->totalVenta();
        $this->dibujarMensajeFinal();
    }
    
    private function dibujarDatosEmpresa() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Datos de la empresa'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->encodeText('Nombre: ' . ($this->datosEmpresa['nombre'] ?? 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('NIT: ' . ($this->datosEmpresa['NIT'] ?? 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Teléfono: ' . ($this->datosEmpresa['telefono'] ?? 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Dirección: ' . ($this->datosEmpresa['direccion'] ?? 'No disponible')), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Correo: ' . ($this->datosEmpresa['email'] ?? 'No disponible')), 0, 1, 'L');
        $this->Ln(10);
    }

    private function dibujarDatosCliente() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Datos del Cliente'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->encodeText('Identificación: ' . $this->datosCliente['identificacion']), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Nombre: ' . $this->datosCliente['nombre']), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Apellido: ' . $this->datosCliente['apellido']), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Teléfono: ' . $this->datosCliente['telefono']), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Dirección: ' . $this->datosCliente['direccion']), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Correo: ' . $this->datosCliente['email']), 0, 1, 'L');
        $this->Ln(10);
    }
    
    private function dibujarTipoPago() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Tipo de Pago'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->encodeText('Método: ' . $this->tipoPago), 0, 1, 'L');
        $this->Ln(10);
    }
    
    private function dibujarFechaVenta() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Fecha de Generación de la Venta'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);

        $fechaObj = new DateTime($this->fechaVenta);
        $fechaFormateada = $fechaObj->format('d/m/Y');
        $horaFormateada = $fechaObj->format('h:i:s A');

        $this->Cell(0, 5, $this->encodeText('Fecha: ' . $fechaFormateada), 0, 1, 'L');
        $this->Cell(0, 5, $this->encodeText('Hora realizada: ' . $horaFormateada), 0, 1, 'L');
        $this->Ln(10);
    }
    
    private function tablaProductos() {
        // El encabezado de la tabla ahora está en el método Header()
        // Aquí solo dibujamos las filas de productos
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $contador = 1;
        foreach ($this->detallesVenta as $row) {
            $this->Cell(10, 10, $contador, 1, 0, 'C');
            $this->Cell(100, 10, $this->encodeText($row['descripcion']), 1, 0, 'L');
            $this->Cell(25, 10, $row['cantidad'], 1, 0, 'C');
            $this->Cell(25, 10, '$'.number_format($row['precio'], 2, '.', ','), 1, 0, 'C');
            $this->Cell(30, 10, '$'.number_format($row['cantidad'] * $row['precio'], 2, '.', ','), 1, 1, 'C');
            $contador++;
        }
    }
    
    private function totalVenta() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(160, 10, $this->encodeText('Total de la Venta:'), 1, 0, 'R');
        $this->Cell(30, 10, '$'.number_format($this->totalVenta, 2, '.', ','), 1, 1, 'C');
    }
    
    private function dibujarMensajeFinal() {
        $this->Ln(10);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, $this->encodeText('Gracias por su compra.'), 0, 1, 'C');
    }
}