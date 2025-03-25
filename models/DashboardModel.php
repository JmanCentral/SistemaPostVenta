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

    function obtenerGananciaTotal() {
        $conexion = getConnection();
        $sql = "SELECT 
                    p.codproducto,
                    p.descripcion,
                    p.precio_compra,
                    p.precio_venta,
                    SUM(dv.cantidad) AS total_vendido,
                    (p.precio_venta - p.precio_compra) * SUM(dv.cantidad) AS ganancia
                FROM detalle_venta dv
                INNER JOIN producto p ON dv.id_producto = p.codproducto
                GROUP BY p.codproducto";
        
        $resultado = $conexion->query($sql);
        
        $productos = array();
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $productos[] = $fila;
            }
        }
        
        return $productos;
    }

    function obtenerProductosStockBajo() {

        $conexion = getConnection();

        $sql = "SELECT i.codproducto, p.descripcion, i.cantidad 
                FROM inventario i
                INNER JOIN producto p ON i.codproducto = p.codproducto
                WHERE i.cantidad < 10";
        
        $resultado = $conexion ->query($sql);
    
        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
    
        return $productos;
    }

}
?>