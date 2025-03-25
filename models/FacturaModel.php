<?php

require_once 'config/database.php';

class FacturaModel {
    
    private $conexion;

    public function __construct() {
        $this->conexion = getConnection() ;
    }

    public function verificarPermisos($id_user, $permiso) {
        $sql = mysqli_query($this->conexion, "SELECT p.*, d.* FROM permisos p 
                                              INNER JOIN detalle_permisos d ON p.id = d.id_permiso 
                                              WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
        
        return ($sql) ? mysqli_fetch_all($sql, MYSQLI_ASSOC) : []; 
    }


    public function getVentas() {
        $query = $this->conexion->query(
            "SELECT v.*, c.idcliente, c.nombre 
            FROM ventas v INNER JOIN cliente c 
            ON v.id_cliente = c.idcliente"
        );
        return $query->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteVenta($id) {
        $stmt = $this->conexion->prepare("DELETE FROM ventas WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>