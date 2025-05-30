<?php
// ¡Importante! No debe haber NINGÚN carácter, espacio ni línea antes de esta línea

ob_start(); // Inicia el buffer de salida
ob_clean(); // Limpia cualquier salida previa

require_once __DIR__ . '/../../config.php'; // Asegúrate que esta ruta sea correcta

// Configurar los headers apropiados
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$servername = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASSWORD;

try {
    // Conexión a la base de datos
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener parámetros
    $fecha = filter_input(INPUT_GET, 'fecha', FILTER_SANITIZE_STRING) ?: date('Y-m-d');
    $mesa = filter_input(INPUT_GET, 'mesa', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_STRING);

    // Validar formato de fecha
    if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
        throw new Exception("Formato de fecha inválido. Use YYYY-MM-DD");
    }

    // Construir consulta SQL dinámica
    $sql = "SELECT * FROM registropedidos WHERE DATE(fecha_hora_pedido) = :fecha";
    $params = [':fecha' => $fecha];

    if ($mesa && $mesa !== 'todas') {
        $sql .= " AND mesa = :mesa";
        $params[':mesa'] = $mesa;
    }

    if ($estado && $estado !== 'todos') {
        $sql .= " AND estado = :estado";
        $params[':estado'] = $estado;
    }

    $sql .= " ORDER BY fecha_hora_pedido DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total
    $total = array_reduce($pedidos, function ($sum, $pedido) {
        return $sum + (float) $pedido['total'];
    }, 0);

    // Enviar respuesta JSON
    ob_end_clean(); // Limpia el buffer antes de enviar la respuesta
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos,
        'total_pedidos' => count($pedidos),
        'total_importe' => number_format($total, 2),
        'fecha_consulta' => $fecha
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos',
        'message' => $e->getMessage()
    ]);
    exit;
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}