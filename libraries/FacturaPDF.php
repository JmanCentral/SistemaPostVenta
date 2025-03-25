<?php
require_once 'fpdf/fpdf.php';

class FacturaPDF extends FPDF {
    public $datosEmpresa;
    public $datosCliente;
    public $detallesVenta;
    public $fechaVenta;
    public $tipoPago;
    public $totalVenta;
    
    function Header() {
        // Logo de la empresa (ajusta la ruta según tu estructura)
        
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->encodeText('Reporte de Venta'), 0, 1, 'C');
        
        // Datos empresa
        $this->SetFont('Arial', '', 10);
        if ($this->datosEmpresa) {
            
            $this->Cell(0, 10, $this->encodeText('Datos de la empresa'), 0, 1, 'L');
            $this->Cell(0, 5, $this->encodeText('Nombre: ' . ($this->datosEmpresa['nombre'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, $this->encodeText('Teléfono: ' . ($this->datosEmpresa['telefono'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, $this->encodeText('Dirección: ' . ($this->datosEmpresa['direccion'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, $this->encodeText('Correo: ' . ($this->datosEmpresa['email'] ?? 'No disponible')), 0, 1, 'L');
        }

        if (!empty($this->fechaVenta)) {
            $this->Cell(0, 5, $this->encodeText('Fecha de Venta: ' . $this->fechaVenta), 0, 1, 'L');
        }
        
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->encodeText('Página ') . $this->PageNo(), 0, 0, 'C');
    }
    
    protected function encodeText($text) {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
    
    public function generarContenido() {
        $this->AddPage();
        $this->AliasNbPages(); // Para el número total de páginas
        
        // Datos cliente
        $this->dibujarDatosCliente();
        
        // Tipo de pago
        $this->dibujarTipoPago();
        
        // Fecha de venta
        $this->dibujarFechaVenta();
        
        // Tabla de productos
        $this->tablaProductos();
        
        // Total
        $this->totalVenta();
        
        // Mensaje final
        $this->dibujarMensajeFinal();
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
        $this->Cell(0, 10, $this->encodeText('Fecha de Venta'), 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->encodeText('Fecha: ' . $this->fechaVenta), 0, 1, 'L');
        $this->Ln(10);
    }
    
    private function tablaProductos() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, $this->encodeText('Detalle de la Venta'), 0, 1, 'L');
        
        // Cabecera tabla
        $this->SetFillColor(59, 89, 152);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(10, 10, '#', 1, 0, 'C', true);
        $this->Cell(100, 10, $this->encodeText('Descripción'), 1, 0, 'L', true);
        $this->Cell(25, 10, $this->encodeText('Cantidad'), 1, 0, 'C', true);
        $this->Cell(25, 10, $this->encodeText('Precio'), 1, 0, 'C', true);
        $this->Cell(30, 10, $this->encodeText('Subtotal'), 1, 1, 'C', true);
        
        // Contenido tabla
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