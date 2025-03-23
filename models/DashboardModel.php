<?php
require_once "config/database.php";

class DashboardModel {
    public function getTotalUsuarios() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM usuario");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getTotalClientes() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM cliente");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getTotalProductos() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM producto");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getTotalVentas() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM ventas");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getTotalProveedores() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM proveedores");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getTotalInventario() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT * FROM inventario");
        $total = mysqli_num_rows($query);
        mysqli_close($conexion);
        return $total;
    }

    public function getProductosStockMinimo() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT p.descripcion, SUM(i.cantidad) AS existencia 
                                          FROM producto p 
                                          INNER JOIN inventario i ON p.codproducto = i.codproducto 
                                          GROUP BY p.codproducto 
                                          HAVING SUM(i.cantidad) <= 10 
                                          ORDER BY existencia ASC 
                                          LIMIT 10");
        $resultados = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $resultados[] = $row;
        }
        mysqli_close($conexion);
        return $resultados;
    }

    public function getProductosMasVendidos() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT p.descripcion, SUM(v.cantidad) AS total_vendido 
                                          FROM producto p 
                                          INNER JOIN detalle_venta v ON p.codproducto = v.id_producto
                                          GROUP BY p.codproducto 
                                          ORDER BY total_vendido DESC 
                                          LIMIT 5");
        $resultados = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $resultados[] = $row;
        }
        mysqli_close($conexion);
        return $resultados;
    }

    public function getVentasPorFecha() {
        $conexion = getConnection();
        $query = mysqli_query($conexion, "SELECT DATE(fecha) as fecha, SUM(total) as total_ventas 
                                          FROM ventas 
                                          GROUP BY DATE(fecha) 
                                          ORDER BY fecha ASC");
        $resultados = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $resultados[] = $row;
        }
        mysqli_close($conexion);
        return $resultados;
    }
}
?>