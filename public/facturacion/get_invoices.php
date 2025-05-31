<?php
require_once '../config.php';
require_once 'functions.php';

header('Content-Type: application/json');

try {
    global $conn;
    
    // Buscar por ID si se proporciona
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM facturas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $factura = $result->fetch_assoc();
        
        if (!$factura) {
            throw new Exception("Factura no encontrada");
        }
        
        // Decodificar los campos JSON
        $factura['items'] = json_decode($factura['items'], true);
        $factura['datos_restaurante'] = json_decode($factura['datos_restaurante'], true);
        
        echo json_encode([
            'success' => true,
            'invoice' => $factura
        ]);
        exit;
    }
    
    // Búsqueda general
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    if ($search) {
        $sql = "SELECT id, numero_factura, fecha, cliente, total, estado 
               FROM facturas 
               WHERE numero_factura LIKE ? OR cliente LIKE ?
               ORDER BY fecha DESC";
        $stmt = $conn->prepare($sql);
        $searchParam = "%$search%";
        $stmt->bind_param('ss', $searchParam, $searchParam);
    } else {
        $sql = "SELECT id, numero_factura, fecha, cliente, total, estado 
               FROM facturas 
               ORDER BY fecha DESC";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $facturas = $result->fetch_all(MYSQLI_ASSOC);
    
    // Formatear las fechas y los números
    foreach ($facturas as &$factura) {
        $factura['fecha_formato'] = date('d/m/Y H:i', strtotime($factura['fecha']));
        $factura['total_formato'] = number_format($factura['total'], 2, '.', ',');
    }
    
    echo json_encode([
        'success' => true,
        'invoices' => $facturas
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>