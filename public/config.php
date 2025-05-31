<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenciales de Railway MySQL
define('DB_HOST', 'metro.proxy.rlwy.net');
define('DB_PORT', '56323'); // Puerto específico de Railway
define('DB_USER', 'root');
define('DB_PASSWORD', 'yeqZhRxholEvzevsVFBzBhmoeKYMDamy');
define('DB_NAME', 'railway'); // Nombre de la base de datos en Railway

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_PERSISTENT => false // Recomendado para entornos de producción
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión MySQLi: " . $conexion->connect_error);
    }
    $conexion->set_charset("utf8");
    $conn = $conexion;
} catch (Exception $e) {
    die($e->getMessage());
}

// Opcional: Configuración adicional para producción
date_default_timezone_set('America/Guatemala'); // Ajusta según tu zona horaria
?>