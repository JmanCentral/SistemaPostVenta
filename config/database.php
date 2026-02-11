<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'puntoventa'); 

function getConnection() {
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    return $conexion;
}
?>