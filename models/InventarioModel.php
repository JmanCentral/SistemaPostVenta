<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos

class InventarioModel {
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

    // Verificar si ya existe un registro de inventario para un producto y proveedor
    public function verificarInventario($codproducto, $idproveedor) {
        $query = mysqli_query($this->conexion, "SELECT * FROM inventario WHERE codproducto = '$codproducto' AND idproveedor = '$idproveedor'");
        return mysqli_fetch_array($query);
    }

    // Insertar un nuevo registro de inventario
    public function insertarInventario($codproducto, $idproveedor, $cantidad, $usuario_id) {
        $query = mysqli_query($this->conexion, "INSERT INTO inventario(codproducto, idproveedor, cantidad, usuario_id) 
                                                VALUES ('$codproducto', '$idproveedor', '$cantidad', '$usuario_id')");
        return $query;
    }

    // Actualizar la cantidad de un registro de inventario
    public function actualizarInventario($codproducto, $idproveedor, $cantidad) {
        $query = mysqli_query($this->conexion, "UPDATE inventario SET cantidad = cantidad + $cantidad 
                                                WHERE codproducto = '$codproducto' AND idproveedor = '$idproveedor'");
        return $query;
    }

    // Obtener todos los registros de inventario
    public function obtenerInventario() {
        $query = mysqli_query($this->conexion, "SELECT i.*, p.descripcion AS producto, pr.nombre AS proveedor 
                                                FROM inventario i 
                                                INNER JOIN producto p ON i.codproducto = p.codproducto 
                                                INNER JOIN proveedores pr ON i.idproveedor = pr.idproveedor");
        return $query;
    }

    // Obtener un registro de inventario por su ID
    public function obtenerInventarioPorId($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM inventario WHERE idinventario = $id");
        return mysqli_fetch_assoc($query);
    }

    // Eliminar un registro de inventario (cambiar estado a inactivo)
    public function eliminarInventario($id) {
        $query = mysqli_query($this->conexion, "UPDATE inventario SET estado = 0 WHERE idinventario = $id");
        return $query;
    }
}
?>