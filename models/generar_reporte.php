
<?php

if (isset($_GET['v']) && isset($_GET['cl'])) {
    $idVenta = $_GET['v'];
    $idCliente = $_GET['cl'];
    
    $ventaController = new VentaController($conexion);
    $ventaController->generarReporteVenta($idVenta, $idCliente);
} else {
    echo "Parámetros inválidos para generar el reporte.";
}
?>