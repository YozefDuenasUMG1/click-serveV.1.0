<?php
require_once '../config.php';
require_once 'functions.php';

// Evitar que PHP muestre errores en la salida JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Obtener el contenido raw del POST
    $inputJSON = file_get_contents('php://input');
    
    // Verificar si hay datos
    if (empty($inputJSON)) {
        throw new Exception('No se recibieron datos');
    }
    
    // Decodificar JSON
    $data = json_decode($inputJSON, true);
    
    // Verificar si el JSON es válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }

    // Validaciones básicas
    if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
        throw new Exception('La factura debe tener al menos un item');
    }

    if (!isset($data['total']) || !is_numeric($data['total']) || $data['total'] <= 0) {
        throw new Exception('El total de la factura debe ser mayor a 0');
    }

    // Validar y preparar los datos
    $data = array_merge([
        'cliente' => 'Consumidor Final',
        'nit' => 'C/F',
        'subtotal' => 0,
        'impuesto' => 0,
        'datos_restaurante' => [
            'nombre' => 'Click&Serve Restaurant',
            'direccion' => 'Dirección del Restaurante',
            'telefono' => '(502) XXXX-XXXX',
            'mensaje' => '¡Gracias por su preferencia!'
        ]
    ], $data);

    // Asegurar que los valores numéricos sean flotantes
    $data['subtotal'] = floatval($data['subtotal']);
    $data['impuesto'] = floatval($data['impuesto']);
    $data['total'] = floatval($data['total']);
    
    // Guardar la factura
    $resultado = guardarFactura($data);
    
    if ($resultado['success']) {
        echo json_encode([
            'success' => true,
            'message' => $resultado['mensaje'],
            'invoice_id' => $resultado['id'],
            'numero_factura' => $resultado['numero_factura']
        ]);
    } else {
        throw new Exception($resultado['mensaje']);
    }

} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error en save_invoice.php: " . $e->getMessage());
    
    // Respuesta de error
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la factura: ' . $e->getMessage()
    ]);
}
?>