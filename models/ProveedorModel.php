<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos

class ProveedorModel {
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

    // Verifica si el proveedor ya existe
    public function verificarProveedor($nombre) {
        $query = mysqli_query($this->conexion, "SELECT * FROM proveedores WHERE nombre = '$nombre'");
        return mysqli_fetch_array($query);
    }

    // Inserta un nuevo proveedor
    public function insertarProveedor($NIT, $nombre, $apellido, $telefono, $email, $direccion, $usuario_id) {
        $query = mysqli_query($this->conexion, "INSERT INTO proveedores(NIT, nombre, apellido, telefono, email, direccion, usuario_id) 
                                                VALUES ('$NIT', '$nombre', '$apellido', '$telefono', '$email', '$direccion', '$usuario_id')");
        return $query;
    }

    // Obtiene todos los proveedores
    public function obtenerProveedores() {
        $query = mysqli_query($this->conexion, "SELECT * FROM proveedores");
        return $query;
    }

    // Obtiene un proveedor por su ID
    public function obtenerProveedorPorId($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM proveedores WHERE idproveedor = $id");
        return mysqli_fetch_assoc($query);
    }

    // Actualiza un proveedor
    public function actualizarProveedor($id, $NIT, $nombre, $apellido, $telefono, $email, $direccion) {
        $query = mysqli_query($this->conexion, "UPDATE proveedores SET NIT = '$NIT', nombre = '$nombre', apellido = '$apellido', 
                                                telefono = '$telefono', email = '$email', direccion = '$direccion' 
                                                WHERE idproveedor = $id");
        return $query;
    }

    // Elimina un proveedor (cambia su estado a inactivo)
    public function eliminarProveedor($id) {
        $query = mysqli_query($this->conexion, "UPDATE proveedores SET estado = 0 WHERE idproveedor = $id");
        return $query;
    }

    // Activar un proveedor (cambia su estado a inactivo)
    public function activarProveedor($id) {
        $query = mysqli_query($this->conexion, "UPDATE proveedores SET estado = 1 WHERE idproveedor = $id");
        return $query;
    }
}
?>