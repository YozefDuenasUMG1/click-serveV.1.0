<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$sql = "SELECT id, mesa, pedido, detalle, estado, fecha_hora, total, items_json 
        FROM pedidos 
        WHERE estado = 'pendiente' 
        ORDER BY fecha_hora ASC";

$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($pedidos);
?>
