<?php

require_once 'config/database.php';

class FacturaModel {
    
    private $conexion;

    public function __construct() {
        $this->conexion = getConnection(); // Asegúrate de que esta función devuelve un objeto MySQLi
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

    public function EliminarVenta($idVenta) {
        $this->conexion->autocommit(false); // Desactivar autocommit para manejar la transacción manualmente
    
        try {
            // Paso 1: Obtener los productos vendidos y sus cantidades junto con su proveedor
            $query = "SELECT dv.id_producto, dv.cantidad, i.idproveedor 
                      FROM detalle_venta dv
                      JOIN inventario i ON dv.id_producto = i.codproducto
                      WHERE dv.id_venta = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $idVenta);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Paso 2: Restaurar el stock en el inventario con el proveedor correcto
            while ($row = $result->fetch_assoc()) {
                $updateStock = "UPDATE inventario 
                                SET cantidad = cantidad + ? 
                                WHERE codproducto = ? AND idproveedor = ?";
                $stmtUpdate = $this->conexion->prepare($updateStock);
                $stmtUpdate->bind_param("iii", $row['cantidad'], $row['id_producto'], $row['idproveedor']);
                $stmtUpdate->execute();
            }
    
            // Paso 3: Eliminar los detalles de la venta
            $deleteDetalles = "DELETE FROM detalle_venta WHERE id_venta = ?";
            $stmt = $this->conexion->prepare($deleteDetalles);
            $stmt->bind_param("i", $idVenta);
            $stmt->execute();
    
            // Paso 4: Eliminar la venta
            $deleteVenta = "DELETE FROM ventas WHERE id = ?";
            $stmt = $this->conexion->prepare($deleteVenta);
            $stmt->bind_param("i", $idVenta);
            $stmt->execute();
    
            // Confirmar la transacción
            $this->conexion->commit();
            
        } catch (Exception $e) {
            $this->conexion->rollback(); // Revertir cambios en caso de error
            throw new Exception('Error al eliminar la venta: ' . $e->getMessage());
        }
    }
    
}

?>