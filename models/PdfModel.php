<?php

require_once 'config/database.php'; 
require_once 'models/fpdf/fpdf.php';

class PdfModel {
    private $conexion;
    
    public function __construct() {
        $this->conexion = getConnection(); 
    }
    
    public function obtenerDatosEmpresa() {
        $query = "SELECT * FROM configuracion";
        $result = mysqli_query($this->conexion, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function obtenerDatosCliente($idCliente) {
        $query = "SELECT * FROM cliente WHERE idcliente = $idCliente";
        $result = mysqli_query($this->conexion, $query);
        return mysqli_fetch_assoc($result);
    }
    
    public function obtenerFechaVenta($idVenta) {
        $query = "SELECT fecha FROM ventas WHERE id = $idVenta";
        $result = mysqli_query($this->conexion, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['fecha'];
    }
    
    public function obtenerDetallesVenta($idVenta) {
        $query = "SELECT d.*, p.codproducto, p.descripcion 
                 FROM detalle_venta d 
                 INNER JOIN producto p ON d.id_producto = p.codproducto 
                 WHERE d.id_venta = $idVenta";
        $result = mysqli_query($this->conexion, $query);
        
        $detalles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $detalles[] = $row;
        }
        return $detalles;
    }
}

// Clase personalizada para el PDF
class PDF extends FPDF {
    function Header() {
        $this->Image("../../assets/img/logo.png", 10, 10, 30, 30, 'PNG');
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Reporte de Venta'), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        if (!empty($this->datosEmpresa)) {
            $this->Cell(0, 5, utf8_decode('Nombre de la Empresa: ' . ($this->datosEmpresa['nombre'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Teléfono: ' . ($this->datosEmpresa['telefono'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Dirección: ' . ($this->datosEmpresa['direccion'] ?? 'No disponible')), 0, 1, 'L');
            $this->Cell(0, 5, utf8_decode('Correo: ' . ($this->datosEmpresa['email'] ?? 'No disponible')), 0, 1, 'L');
        }

        if (!empty($this->datosFecha)) {
            $this->Cell(0, 5, utf8_decode('Fecha de Venta: ' . $this->datosFecha), 0, 1, 'L');
        }
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }

    public $datosEmpresa;
    public $datosFecha;
    public $datosCliente;
    public $detallesVenta;
}
?>