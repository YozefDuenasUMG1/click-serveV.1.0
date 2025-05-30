<?php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rol = isset($_POST['rol']) ? trim($_POST['rol']) : 'cliente';

    // Validar los datos
    if (empty($usuario) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Usuario y contraseña son obligatorios"]);
        exit();
    }

    if (strlen($usuario) < 4) {
        echo json_encode(["success" => false, "message" => "El nombre de usuario debe tener al menos 4 caracteres"]);
        exit();
    }

    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres"]);
        exit();
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El nombre de usuario ya está registrado"]);
        exit();
    }

    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol, creado_en) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $usuario, $passwordHash, $rol);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}

$conn->close();
?>