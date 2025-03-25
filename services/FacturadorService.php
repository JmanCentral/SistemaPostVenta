<?php

require_once 'models/VentaModel.php';
require_once 'libraries/FacturaPDF.php';

class FacturadorService {
    private $pdf;
    private $ventaModel;
    
    public function __construct() {
        $this->pdf = new FacturaPDF('P', 'mm', 'letter');
        $this->ventaModel = new VentaModel();
    }
    
    public function generarFactura($idVenta, $idCliente) {
        // Obtener datos
        $datos = [
            'empresa' => $this->ventaModel->obtenerDatosEmpresa(),
            'cliente' => $this->ventaModel->obtenerDatosCliente($idCliente),
            'detalles' => $this->ventaModel->obtenerDetallesVenta($idVenta),
            'fecha' => $this->ventaModel->obtenerFechaVenta($idVenta),
            'tipoPago' => $this->ventaModel->obtenerTipoPago($idVenta)
        ];
        
        // Configurar PDF
        $this->pdf->datosEmpresa = $datos['empresa'];
        $this->pdf->datosCliente = $datos['cliente'];
        $this->pdf->detallesVenta = $datos['detalles'];
        $this->pdf->fechaVenta = $datos['fecha'];
        $this->pdf->tipoPago = $datos['tipoPago'];
        $this->pdf->totalVenta = $this->ventaModel->calcularTotalVenta($datos['detalles']);
        
        // Generar contenido
        $this->pdf->generarContenido();
        
        return $this->pdf;
    }
}