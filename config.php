<?php
// Protección contra acceso directo
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Acceso directo no permitido.');
}

// --- CONFIGURACIÓN PARA PRODUCCIÓN ---
// Desactivar la visualización de errores en producción
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Registrar errores en un archivo seguro fuera del directorio público
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// --- SEGURIDAD ---
// Usar variables de entorno para las credenciales
$env_vars = ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'];
foreach ($env_vars as $var) {
    if (getenv($var) === false || getenv($var) === '') {
        error_log("Falta la variable de entorno: $var");
        die("Configuración incompleta. Contacta al administrador.");
    }
}
define('DB_HOST', getenv('DB_HOST'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME'));

// No uses el usuario root en producción. Crea un usuario con permisos limitados.
if (DB_USER === 'root') {
    error_log('Advertencia: Se está usando el usuario root para la base de datos.');
}

// --- OPCIONAL: Forzar HTTPS ---
// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
//     header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//     exit;
// }

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
    // Opcional: Log de conexión exitosa
    // error_log('Conexión PDO exitosa');
} catch (PDOException $e) {
    error_log("Error de conexión PDO: " . $e->getMessage());
    die("Error de conexión a la base de datos.");
}

try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión MySQLi: " . $conexion->connect_error);
    }
    $conexion->set_charset("utf8");
    $conn = $conexion;
    // Opcional: Log de conexión exitosa
    // error_log('Conexión MySQLi exitosa');
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Error de conexión a la base de datos.");
}

// Configuración adicional para producción
// Ajusta según tu zona horaria
if (!date_default_timezone_set('America/Guatemala')) {
    error_log('No se pudo establecer la zona horaria.');
}
?>