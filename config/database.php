<?php
define('DB_HOST', 'sql112.byethost15.com');
define('DB_USER', 'b15_39936194');
define('DB_PASS', 'Kallen.741');
define('DB_NAME', 'b15_39936194_puntoventa'); // Cambia 'tu_base_de_datos' por el nombre de tu base de datos

function getConnection() {
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conexion;
}
?>
