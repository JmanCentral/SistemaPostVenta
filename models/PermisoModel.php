<?php

require_once 'config/database.php';

class PermisoModel {
    private $conexion;

    public function __construct() {
        $this->conexion = getConnection(); // Obtiene la conexión a la base de datos
    }

    // Obtener todos los permisos
    public function obtenerPermisos() {
        $query = mysqli_query($this->conexion, "SELECT * FROM permisos");
        return $query;
    }

    // Obtener un usuario por su ID
    public function obtenerUsuario($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM usuario WHERE idusuario = $id");
        return mysqli_fetch_assoc($query);
    }

    // Obtener los permisos asignados a un usuario
    public function obtenerPermisosUsuario($id) {
        $query = mysqli_query($this->conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $id");
        $datos = array();
        while ($row = mysqli_fetch_assoc($query)) {
            $datos[$row['id_permiso']] = true;
        }
        return $datos;
    }

    // Eliminar permisos asignados a un usuario
    public function eliminarPermisosUsuario($id) {
        mysqli_query($this->conexion, "DELETE FROM detalle_permisos WHERE id_usuario = $id");
    }

    // Asignar permisos a un usuario
    public function asignarPermisosUsuario($id_user, $permisos) {
        foreach ($permisos as $permiso) {
            mysqli_query($this->conexion, "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES ($id_user, $permiso)");
        }
    }
}
?>