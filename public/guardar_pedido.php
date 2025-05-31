<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Recibir el JSON del cuerpo de la solicitud
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['mesa']) && isset($data['items'])) {
    $mesa = trim($data['mesa']);
    $items = $data['items'];
    $detalle = trim($data['detalle'] ?? '');
    $total = $data['total'] ?? 0;

    if (empty($mesa) || empty($items)) {
        echo json_encode(["error" => "Los campos 'mesa' y 'items' no pueden estar vacíos."]);
        exit;
    }

    // Convertir los items a un formato legible para la cocina
    $pedido_texto = '';
    foreach ($items as $item) {
        $pedido_texto .= "- {$item['nombre']} x{$item['cantidad']}\n";
        if (isset($item['ingredientes_removidos']) && !empty($item['ingredientes_removidos'])) {
            $pedido_texto .= "  Sin: " . implode(", ", $item['ingredientes_removidos']) . "\n";
        }
    }

    $fecha_hora = date('Y-m-d H:i:s');

    $sql = "INSERT INTO pedidos (mesa, pedido, detalle, estado, total, fecha_hora, items_json) 
            VALUES (?, ?, ?, 'pendiente', ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            $mesa,
            $pedido_texto,
            $detalle,
            $total,
            $fecha_hora,
            json_encode($items)
        ]);
        
        echo json_encode([
            "success" => true,
            "message" => "Pedido guardado correctamente",
            "pedido_id" => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => "Error al guardar el pedido: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["error" => "Faltan datos requeridos"]);
}
?>
