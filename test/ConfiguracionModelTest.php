<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/ConfiguracionModel.php';

class ConfiguracionModelTest extends TestCase {
    private $conexion;
    private $configuracionModel;

    protected function setUp(): void {
        putenv('APP_ENV=test');
        $this->conexion = new mysqli("localhost", "root", "", "puntoventa");
        $this->conexion->query("DELETE FROM configuracion");
        $this->configuracionModel = new ConfiguracionModel($this->conexion);
    }

    public function testInsertarConfiguracion() {
        $idInsertado = $this->configuracionModel->insertarConfiguracion(
            "Mi Empresa", "900123456", "3100000000", "empresa@test.com", "Calle 123"
        );

        $this->assertIsInt($idInsertado);

        $config = $this->configuracionModel->obtenerConfiguracion();
        $this->assertEquals("Mi Empresa", $config['nombre']);
    }

    public function testActualizarConfiguracion() {
        $idInsertado = $this->configuracionModel->insertarConfiguracion(
            "Mi Empresa", "900123456", "3100000000", "empresa@test.com", "Calle 123"
        );

        $resultado = $this->configuracionModel->actualizarConfiguracion(
            $idInsertado, "Mi Empresa Editada", "900123456", "3111111111", "nuevo@test.com", "Calle 456"
        );

        $this->assertTrue($resultado);

        $config = $this->configuracionModel->obtenerConfiguracion();
        $this->assertEquals("Mi Empresa Editada", $config['nombre']);
        $this->assertEquals("3111111111", $config['telefono']);
    }

    public function testObtenerConfiguracionVacia() {
        $this->conexion->query("DELETE FROM configuracion");
        $config = $this->configuracionModel->obtenerConfiguracion();
        $this->assertNull($config);
    }
}
