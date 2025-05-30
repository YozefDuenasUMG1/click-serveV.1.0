<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$sql = "SELECT id, usuario, rol FROM usuarios";
$result = $conn->query($sql);

if ($result) {
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    echo json_encode($usuarios);
} else {
    echo json_encode(["error" => "Error al obtener los usuarios: " . $conn->error]);
}

$conn->close();
?>