<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/DashboardModel.php';
require_once __DIR__ . '/../config/database.php';

class DashboardModelTest extends TestCase {
    private $conexion;
    private $dashboard;

    protected function setUp(): void {
        $this->conexion = getConnection();

        // Limpiar todas las tablas relacionadas
        $this->conexion->query("DELETE FROM detalle_venta");
        $this->conexion->query("DELETE FROM ventas");
        $this->conexion->query("DELETE FROM inventario");
        $this->conexion->query("DELETE FROM producto");
        $this->conexion->query("DELETE FROM proveedores");
        $this->conexion->query("DELETE FROM cliente");
        $this->conexion->query("DELETE FROM usuario");

        $this->dashboard = new DashboardModel();
    }

    public function testGetTotalUsuarios() {
        $this->conexion->query("INSERT INTO usuario (nombre) VALUES ('Usuario Test')");
        $this->assertEquals(1, $this->dashboard->getTotalUsuarios());
    }

    public function testGetTotalClientes() {
        $this->conexion->query("INSERT INTO cliente (nombre) VALUES ('Cliente Test')");
        $this->assertEquals(1, $this->dashboard->getTotalClientes());
    }

    public function testGetTotalProductos() {
        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Test', 1)");
        $this->assertEquals(1, $this->dashboard->getTotalProductos());
    }

    public function testGetTotalVentas() {
        $this->conexion->query("INSERT INTO ventas (total, fecha) VALUES (100, NOW())");
        $this->assertEquals(1, $this->dashboard->getTotalVentas());
    }

    public function testGetTotalProveedores() {
        $this->conexion->query("INSERT INTO proveedores (nombre) VALUES ('Proveedor Test')");
        $this->assertEquals(1, $this->dashboard->getTotalProveedores());
    }

    public function testGetTotalInventario() {
        $this->conexion->query("INSERT INTO proveedores (nombre) VALUES ('Proveedor Inv')");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Producto Inv', 1)");
        $productoId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO inventario (codproducto, cantidad, idproveedor) VALUES ($productoId, 5, $proveedorId)");
        $this->assertEquals(1, $this->dashboard->getTotalInventario());
    }

    public function testGetProductosStockMinimo() {
        $this->conexion->query("INSERT INTO proveedores (nombre) VALUES ('Proveedor Min')");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Prod Bajo', 1)");
        $productoId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO inventario (codproducto, cantidad, idproveedor) VALUES ($productoId, 5, $proveedorId)");

        $result = $this->dashboard->getProductosStockMinimo();
        $this->assertCount(1, $result);
        $this->assertEquals('Prod Bajo', $result[0]['descripcion']);
    }

    public function testGetProductosMasVendidos() {
        // Insertar producto
        $this->conexion->query("INSERT INTO producto (descripcion, precio_compra, precio_venta, estado) VALUES ('Prod Vendido', 50, 100, 1)");
        $productoId = $this->conexion->insert_id;

        // Insertar venta
        $this->conexion->query("INSERT INTO ventas (total, fecha) VALUES (200, NOW())");
        $ventaId = $this->conexion->insert_id;

        // Insertar detalle_venta
        $this->conexion->query("INSERT INTO detalle_venta (id_producto, cantidad, id_venta) VALUES ($productoId, 2, $ventaId)");

        $result = $this->dashboard->getProductosMasVendidos();
        $this->assertCount(1, $result);
        $this->assertEquals('Prod Vendido', $result[0]['descripcion']);
    }

    public function testObtenerGananciaTotal() {
        $this->conexion->query("INSERT INTO producto (descripcion, precio_compra, precio_venta, estado) VALUES ('Prod Gan', 50, 100, 1)");
        $productoId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO ventas (total, fecha) VALUES (100, NOW())");
        $ventaId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO detalle_venta (id_producto, cantidad, id_venta) VALUES ($productoId, 2, $ventaId)");

        $result = $this->dashboard->obtenerGananciaTotal();
        $this->assertCount(1, $result);
        $this->assertEquals(100, $result[0]['ganancia']);
    }

    public function testObtenerProductosStockBajo() {
        $this->conexion->query("INSERT INTO proveedores (nombre) VALUES ('Proveedor SB')");
        $proveedorId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO producto (descripcion, estado) VALUES ('Prod SB', 1)");
        $productoId = $this->conexion->insert_id;

        $this->conexion->query("INSERT INTO inventario (codproducto, cantidad, idproveedor) VALUES ($productoId, 5, $proveedorId)");

        $result = $this->dashboard->obtenerProductosStockBajo();
        $this->assertCount(1, $result);
        $this->assertEquals('Prod SB', $result[0]['descripcion']);
    }
}
