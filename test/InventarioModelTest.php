<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/InventarioModel.php';
require_once __DIR__ . '/../config/database.php';

class InventarioModelTest extends TestCase {
    private $conexion;
    private $inventario;

    protected function setUp(): void {
        $this->conexion = getConnection();

        // Limpiar tablas relacionadas
        $this->conexion->query("DELETE FROM inventario");
        $this->conexion->query("DELETE FROM producto");
        $this->conexion->query("DELETE FROM proveedores");
        $this->conexion->query("DELETE FROM usuario");
        $this->conexion->query("DELETE FROM detalle_permisos");
        $this->conexion->query("DELETE FROM permisos");

        $this->inventario = new InventarioModel();
    }

    public function testVerificarPermisos() {
        // Insertar usuario
        $this->conexion->query("INSERT INTO usuario (nombre) VALUES ('Usuario Test')");
        $usuarioId = $this->conexion->insert_id;

        // Insertar permiso
        $this->conexion->query("INSERT INTO permisos (nombre) VALUES ('inventario')");
        $permisoId = $this->conexion->insert_id;

        // Asignar permiso al usuario
        $this->conexion->query("INSERT INTO detalle_permisos (id_usuario, id_permiso) VALUES ($usuarioId, $permisoId)");

        $result = $this->inventario->verificarPermisos($usuarioId, 'inventario');
        $this->assertCount(1, $result);
        $this->assertEquals('inventario', $result[0]['nombre']);
    }

    public function testVerificarProductoYProveedor() {
        // Insertar proveedor
        $this->conexion->query("INSERT INTO proveedores (nombre, estado) VALUES ('Proveedor Test', 1)");
        $proveedorId = $this->conexion->insert_id;

        // Insertar producto
        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $productoId = $this->conexion->insert_id;

        // Verificar producto
        $prod = $this->inventario->verificarProducto($productoId);
        $this->assertEquals('Producto Test', $prod['descripcion']);

        // Verificar proveedor
        $prov = $this->inventario->verificarProveedor($proveedorId);
        $this->assertEquals('Proveedor Test', $prov['nombre']);
    }

    public function testInsertarYActualizarInventario() {
        // Insertar proveedor y producto
        $this->conexion->query("INSERT INTO proveedores (nombre, estado) VALUES ('Proveedor Test', 1)");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $productoId = $this->conexion->insert_id;

        // Insertar inventario
        $insert = $this->inventario->insertarInventario($productoId, $proveedorId, 5, 1);
        $this->assertTrue($insert);

        // Obtener idinventario
        $inv = $this->inventario->verificarInventario($productoId, $proveedorId);
        $this->assertEquals(5, $inv['cantidad']);

        // Actualizar cantidad
        $update = $this->inventario->actualizarCantidadInventario($inv['idinventario'], 10);
        $this->assertTrue($update);

        $invActualizado = $this->inventario->obtenerInventarioPorId($inv['idinventario']);
        $this->assertEquals(10, $invActualizado['cantidad']);
    }

    public function testObtenerInventarioYProveedores() {
        // Insertar proveedor y producto
        $this->conexion->query("INSERT INTO proveedores (nombre, estado) VALUES ('Proveedor Test', 1)");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $productoId = $this->conexion->insert_id;

        // Insertar inventario
        $this->inventario->insertarInventario($productoId, $proveedorId, 5, 1);

        // Obtener inventario
        $inv = $this->inventario->obtenerInventario();
        $this->assertEquals(1, mysqli_num_rows($inv));

        // Obtener proveedores
        $prov = $this->inventario->obtenerProveedores();
        $this->assertEquals(1, mysqli_num_rows($prov));
    }

    public function testEliminarInventarioYVerificarProductoInactivo() {
        // Insertar proveedor y producto
        $this->conexion->query("INSERT INTO proveedores (nombre, estado) VALUES ('Proveedor Test', 1)");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $productoId = $this->conexion->insert_id;

        // Insertar inventario
        $this->inventario->insertarInventario($productoId, $proveedorId, 5, 1);
        $inv = $this->inventario->verificarInventario($productoId, $proveedorId);
        $idInventario = $inv['idinventario'];

        // Eliminar inventario
        $del = $this->inventario->eliminarInventario($idInventario);
        $this->assertTrue($del);

        // Verificar producto inactivo
        $estado = $this->inventario->verificarProductoInactivo($idInventario);
        $this->assertEquals(1, $estado); // Si la tabla producto sigue activa, devuelve 1
    }
}
