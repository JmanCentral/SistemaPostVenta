<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos

class VentaModel {
    private $conexion;

    public function __construct() {
        $this->conexion = getConnection(); // Obtiene la conexión a la base de datos
    }

    public function verificarPermisos($id_user, $permiso) {
        $sql = mysqli_query($this->conexion, "SELECT p.*, d.* FROM permisos p 
                                              INNER JOIN detalle_permisos d ON p.id = d.id_permiso 
                                              WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
        
        return ($sql) ? mysqli_fetch_all($sql, MYSQLI_ASSOC) : []; // Retorna un array asociativo
    }

    public function buscarCliente($identificacion) {
        $datos = array();
        $query = $this->conexion->query("SELECT * FROM cliente WHERE identificacion LIKE '%$identificacion%' AND estado = 1");
        while ($row = $query->fetch_assoc()) {
            $data = [
                'id' => $row['idcliente'],
                'label' => $row['identificacion'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'direccion' => $row['direccion'],
                'telefono' => $row['telefono'],
                'correo' => $row['email']
            ];
            array_push($datos, $data);
        }
        return $datos;
    }

    public function buscarProducto($nombre) {
        $datos = array();
        $query = $this->conexion->query("SELECT p.*, SUM(i.cantidad) AS stock 
                                       FROM producto p 
                                       LEFT JOIN inventario i ON p.codproducto = i.codproducto 
                                       WHERE (p.codigo LIKE '%$nombre%' OR p.descripcion LIKE '%$nombre%') AND p.estado = 1 
                                       GROUP BY p.codproducto");
        while ($row = $query->fetch_assoc()) {
            $data = [
                'id' => $row['codproducto'],
                'label' => $row['codigo'] . ' - ' . $row['descripcion'],
                'value' => $row['descripcion'],
                'precio' => $row['precio_venta'],
                'stock' => $row['stock'] ?? 0
            ];
            array_push($datos, $data);
        }
        return $datos;
    }

    public function obtenerDetalleTemp($id_usuario) {
        $datos = array();
        $query = $this->conexion->query("SELECT d.*, p.codproducto, p.descripcion 
                                        FROM detalle_temp d 
                                        INNER JOIN producto p ON d.id_producto = p.codproducto 
                                        WHERE d.id_usuario = $id_usuario");
        while ($row = $query->fetch_assoc()) {
            $data = [
                'id' => $row['id'],
                'descripcion' => $row['descripcion'],
                'cantidad' => $row['cantidad'],
                'precio_venta' => $row['precio_venta'],
                'sub_total' => number_format($row['precio_venta'] * $row['cantidad'], 2, '.', ',')
            ];
            array_push($datos, $data);
        }
        return $datos;
    }

    public function eliminarDetalleTemp($id_detalle, $cantidad_a_eliminar) {
        // Iniciar transacción
        $this->conexion->begin_transaction();
    
        try {
            // Obtener detalle actual con prepared statement
            $stmt = $this->conexion->prepare("SELECT cantidad FROM detalle_temp WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $id_detalle);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows == 0) {
                throw new Exception('Registro no encontrado');
            }
    
            $detalle = $result->fetch_assoc();
            $cantidad_actual = $detalle['cantidad'];
    
            // Validar cantidad
            if ($cantidad_a_eliminar > $cantidad_actual) {
                throw new Exception('Cantidad a eliminar excede el stock');
            }
    
            // Calcular nueva cantidad
            $nueva_cantidad = $cantidad_actual - $cantidad_a_eliminar;
    
            if ($nueva_cantidad > 0) {
                // Actualizar cantidad
                $stmt = $this->conexion->prepare("UPDATE detalle_temp SET cantidad = ? WHERE id = ?");
                $stmt->bind_param("ii", $nueva_cantidad, $id_detalle);
                $stmt->execute();
    
                $respuesta = [
                    'estado' => 'parcialmente_eliminado',
                    'cantidad_eliminada' => $cantidad_a_eliminar,
                    'cantidad_restante' => $nueva_cantidad
                ];
            } else {
                // Eliminar registro
                $stmt = $this->conexion->prepare("DELETE FROM detalle_temp WHERE id = ?");
                $stmt->bind_param("i", $id_detalle);
                $stmt->execute();
    
                $respuesta = [
                    'estado' => 'completamente_eliminado',
                    'cantidad_eliminada' => $cantidad_actual
                ];
            }
    
            $this->conexion->commit();
            return json_encode($respuesta);
    
        } catch (Exception $e) {
            $this->conexion->rollback();
            return json_encode([
                'estado' => 'error',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public function procesarVenta($id_cliente, $id_user, $tipo_pago, $fecha_venta) {
        // Obtener el total de la venta
        $consulta = $this->conexion->query("SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_user");
        $result = $consulta->fetch_assoc();
        $total = $result['total_pagar'];
            
        // Insertar la venta
        $insertar = $this->conexion->query("INSERT INTO ventas(id_cliente, total, id_usuario, fecha) VALUES ($id_cliente, '$total', $id_user, '$fecha_venta')");
        
        if (!$insertar) {
            return ['mensaje' => 'error'];
        }

        // Obtener el ID de la venta
        $id_maximo = $this->conexion->query("SELECT MAX(id) AS total FROM ventas");
        $resultId = $id_maximo->fetch_assoc();
        $ultimoId = $resultId['total'];

        // Procesar detalles
        $consultaDetalle = $this->conexion->query("SELECT * FROM detalle_temp WHERE id_usuario = $id_user");
        while ($row = $consultaDetalle->fetch_assoc()) {
            $id_producto = $row['id_producto'];
            $cantidad = $row['cantidad'];
            $precio = $row['precio_venta'];

            // Insertar detalle
            $this->conexion->query("INSERT INTO detalle_venta(id_producto, id_venta, cantidad, precio, tipo_pago) VALUES ($id_producto, $ultimoId, $cantidad, '$precio', '$tipo_pago')");

            // Actualizar inventario
            $query_inventario = $this->conexion->query("SELECT * FROM inventario WHERE codproducto = $id_producto ORDER BY cantidad DESC LIMIT 1");
            $inventario = $query_inventario->fetch_assoc();
            $nueva_cantidad = $inventario['cantidad'] - $cantidad;

            if ($nueva_cantidad >= 0) {
                $this->conexion->query("UPDATE inventario SET cantidad = $nueva_cantidad WHERE idinventario = {$inventario['idinventario']}");
            } else {
                return ['mensaje' => 'No hay suficiente stock para el producto ' . $id_producto];
            }
        }

        // Limpiar temporal
        $this->conexion->query("DELETE FROM detalle_temp WHERE id_usuario = $id_user");
        return ['id_cliente' => $id_cliente, 'id_venta' => $ultimoId];
    }

    public function agregarProductoTemp($id, $cant, $precio, $id_user) {
        $total = $precio * $cant;
        $verificar = $this->conexion->query("SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
        
        if ($verificar->num_rows > 0) {
            $datos = $verificar->fetch_assoc();
            $cantidad = $datos['cantidad'] + 1;
            $total_precio = $cantidad * $total;
            $query = $this->conexion->query("UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE id_producto = $id AND id_usuario = $id_user");
            return $query ? "actualizado" : "Error al ingresar";
        } else {
            $query = $this->conexion->query("INSERT INTO detalle_temp(id_usuario, id_producto, cantidad, precio_venta, total) VALUES ($id_user, $id, $cant, '$precio', $total)");
            return $query ? "registrado" : "Error al ingresar";
        }
    }

    public function cambiarClave($id, $actual, $nueva) {
        $consulta = $this->conexion->query("SELECT * FROM usuario WHERE clave = '$actual' AND idusuario = $id");
        
        if ($consulta->num_rows == 1) {
            $query = $this->conexion->query("UPDATE usuario SET clave = '$nueva' WHERE idusuario = $id");
            return $query ? 'ok' : 'error';
        } else {
            return 'dif';
        }
    }

    public function obtenerGanancias() {
        $query = mysqli_query($this->conexion, "
            SELECT 
                p.codproducto,
                p.descripcion AS producto,
                SUM(dv.cantidad) AS cantidad_vendida,
                p.precio_compra,
                p.precio_venta,
                SUM((p.precio_venta - p.precio_compra) * dv.cantidad) AS ganancia
            FROM 
                detalle_venta dv
            INNER JOIN 
                producto p ON dv.id_producto = p.codproducto
            GROUP BY 
                p.codproducto
        ");
        
        $resultados = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $resultados[] = $row;
        }
        return $resultados;
    }

    public function obtenerDatosEmpresa() {
        $query = mysqli_query($this->conexion, "SELECT * FROM configuracion");
        return mysqli_fetch_assoc($query);
    }

    public function obtenerDatosCliente($idCliente) {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE idcliente = $idCliente");
        return mysqli_fetch_assoc($query);
    }

    public function obtenerFechaVenta($idVenta) {
        $query = mysqli_query($this->conexion, "SELECT fecha FROM ventas WHERE id = $idVenta");
        $result = mysqli_fetch_assoc($query);
        return $result['fecha'];
    }

    public function obtenerDetallesVenta($idVenta) {
        $query = mysqli_query($this->conexion, 
            "SELECT d.*, p.codproducto, p.descripcion 
             FROM detalle_venta d 
             INNER JOIN producto p ON d.id_producto = p.codproducto 
             WHERE d.id_venta = $idVenta");
        
        $detalles = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $detalles[] = $row;
        }
        return $detalles;
    }

    public function calcularTotalVenta($detalles) {
        $total = 0;
        foreach ($detalles as $item) {
            $total += $item['cantidad'] * $item['precio'];
        }
        return $total;
    }

    public function obtenerTipoPago($idVenta) {
        $query = mysqli_query($this->conexion, 
            "SELECT tipo_pago FROM detalle_venta WHERE id_venta = $idVenta LIMIT 1");
        $result = mysqli_fetch_assoc($query);
        return $result['tipo_pago'] ?? '';
    }

}
?>