<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/ClienteModel.php';

class ClienteModelTest extends TestCase {
    private $conexion;
    private $clienteModel;

    protected function setUp(): void {
        putenv('APP_ENV=test');
        $this->conexion = new mysqli("localhost", "root", "", "puntoventa");
        $this->conexion->query("DELETE FROM cliente");
        $this->clienteModel = new ClienteModel($this->conexion); // usar misma conexión
    }

    public function testInsertarClienteValido() {
        $idInsertado = $this->clienteModel->insertarCliente(
            "Oscar", "Pacheco", "123456", "3000000000", "Calle 123", "oscar@test.com", 1
        );
        $this->assertIsInt($idInsertado);

        $cliente = $this->clienteModel->obtenerClientePorId($idInsertado);
        $this->assertEquals("Oscar", $cliente['nombre']);
    }

    public function testActualizarCliente() {
        $idInsertado = $this->clienteModel->insertarCliente(
            "Oscar", "Pacheco", "123456", "3000000000", "Calle 123", "oscar@test.com", 1
        );

        $resultado = $this->clienteModel->actualizarCliente(
            $idInsertado, "Oscar Editado", "Pacheco", "123456", "3000000000", "Calle 456", "nuevo@test.com"
        );
        $this->assertTrue($resultado);

        $clienteActualizado = $this->clienteModel->obtenerClientePorId($idInsertado);
        $this->assertEquals("Oscar Editado", $clienteActualizado['nombre']);
    }

    public function testEliminarCliente() {
        $idInsertado = $this->clienteModel->insertarCliente(
            "Oscar", "Pacheco", "123456", "3000000000", "Calle 123", "oscar@test.com", 1
        );

        $resultado = $this->clienteModel->eliminarCliente($idInsertado);
        $this->assertTrue($resultado);

        $inactivos = $this->clienteModel->obtenerClientesInactivos();
        $this->assertGreaterThan(0, $inactivos->num_rows);
    }

    public function testObtenerClientePorId() {
        $idInsertado = $this->clienteModel->insertarCliente(
            "Ana", "Lopez", "654321", "3111111111", "Carrera 10", "ana@test.com", 1
        );

        $cliente = $this->clienteModel->obtenerClientePorId($idInsertado);
        $this->assertNotNull($cliente);
        $this->assertEquals("Ana", $cliente['nombre']);
    }

    public function testObtenerClientes() {
        $this->clienteModel->insertarCliente(
            "Juan", "Perez", "111111", "3222222222", "Calle 50", "juan@test.com", 1
        );
        $this->clienteModel->insertarCliente(
            "Maria", "Diaz", "222222", "3333333333", "Calle 60", "maria@test.com", 1
        );

        $clientes = $this->clienteModel->obtenerClientes();
        $this->assertGreaterThanOrEqual(2, $clientes->num_rows);
    }

    public function testObtenerClientesInactivos() {
        $idInsertado = $this->clienteModel->insertarCliente(
            "Carlos", "Ramirez", "333333", "3000000000", "Calle 70", "carlos@test.com", 1
        );

        $this->clienteModel->eliminarCliente($idInsertado);

        $inactivos = $this->clienteModel->obtenerClientesInactivos();
        $this->assertGreaterThan(0, $inactivos->num_rows);
    }
}
