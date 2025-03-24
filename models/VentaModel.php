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
        
        return ($sql) ? mysqli_fetch_all($sql, MYSQLI_ASSOC) : []; 
    }

    // Buscar cliente por identificación
    public function buscarCliente($identificacion) {
        $query = mysqli_query($this->conexion, "SELECT * FROM cliente WHERE identificacion LIKE '%$identificacion%' AND estado = 1");
        $datos = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $data['id'] = $row['idcliente'];
            $data['label'] = $row['identificacion'];
            $data['nombre'] = $row['nombre'];
            $data['apellido'] = $row['apellido'];
            $data['direccion'] = $row['direccion'];
            $data['telefono'] = $row['telefono'];
            array_push($datos, $data);
        }
        return $datos;
    }

    // Buscar producto por nombre o código
    public function buscarProducto($nombre) {
        $query = mysqli_query($this->conexion, "SELECT p.*, SUM(i.cantidad) AS stock 
                                                FROM producto p 
                                                LEFT JOIN inventario i ON p.codproducto = i.codproducto 
                                                WHERE (p.codigo LIKE '%$nombre%' OR p.descripcion LIKE '%$nombre%') AND p.estado = 1 
                                                GROUP BY p.codproducto");
        $datos = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $data['id'] = $row['codproducto'];
            $data['label'] = $row['codigo'] . ' - ' . $row['descripcion'];
            $data['value'] = $row['descripcion'];
            $data['precio'] = $row['precio_venta'];
            $data['stock'] = $row['stock'] ?? 0;
            array_push($datos, $data);
        }
        return $datos;
    }

    // Obtener detalles temporales de la venta
    public function obtenerDetalleTemporal($id_usuario) {
        $query = mysqli_query($this->conexion, "SELECT d.*, p.codproducto, p.descripcion 
                                                FROM detalle_temp d 
                                                INNER JOIN producto p ON d.id_producto = p.codproducto 
                                                WHERE d.id_usuario = $id_usuario");
        $datos = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $data['id'] = $row['id'];
            $data['descripcion'] = $row['descripcion'];
            $data['cantidad'] = $row['cantidad'];
            $data['precio_venta'] = $row['precio_venta'];
            $data['sub_total'] = number_format($row['precio_venta'] * $row['cantidad'], 2, '.', ',');
            array_push($datos, $data);
        }
        return $datos;
    }

    // Eliminar detalle temporal
    public function eliminarDetalleTemporal($id_detalle) {
        $verificar = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id = $id_detalle");
        $datos = mysqli_fetch_assoc($verificar);
        if ($datos['cantidad'] > 1) {
            $cantidad = $datos['cantidad'] - 1;
            $query = mysqli_query($this->conexion, "UPDATE detalle_temp SET cantidad = $cantidad WHERE id = $id_detalle");
            return $query ? "restado" : "Error";
        } else {
            $query = mysqli_query($this->conexion, "DELETE FROM detalle_temp WHERE id = $id_detalle");
            return $query ? "ok" : "Error";
        }
    }

    // Procesar la venta
    public function procesarVenta($id_cliente, $id_usuario, $tipo_pago, $fecha_venta) {
        // Obtener el total de la venta
        $consulta = mysqli_query($this->conexion, "SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_usuario");
        $result = mysqli_fetch_assoc($consulta);
        $total = $result['total_pagar'];

        // Insertar la venta
        $insertar = mysqli_query($this->conexion, "INSERT INTO ventas(id_cliente, total, id_usuario, fecha) VALUES ($id_cliente, '$total', $id_usuario, '$fecha_venta')");
        if ($insertar) {
            // Obtener el ID de la venta recién insertada
            $id_maximo = mysqli_query($this->conexion, "SELECT MAX(id) AS total FROM ventas");
            $resultId = mysqli_fetch_assoc($id_maximo);
            $ultimoId = $resultId['total'];

            // Insertar detalles de la venta y actualizar el inventario
            $consultaDetalle = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $id_usuario");
            while ($row = mysqli_fetch_assoc($consultaDetalle)) {
                $id_producto = $row['id_producto'];
                $cantidad = $row['cantidad'];
                $precio = $row['precio_venta'];

                // Insertar detalle de venta con el tipo de pago
                $insertarDet = mysqli_query($this->conexion, "INSERT INTO detalle_venta(id_producto, id_venta, cantidad, precio, tipo_pago) VALUES ($id_producto, $ultimoId, $cantidad, '$precio', '$tipo_pago')");

                // Actualizar el inventario
                $query_inventario = mysqli_query($this->conexion, "SELECT * FROM inventario WHERE codproducto = $id_producto ORDER BY cantidad DESC LIMIT 1");
                $inventario = mysqli_fetch_assoc($query_inventario);
                $nueva_cantidad = $inventario['cantidad'] - $cantidad;

                if ($nueva_cantidad >= 0) {
                    mysqli_query($this->conexion, "UPDATE inventario SET cantidad = $nueva_cantidad WHERE idinventario = {$inventario['idinventario']}");
                } else {
                    // Manejar casos donde no hay suficiente stock
                    return ['mensaje' => 'No hay suficiente stock para el producto ' . $id_producto];
                }
            }

            // Eliminar detalles temporales
            $eliminar = mysqli_query($this->conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id_usuario");
            return ['id_cliente' => $id_cliente, 'id_venta' => $ultimoId];
        } else {
            return ['mensaje' => 'error'];
        }
    }

    // Agregar producto al detalle temporal
    public function agregarDetalleTemporal($id, $cant, $precio, $id_user) {
        $total = $precio * $cant;

        // Verificar si el producto ya está en el detalle temporal
        $verificar = mysqli_query($this->conexion, "SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
        $result = mysqli_num_rows($verificar);
        $datos = mysqli_fetch_assoc($verificar);

        if ($result > 0) {
            $cantidad = $datos['cantidad'] + 1;
            $total_precio = $cantidad * $total;
            $query = mysqli_query($this->conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE id_producto = $id AND id_usuario = $id_user");
            return $query ? "actualizado" : "Error al ingresar";
        } else {
            $query = mysqli_query($this->conexion, "INSERT INTO detalle_temp(id_usuario, id_producto, cantidad, precio_venta, total) VALUES ($id_user, $id, $cant, '$precio', $total)");
            return $query ? "registrado" : "Error al ingresar";
        }
    }
}
?>