<?php
// Archivo de configuración para la conexión a la base de datos y otras configuraciones globales

// Configuración de la base de datos
$host = "localhost";
$usuario = "root";
$contraseña = ""; // Contraseña vacía cuando no hay contraseña
$base_de_datos = "pedidos";

// Crear conexión a la base de datos
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    // Registrar el error en los logs del servidor sin mostrar detalles sensibles al usuario
    error_log("Error de conexión a la base de datos: " . $conn->connect_error);
    die("Error al conectar con la base de datos. Por favor contacte al administrador.");
}

// Establecer UTF-8 para la conexión
$conn->set_charset("utf8");

// Configuración de la aplicación
define('APP_NAME', 'RestaurantTech');
define('APP_VERSION', '1.0.0');

// Configuración de seguridad
ini_set('session.cookie_httponly', 1);    // Prevenir acceso a cookies via JavaScript
ini_set('session.use_only_cookies', 1);   // Forzar el uso de cookies para las sesiones
ini_set('session.cookie_secure', 1);      // Cookies solo a través de conexiones HTTPS
ini_set('session.cookie_samesite', 'Lax'); // Protección contra CSRF

// Tiempo máximo de inactividad en segundos (30 minutos)
define('SESSION_TIMEOUT', 1800);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City'); // Ajustar según tu ubicación

// Función para verificar si la sesión ha expirado
function checkSessionTimeout() {
    if (isset($_SESSION['ultimo_acceso'])) {
        if (time() - $_SESSION['ultimo_acceso'] > SESSION_TIMEOUT) {
            // La sesión ha expirado
            session_unset();     // Eliminar todas las variables de sesión
            session_destroy();   // Destruir la sesión
            
            // Redirigir al login con mensaje de expiración
            header("Location: /login.php?error=sesion_expirada");
            exit();
        } else {
            // Actualizar el tiempo de último acceso
            $_SESSION['ultimo_acceso'] = time();
        }
    }
}

// Función para validar permisos de acceso según el rol
function checkPermission($requiredRoles = []) {
    // Verificar primero si existe una sesión activa
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
        header("Location: /login.php?error=acceso_denegado");
        exit();
    }
    
    // Verificar el timeout de la sesión
    checkSessionTimeout();
    
    // Si no se especifican roles, solo verificamos que esté logueado
    if (empty($requiredRoles)) {
        return true;
    }
    
    // Verificar si el rol del usuario está entre los permitidos
    if (in_array($_SESSION['rol'], $requiredRoles)) {
        return true;
    } else {
        header("Location: /acceso_denegado.php");
        exit();
    }
}

// Función para sanitizar datos de entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}