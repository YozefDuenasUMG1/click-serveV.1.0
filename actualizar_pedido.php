<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Verificar si se recibió el ID del pedido
if (!isset($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de pedido no proporcionado'
    ]);
    exit;
}

$pedido_id = $_GET['id'];
$nuevo_estado = $_GET['estado'] ?? 'completado'; // Valor por defecto

try {
    // Iniciar transacción para asegurar la integridad de los datos
    $pdo->beginTransaction();

    // 1. Obtener los datos actuales del pedido
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        throw new Exception('Pedido no encontrado');
    }

    // 2. Registrar el estado ACTUAL en la tabla histórica antes de cambiarlo
    $stmt = $pdo->prepare("INSERT INTO registropedidos 
                          (pedido_id, mesa, pedido, detalle, estado, total, fecha_hora_pedido, items_json) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $resultado_registro = $stmt->execute([
        $pedido['id'],
        $pedido['mesa'],
        $pedido['pedido'],
        $pedido['detalle'],
        $pedido['estado'], // Estado actual antes del cambio
        $pedido['total'],
        $pedido['fecha_hora'],
        $pedido['items_json']
    ]);

    if (!$resultado_registro) {
        throw new Exception('Error al registrar el historial del pedido');
    }

    // 3. Actualizar el estado en la tabla principal de pedidos
    $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $resultado_actualizacion = $stmt->execute([$nuevo_estado, $pedido_id]);

    if (!$resultado_actualizacion || $stmt->rowCount() === 0) {
        throw new Exception('No se pudo actualizar el estado del pedido');
    }

    // Confirmar ambas operaciones
    $pdo->commit();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Pedido actualizado y registrado en historial',
        'pedido_id' => $pedido_id,
        'estado_anterior' => $pedido['estado'],
        'nuevo_estado' => $nuevo_estado
    ]);

} catch (PDOException $e) {
    // Revertir en caso de error de base de datos
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Revertir en caso de otros errores
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>