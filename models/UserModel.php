<?php
require_once "config/database.php";

class UserModel {
    private $conexion; // Propiedad para la conexión

    public function __construct() {
        $this->conexion = getConnection(); // Inicializa la conexión al instanciar la clase
    }

    public function authenticate($user, $clave) {
        $user = mysqli_real_escape_string($this->conexion, $user);
        $clave = mysqli_real_escape_string($this->conexion, $clave);

        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave' AND estado = 1");

        return mysqli_fetch_assoc($query);
    }

    public function obtenerUsuarioPorId($id) {
        $id = mysqli_real_escape_string($this->conexion, $id); // Evitar SQL Injection
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE idusuario = '$id' LIMIT 1");
        return ($query) ? mysqli_fetch_assoc($query) : null;
    }
    

    // Verifica si el usuario tiene permisos
    public function verificarPermisos($id_user, $permiso) {
        $sql = mysqli_query($this->conexion, "SELECT p.*, d.* FROM permisos p 
                                              INNER JOIN detalle_permisos d ON p.id = d.id_permiso 
                                              WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
        
        return ($sql) ? mysqli_fetch_all($sql, MYSQLI_ASSOC) : []; // Retorna un array asociativo
    }

    // Obtiene todos los usuarios
    public function obtenerUsuarios() {
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario ORDER BY estado DESC");
        return $query; // Devuelve un objeto mysqli_result
    }

    // Inserta un nuevo usuario
    public function insertarUsuario($nombre, $email, $user, $clave) {
        $query = mysqli_query($this->conexion, "INSERT INTO usuario(nombre, correo, usuario, clave) 
                                                VALUES ('$nombre', '$email', '$user', '$clave')");
        return ($query) ? mysqli_insert_id($this->conexion) : false;
    }

    // Verifica si el correo ya existe
    public function verificarCorreo($email) {
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE correo = '$email'");
        return ($query) ? mysqli_fetch_assoc($query) : null;
    }

    // Actualiza un usuario
    public function actualizarUsuario($id, $nombre, $correo, $usuario) {
        $sql = mysqli_query($this->conexion, "UPDATE usuario SET nombre = '$nombre', correo = '$correo', usuario = '$usuario' 
                                              WHERE idusuario = $id");
        return ($sql) ? mysqli_affected_rows($this->conexion) > 0 : false;
    }

    // Elimina un usuario (cambia su estado a inactivo)
    public function eliminarUsuario($id) {
        $sql = mysqli_query($this->conexion, "UPDATE usuario SET estado = 0 WHERE idusuario = $id");
        return ($sql) ? mysqli_affected_rows($this->conexion) > 0 : false;
    }
}
?>
