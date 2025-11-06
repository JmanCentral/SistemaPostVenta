<?php
use PHPUnit\Framework\TestCase;

require_once 'models/VentaModel.php';
require_once 'config/database.php';

class VentaModelTest extends TestCase {
    private $ventaModel;
    private $conexion;
    private $testUserId;
    private $testProductId;
    private $testClientId;
    private $testVentaId;

    protected function setUp(): void {
        $this->ventaModel = new VentaModel();
        $this->conexion = getConnection();
        $this->testUserId = 1; // Usuario admin por defecto
        
        // Crear datos de prueba
        $this->testClientId = $this->createTestClient();
        $this->testProductId = $this->createTestProduct();
        $this->testVentaId = $this->createTestVenta();
        
        // Asignar permisos si es necesario
        $this->assignTestPermissions();
    }

    protected function tearDown(): void {
        // Limpiar datos de prueba
        if ($this->testClientId) {
            mysqli_query($this->conexion, "DELETE FROM cliente WHERE idcliente = $this->testClientId");
        }
        if ($this->testProductId) {
            mysqli_query($this->conexion, "DELETE FROM producto WHERE codproducto = $this->testProductId");
            mysqli_query($this->conexion, "DELETE FROM inventario WHERE codproducto = $this->testProductId");
        }
        if ($this->testVentaId) {
            mysqli_query($this->conexion, "DELETE FROM ventas WHERE id = $this->testVentaId");
            mysqli_query($this->conexion, "DELETE FROM detalle_venta WHERE id_venta = $this->testVentaId");
        }
        
        // Limpiar temporal
        mysqli_query($this->conexion, "DELETE FROM detalle_temp WHERE id_usuario = $this->testUserId");
    }

    private function createTestClient() {
        $identificacion = 'TEST_' . uniqid();
        $nombre = 'Cliente Test';
        $apellido = 'Apellido Test';
        $direccion = 'Dirección Test';
        $telefono = '123456789';
        $email = 'test@example.com';
        
        $query = "INSERT INTO cliente (identificacion, nombre, apellido, direccion, telefono, email, estado) 
                 VALUES ('$identificacion', '$nombre', '$apellido', '$direccion', '$telefono', '$email', 1)";
        
        $result = mysqli_query($this->conexion, $query);
        return $result ? mysqli_insert_id($this->conexion) : null;
    }

