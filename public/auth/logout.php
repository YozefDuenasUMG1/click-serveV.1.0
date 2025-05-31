<?php
// Iniciar sesión (debe ser lo primero, sin espacios antes)
session_start();

// Verificar si hay sesión activa para personalizar el mensaje (opcional)
$usuario = $_SESSION['usuario'] ?? null;

// Destruir completamente la sesión
session_unset();  // Limpia todas las variables de sesión
$_SESSION = [];   // Doble seguridad

// Eliminar cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 86400,  // 1 día en el pasado
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();
?>

<script>
    // Función para borrar el localStorage
    function clearLocalStorage() {
        localStorage.clear();
    }

    // Llamar a la función antes de redirigir
    clearLocalStorage();
</script>

<?php
// Redirigir con mensaje opcional
$mensaje = $usuario ? "?mensaje=Hasta pronto, " . urlencode($usuario) : "";
header("Location: /click-serveBeta-main/login.html" . $mensaje);
exit();
