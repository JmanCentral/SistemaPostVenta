<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/FacturaModel.php';
require_once __DIR__ . '/../config/database.php';

class FacturaModelTest extends TestCase {
    private $conexion;
    private $factura;

    protected function setUp(): void {
        $this->conexion = getConnection();

        // Limpiar tablas relacionadas
        $this->conexion->query("DELETE FROM detalle_venta");
        $this->conexion->query("DELETE FROM ventas");
        $this->conexion->query("DELETE FROM inventario");
        $this->conexion->query("DELETE FROM cliente");
        $this->conexion->query("DELETE FROM detalle_permisos");
        $this->conexion->query("DELETE FROM permisos");
        $this->conexion->query("DELETE FROM usuario");

        $this->factura = new FacturaModel();
    }

    public function testVerificarPermisos() {
        // Insertar usuario
        $this->conexion->query("INSERT INTO usuario (nombre) VALUES ('Usuario Test')");
        $usuarioId = $this->conexion->insert_id;

        // Insertar permiso
        $this->conexion->query("INSERT INTO permisos (nombre) VALUES ('venta')");
        $permisoId = $this->conexion->insert_id;

        // Asignar permiso al usuario
        $this->conexion->query("INSERT INTO detalle_permisos (id_usuario, id_permiso) VALUES ($usuarioId, $permisoId)");

        $result = $this->factura->verificarPermisos($usuarioId, 'venta');
        $this->assertCount(1, $result);
        $this->assertEquals('venta', $result[0]['nombre']);
    }

    public function testGetVentas() {
        // Insertar cliente
        $this->conexion->query("INSERT INTO cliente (nombre) VALUES ('Cliente Test')");
        $clienteId = $this->conexion->insert_id;

        // Insertar venta
        $this->conexion->query("INSERT INTO ventas (total, fecha, id_cliente) VALUES (150, NOW(), $clienteId)");

        $result = $this->factura->getVentas();
        $this->assertCount(1, $result);
        $this->assertEquals('Cliente Test', $result[0]['nombre']);
        $this->assertEquals(150, $result[0]['total']);
    }

    public function testEliminarVenta() {
        // Insertar proveedor
        $this->conexion->query("INSERT INTO proveedores (nombre) VALUES ('Proveedor Test')");
        $proveedorId = $this->conexion->insert_id;

        // Insertar producto
        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $productoId = $this->conexion->insert_id;

        // Insertar inventario
        $this->conexion->query("INSERT INTO inventario (codproducto, cantidad, idproveedor) VALUES ($productoId, 10, $proveedorId)");

        // Insertar cliente
        $this->conexion->query("INSERT INTO cliente (nombre) VALUES ('Cliente Test')");
        $clienteId = $this->conexion->insert_id;

        // Insertar venta
        $this->conexion->query("INSERT INTO ventas (total, fecha, id_cliente) VALUES (100, NOW(), $clienteId)");
        $ventaId = $this->conexion->insert_id;

        // Insertar detalle_venta
        $this->conexion->query("INSERT INTO detalle_venta (id_producto, cantidad, id_venta) VALUES ($productoId, 3, $ventaId)");

        // Ejecutar eliminación
        $this->factura->EliminarVenta($ventaId);

        // Verificar que la venta y detalle_venta fueron eliminados
        $ventaCheck = $this->conexion->query("SELECT * FROM ventas WHERE id = $ventaId");
        $this->assertEquals(0, $ventaCheck->num_rows);

        $detalleCheck = $this->conexion->query("SELECT * FROM detalle_venta WHERE id_venta = $ventaId");
        $this->assertEquals(0, $detalleCheck->num_rows);

        // Verificar que el stock se restauró
        $inventarioCheck = $this->conexion->query("SELECT cantidad FROM inventario WHERE codproducto = $productoId AND idproveedor = $proveedorId");
        $row = $inventarioCheck->fetch_assoc();
        $this->assertEquals(13, $row['cantidad']); // 10 + 3 restaurados
    }
}
