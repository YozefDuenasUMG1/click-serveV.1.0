<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log("Datos recibidos: " . print_r($_POST, true));
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($usuario) || empty($password)) {
        error_log("Error: Usuario o contraseña vacíos");
        header("Location: ../login.html?error=" . urlencode("Usuario o contraseña vacíos"));
        exit();
    }

    $sql = "SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuarioData = $result->fetch_assoc();
        if (password_verify($password, $usuarioData['password'])) {
            error_log("Inicio de sesión exitoso para el usuario: " . $usuarioData['usuario']);
            $_SESSION['user_id'] = $usuarioData['id'];
            $_SESSION['nombre_usuario'] = $usuarioData['usuario'];
            $_SESSION['rol'] = $usuarioData['rol'];

            // Redirige según el rol
            $redirect = match($usuarioData['rol']) {
                'admin' => '/click-serveBeta-main/modulos/admin/admin_panel.php',
                'cocinero' => '/click-serveBeta-main/modulos/cocinero/cocinero_panel.php',
                'cliente' => '/click-serveBeta-main/modulos/cliente/index.php',
                'cajero' => '/click-serveBeta-main/modulos/cajero/cajeropanel.php',
                default => '/click-serveBeta-main/login.html?error=rol_no_valido'
            };
            header("Location: $redirect");
            exit();
        } else {
            error_log("Error: Contraseña incorrecta para el usuario: " . $usuario);
            $error = "Credenciales incorrectas";
        }
    } else {
        error_log("Error: Usuario no encontrado o inactivo: " . $usuario);
        $error = "Usuario no encontrado o inactivo";
    }

    if (isset($error)) {
        header("Location: ../login.html?error=" . urlencode($error));
        exit();
    }
}
?>