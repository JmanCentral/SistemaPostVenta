<?php
use PHPUnit\Framework\TestCase;

require_once 'models/PdfModel.php';
require_once 'config/database.php';

class PdfModelTest extends TestCase {
    private $pdfModel;

    protected function setUp(): void {
        $this->pdfModel = new PdfModel();
    }

    public function testObtenerDatosEmpresa() {
        $datos = $this->pdfModel->obtenerDatosEmpresa();
        $this->assertIsArray($datos, "Los datos de la empresa deberían ser un array");
        
        // Verificar que al menos tenga algunas claves esperadas
        if (!empty($datos)) {
            $this->assertArrayHasKey('nombre', $datos);
            $this->assertArrayHasKey('telefono', $datos);
        }
    }

    public function testObtenerDatosCliente() {
        // Usar un ID que probablemente exista o verificar si está vacío
        $datos = $this->pdfModel->obtenerDatosCliente(1);
        $this->assertIsArray($datos, "Los datos del cliente deberían ser un array");
        
        if (!empty($datos)) {
            // Verificar las claves reales de tu tabla cliente
            $this->assertArrayHasKey('idcliente', $datos);
            $this->assertArrayHasKey('nombre', $datos);
            $this->assertArrayHasKey('apellido', $datos);
        }
    }

    public function testObtenerFechaVenta() {
        // Usar un ID que probablemente exista
        $fecha = $this->pdfModel->obtenerFechaVenta(1);
        $this->assertIsString($fecha, "La fecha debería ser un string");
        
        if (!empty($fecha)) {
            $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $fecha);
        }
    }

    public function testObtenerDetallesVenta() {
        // Usar un ID que probablemente exista
        $detalles = $this->pdfModel->obtenerDetallesVenta(1);
        $this->assertIsArray($detalles, "Los detalles de la venta deberían ser un array");
        
        if (!empty($detalles)) {
            $this->assertArrayHasKey('id_producto', $detalles[0]);
            $this->assertArrayHasKey('codproducto', $detalles[0]);
            $this->assertArrayHasKey('descripcion', $detalles[0]);
        }
    }

    protected function tearDown(): void {
        // Limpiar si es necesario
    }
}