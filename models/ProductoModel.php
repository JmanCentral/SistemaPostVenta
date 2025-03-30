<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos

class ProductoModel {
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

    // Verifica si el código del producto ya existe
    public function verificarCodigo($codigo) {
        $query = mysqli_query($this->conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
        return mysqli_fetch_array($query);
    }

    // Inserta un nuevo producto
    public function insertarProducto($codigo, $descripcion, $precio_compra, $precio_venta, $usuario_id, $imagen) {
        $query = mysqli_query($this->conexion, "INSERT INTO producto(codigo, descripcion, precio_compra, precio_venta, usuario_id, imagen) 
                                                VALUES ('$codigo', '$descripcion', '$precio_compra', '$precio_venta', '$usuario_id', '$imagen')");
        return $query;
    }

    // Obtiene todos los productos con su stock
    public function obtenerProductos() {
        $query = mysqli_query($this->conexion, "SELECT p.*, SUM(i.cantidad) AS stock 
                                                FROM producto p 
                                                LEFT JOIN inventario i ON p.codproducto = i.codproducto 
                                                GROUP BY p.codproducto");
        return $query;
    }

    // Obtiene un producto por su ID
    public function obtenerProductoPorId($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM producto WHERE codproducto = $id");
        return mysqli_fetch_assoc($query);
    }

    // Actualiza un producto
    public function actualizarProducto($id, $codigo, $descripcion, $precio_compra, $precio_venta, $imagen = null) {
        $sql = "UPDATE producto SET codigo = '$codigo', descripcion = '$descripcion', 
                precio_compra = '$precio_compra', precio_venta = '$precio_venta'";
        if ($imagen) {
            $sql .= ", imagen = '$imagen'";
        }
        $sql .= " WHERE codproducto = $id";
        return mysqli_query($this->conexion, $sql);
    }

    // Elimina un producto (cambia su estado a inactivo)
    public function eliminarProducto($id) {
        $query = mysqli_query($this->conexion, 
            "UPDATE producto p
             LEFT JOIN inventario i ON p.codproducto = i.codproducto
             SET p.estado = 0, i.estado = 0
             WHERE p.codproducto = $id");
        
        return $query;
    }

    // Activar un producto (cambia su estado a activo)
    public function activarProducto($id) {
        $query = mysqli_query($this->conexion, 
            "UPDATE producto p
             LEFT JOIN inventario i ON p.codproducto = i.codproducto
             SET p.estado = 1, i.estado = 1
             WHERE p.codproducto = $id");
        
        return $query;
    }

}
?>