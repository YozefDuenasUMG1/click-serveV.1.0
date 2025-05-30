<?php
require_once '../config.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('ID de factura no proporcionado');
    }

    global $conn;
    
    // Comenzar transacción
    $conn->begin_transaction();

    try {
        $sql = "UPDATE facturas SET estado = 'anulada' WHERE id = ? AND estado = 'activa'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $data['id']);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception('La factura no existe o ya está anulada');
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Factura anulada correctamente'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al anular la factura: ' . $e->getMessage()
    ]);
}
?>