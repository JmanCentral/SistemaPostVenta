<?php

const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'puntoventa';

/**
 * Clase para manejar la conexión a la base de datos usando el patrón Singleton.
 */
class Database
{
    /**
     * @var mysqli|null La única instancia de la conexión a la base de datos.
     */
    private static ?mysqli $connection = null;

    /**
     * Constructor privado para prevenir la creación directa de objetos.
     */
    private function __construct()
    {
    }

    /**
     * Obtiene la instancia única de la conexión a la base de datos.
     *
     * @return mysqli El objeto de conexión a la base de datos.
     * @throws mysqli_sql_exception Si la conexión falla.
     */
    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
        return self::$connection;
    }
}

/**
 * Función de ayuda para obtener la conexión a la base de datos.
 * Mantiene la compatibilidad con el código existente que usa getConnection().
 *
 * @return mysqli
 */
function getConnection(): mysqli
{
    return Database::getConnection();
}
