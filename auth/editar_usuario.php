<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $usuario = trim($_POST['usuario']);
    $rol = trim($_POST['rol']);

    if (empty($id) || empty($usuario) || empty($rol)) {
        echo "Error: Todos los campos son obligatorios.";
        exit;
    }

    $sql = "UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error en la consulta: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssi", $usuario, $rol, $id);

    if ($stmt->execute()) {
        echo "Usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método no permitido.";
}
?>