    private function createTestProduct() {
        $codigo = 'TEST_' . uniqid();
        $descripcion = 'Producto Test';
        $precio_compra = 10.50;
        $precio_venta = 15.99;
        
        $query = "INSERT INTO producto (codigo, descripcion, precio_compra, precio_venta, usuario_id, estado) 
                 VALUES ('$codigo', '$descripcion', $precio_compra, $precio_venta, $this->testUserId, 1)";
        
        $result = mysqli_query($this->conexion, $query);
        
        if ($result) {
            $productId = mysqli_insert_id($this->conexion);
            // Agregar inventario
            mysqli_query($this->conexion, "INSERT INTO inventario (codproducto, cantidad, estado) 
                                         VALUES ($productId, 100, 1)");
            return $productId;
        }
        
        return null;
    }

    private function createTestVenta() {
        if (!$this->testClientId || !$this->testProductId) {
            return null;
        }
        
        $total = 15.99;
        $fecha = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO ventas (id_cliente, total, id_usuario, fecha) 
                 VALUES ($this->testClientId, $total, $this->testUserId, '$fecha')";
        
        $result = mysqli_query($this->conexion, $query);
        
        if ($result) {
            $ventaId = mysqli_insert_id($this->conexion);
            
            // Agregar detalle de venta
            $queryDetalle = "INSERT INTO detalle_venta (id_producto, id_venta, cantidad, tipo_pago, precio, precio_compra_historico, precio_venta_historico) 
                           VALUES ($this->testProductId, $ventaId, 1, 'contado', 15.99, 10.50, 15.99)";
            
            mysqli_query($this->conexion, $queryDetalle);
            return $ventaId;
        }
        
        return null;
    }

    private function assignTestPermissions() {
        // Verificar si existe el permiso de ventas
        $result = mysqli_query($this->conexion, "SELECT id FROM permisos WHERE nombre = 'ventas'");
        if (mysqli_num_rows($result) == 0) {
            mysqli_query($this->conexion, "INSERT INTO permisos (nombre) VALUES ('ventas')");
            $permisoId = mysqli_insert_id($this->conexion);
        } else {
            $row = mysqli_fetch_assoc($result);
            $permisoId = $row['id'];
        }
        
        // Asignar permiso al usuario
        $check = mysqli_query($this->conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $this->testUserId AND id_permiso = $permisoId");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($this->conexion, "INSERT INTO detalle_permisos (id_permiso, id_usuario) VALUES ($permisoId, $this->testUserId)");
        }
    }

    public function testVerificarPermisos() {
        $permisos = $this->ventaModel->verificarPermisos($this->testUserId, 'ventas');
        
        $this->assertIsArray($permisos);
        
        if (empty($permisos)) {
            $this->markTestSkipped("El usuario no tiene permisos de ventas");
            return;
        }
        
        $this->assertNotEmpty($permisos);
    }

    public function testBuscarCliente() {
        if (!$this->testClientId) {
            $this->markTestSkipped("Cliente de prueba no disponible");
            return;
        }
        
        // Obtener el cliente de prueba
        $cliente = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE idcliente = $this->testClientId");
        $clienteData = mysqli_fetch_assoc($cliente);
        
        $resultado = $this->ventaModel->buscarCliente($clienteData['identificacion']);
        
        $this->assertIsArray($resultado);
        $this->assertNotEmpty($resultado);
        $this->assertEquals($clienteData['idcliente'], $resultado[0]['id']);
    }

    public function testBuscarProducto() {
        if (!$this->testProductId) {
            $this->markTestSkipped("Producto de prueba no disponible");
            return;
        }
        
        // Obtener el producto de prueba
        $producto = mysqli_query($this->conexion, "SELECT * FROM producto WHERE codproducto = $this->testProductId");
        $productoData = mysqli_fetch_assoc($producto);
        
        $resultado = $this->ventaModel->buscarProducto($productoData['codigo']);
        
        $this->assertIsArray($resultado);
        $this->assertNotEmpty($resultado);
        $this->assertEquals($productoData['codproducto'], $resultado[0]['id']);
    }

    public function testAgregarProductoTemp() {
        if (!$this->testProductId) {
            $this->markTestSkipped("Producto de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->agregarProductoTemp($this->testProductId, 2, 15.99, $this->testUserId);
        
        $this->assertContains($resultado, ['registrado', 'actualizado']);
        
        // Verificar que se agregó al temporal
        $temp = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $this->testUserId");
        $this->assertEquals(1, mysqli_num_rows($temp));
    }

    public function testObtenerDetalleTemp() {
        // Primero agregar un producto al temporal
        if (!$this->testProductId) {
            $this->markTestSkipped("Producto de prueba no disponible");
            return;
        }
        
        $this->ventaModel->agregarProductoTemp($this->testProductId, 1, 15.99, $this->testUserId);
        
        $resultado = $this->ventaModel->obtenerDetalleTemp($this->testUserId);
        
        $this->assertIsArray($resultado);
        $this->assertNotEmpty($resultado);
        $this->assertArrayHasKey('descripcion', $resultado[0]);
        $this->assertArrayHasKey('cantidad', $resultado[0]);
        $this->assertArrayHasKey('precio_venta', $resultado[0]);
    }

    public function testEliminarDetalleTemp() {
        if (!$this->testProductId) {
            $this->markTestSkipped("Producto de prueba no disponible");
            return;
        }
        
        // Agregar producto al temporal
        $this->ventaModel->agregarProductoTemp($this->testProductId, 3, 15.99, $this->testUserId);
        
        // Obtener el ID del detalle temporal
        $temp = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $this->testUserId");
        $tempData = mysqli_fetch_assoc($temp);
        
        $resultado = $this->ventaModel->eliminarDetalleTemp($tempData['id'], 1);
        $resultadoArray = json_decode($resultado, true);
        
        $this->assertIsArray($resultadoArray);
        $this->assertArrayHasKey('estado', $resultadoArray);
    }

    public function testProcesarVenta() {
        if (!$this->testClientId || !$this->testProductId) {
            $this->markTestSkipped("Datos de prueba incompletos");
            return;
        }
        
        // Agregar producto al temporal
        $this->ventaModel->agregarProductoTemp($this->testProductId, 2, 15.99, $this->testUserId);
        
        $fecha = date('Y-m-d H:i:s');
        $resultado = $this->ventaModel->procesarVenta($this->testClientId, $this->testUserId, 'contado', $fecha);
        
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('id_cliente', $resultado);
        $this->assertArrayHasKey('id_venta', $resultado);
        
        // Verificar que se creó la venta
        $venta = mysqli_query($this->conexion, "SELECT * FROM ventas WHERE id = {$resultado['id_venta']}");
        $this->assertEquals(1, mysqli_num_rows($venta));
        
        // Verificar que se limpió el temporal
        $temp = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $this->testUserId");
        $this->assertEquals(0, mysqli_num_rows($temp));
    }

    public function testObtenerGanancias() {
        if (!$this->testVentaId) {
            $this->markTestSkipped("Venta de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->obtenerGanancias();
        
        $this->assertIsArray($resultado);
        // Puede estar vacío si no hay ventas con ganancias
    }

    public function testObtenerDatosEmpresa() {
        $resultado = $this->ventaModel->obtenerDatosEmpresa();
        
        $this->assertIsArray($resultado);
        // Puede estar vacío si no hay configuración
    }

    public function testObtenerDatosCliente() {
        if (!$this->testClientId) {
            $this->markTestSkipped("Cliente de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->obtenerDatosCliente($this->testClientId);
        
        $this->assertIsArray($resultado);
        $this->assertEquals($this->testClientId, $resultado['idcliente']);
    }

    public function testObtenerFechaVenta() {
        if (!$this->testVentaId) {
            $this->markTestSkipped("Venta de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->obtenerFechaVenta($this->testVentaId);
        
        $this->assertIsString($resultado);
    }

    public function testObtenerDetallesVenta() {
        if (!$this->testVentaId) {
            $this->markTestSkipped("Venta de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->obtenerDetallesVenta($this->testVentaId);
        
        $this->assertIsArray($resultado);
    }

    public function testCalcularTotalVenta() {
        $detalles = [
            ['cantidad' => 2, 'precio' => 10.50],
            ['cantidad' => 1, 'precio' => 15.99]
        ];
        
        $resultado = $this->ventaModel->calcularTotalVenta($detalles);
        
        $this->assertEquals(36.99, $resultado);
    }

    public function testObtenerTipoPago() {
        if (!$this->testVentaId) {
            $this->markTestSkipped("Venta de prueba no disponible");
            return;
        }
        
        $resultado = $this->ventaModel->obtenerTipoPago($this->testVentaId);
        
        $this->assertIsString($resultado);
    }

    public function testCalcularGanancia() {
        $resultado = $this->ventaModel->calcularGanancia();
        
        $this->assertIsNumeric($resultado);
    }

    public function testCalcularCantidadesVendidas() {
        $resultado = $this->ventaModel->calcularCantidadesVendidas();
        
        $this->assertIsNumeric($resultado);
    }

    public function testCalcularPrecioCompra() {
        $resultado = $this->ventaModel->calcularPrecioCompra();
        
        $this->assertIsNumeric($resultado);
    }

    public function testCalcularPrecioVenta() {
        $resultado = $this->ventaModel->calcularPrecioVenta();
        
        $this->assertIsNumeric($resultado);
    }

    public function testCambiarClave() {
        // Este test requiere un usuario existente con contraseña conocida
        // Puede ser complejo de testear, se marca como skipped
        $this->markTestSkipped("Test de cambiar clave requiere setup específico");
    }
}