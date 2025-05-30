<?php
// Verificación de sesión sin generar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navbar para Administrador -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Bienvenido, <?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario') ?></span>
        <div class="d-flex">
            <a class="btn btn-danger" href="/click-serveBeta-main/auth/logout.php">Cerrar Sesión</a>
        </div>
    </div>
</nav>