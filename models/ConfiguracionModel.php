<?php
require_once 'config/database.php'; // Asegúrate de incluir la conexión a la base de datos

class ConfiguracionModel {
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


    // Obtiene los datos de configuración
    public function obtenerConfiguracion() {
        $query = mysqli_query($this->conexion, "SELECT * FROM configuracion");
        return mysqli_fetch_assoc($query);
    }

    // Inserta una nueva configuración
    public function insertarConfiguracion($nombre, $NIT , $telefono, $email, $direccion) {
        $query = mysqli_query($this->conexion, "INSERT INTO configuracion(nombre, NIT , telefono, email, direccion) 
                                                VALUES ('$nombre', '$NIT', $telefono', '$email', '$direccion')");
        return $query;
    }

    // Actualiza la configuración
    public function actualizarConfiguracion($id, $nombre, $NIT , $telefono, $email, $direccion) {
        $query = mysqli_query($this->conexion, "UPDATE configuracion SET nombre = '$nombre', NIT = '$NIT' , telefono = '$telefono', 
                                                email = '$email', direccion = '$direccion' WHERE id = $id");
        return $query;
    }
}
?>