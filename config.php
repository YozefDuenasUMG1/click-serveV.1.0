<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'pedidos');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión MySQLi: " . $conexion->connect_error);
    }
    $conexion->set_charset("utf8");
    $conn = $conexion;
} catch (Exception $e) {
    die($e->getMessage());
}
?>
