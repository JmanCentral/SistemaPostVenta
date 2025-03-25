<?php
require_once "models/DashboardModel.php";

class DashboardController {
    public function index() {

        $dashboardModel = new DashboardModel();

        $data = [
            'totalU' => $dashboardModel->getTotalUsuarios(),
            'totalC' => $dashboardModel->getTotalClientes(),
            'totalP' => $dashboardModel->getTotalProductos(),
            'totalV' => $dashboardModel->getTotalVentas(),
            'totalProv' => $dashboardModel->getTotalProveedores(),
            'totalInv' => $dashboardModel->getTotalInventario(),
            'productos_stock_minimo' => $dashboardModel->getProductosStockMinimo(),
            'productos_vendidos' => $dashboardModel->getProductosMasVendidos(),
            'ventas_fecha' => $dashboardModel->getVentasPorFecha(),
            'ganancias' => $dashboardModel->obtenerGananciaTotal(),
            'stock_bajo' => $dashboardModel->   obtenerProductosStockBajo()
        ];

        require_once 'views/dashboard.php';
    }

    public function stock_bajo() {

        $dashboardModel = new DashboardModel();

        $productosStockBajo = $dashboardModel->obtenerProductosStockBajo();

        // Incluir la vista y pasar los datos
        require_once 'views/stock_bajo.php';

    }
}
?>