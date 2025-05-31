<?php
require '../../config.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos

header('Content-Type: application/json');

// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Usar las constantes definidas en config.php para la conexión a la base de datos
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
$username = DB_USER;
$password = DB_PASSWORD;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

// Manejo de errores estructurado
function handleError($message) {
    echo json_encode(['error' => $message]);
    exit;
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    handleError('Error de conexión a la base de datos: ' . $e->getMessage());
}

// Verificar conexión a la base de datos
if (!$pdo) {
    handleError('No se pudo establecer la conexión a la base de datos.');
}

// Obtener parámetros de fecha
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;

// Validación de entrada para evitar inyecciones SQL o errores de formato
if ($startDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
    handleError('El formato de startDate no es válido.');
}
if ($endDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    handleError('El formato de endDate no es válido.');
}

// Construcción dinámica de filtros SQL
$filters = [];
if ($startDate && $endDate) {
    $filters[] = "DATE(fecha_hora_pedido) BETWEEN :startDate AND :endDate";
}
$dateFilter = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';

// Implementación de caché alternativo utilizando archivos temporales
$cacheDir = sys_get_temp_dir();
$cacheFile = $cacheDir . DIRECTORY_SEPARATOR . 'dashboard_data_' . md5(json_encode($_GET)) . '.json';

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) { // Caché válido por 5 minutos
    echo file_get_contents($cacheFile);
    exit;
}

// Implementación de paginación (ejemplo para pedidosPorDia)
$limit = 100; // Límite de filas por consulta
$offset = $_GET['offset'] ?? 0;
if (!is_numeric($offset) || $offset < 0) {
    $offset = 0;
}

// Depurar consultas SQL
try {
    $stmt = $pdo->prepare("SELECT pedido AS producto, COUNT(*) as cantidad FROM registropedidos $dateFilter GROUP BY pedido ORDER BY cantidad DESC LIMIT 1");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $productoMasVendido = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$productoMasVendido) {
        handleError('No se encontraron datos para el producto más vendido.');
    }

    $stmt = $pdo->prepare("SELECT AVG(total) as promedio FROM registropedidos $dateFilter");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $promedioPedidos = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$promedioPedidos) {
        handleError('No se pudo calcular el promedio de pedidos.');
    }

    $stmt = $pdo->prepare("SELECT DATE(fecha_hora_pedido) as dia, COUNT(*) as total FROM registropedidos $dateFilter GROUP BY DATE(fecha_hora_pedido) LIMIT :limit OFFSET :offset");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $pedidosPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pedidosPorDia || empty($pedidosPorDia)) {
        $pedidosPorDia = [];
    }

    // Distribución de estados de pedidos
    $stmt = $pdo->prepare("SELECT estado, COUNT(*) as cantidad FROM registropedidos $dateFilter GROUP BY estado");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $estadoPedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$estadoPedidos || empty($estadoPedidos)) {
        $estadoPedidos = [];
    }

    // Ingresos totales por día
    $stmt = $pdo->prepare("SELECT DATE(fecha_hora_pedido) as dia, SUM(total) as ingresos FROM registropedidos $dateFilter GROUP BY DATE(fecha_hora_pedido)");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $ingresosPorDia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$ingresosPorDia || empty($ingresosPorDia)) {
        $ingresosPorDia = [];
    }

    // Cantidad de pedidos por mesa
    $stmt = $pdo->prepare("SELECT mesa, COUNT(*) as cantidad FROM registropedidos $dateFilter GROUP BY mesa");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $pedidosPorMesa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pedidosPorMesa || empty($pedidosPorMesa)) {
        $pedidosPorMesa = [];
    }

    // Tendencia de ventas por hora
    $stmt = $pdo->prepare("SELECT HOUR(fecha_hora_pedido) as hora, COUNT(*) as total FROM registropedidos $dateFilter GROUP BY HOUR(fecha_hora_pedido)");
    if ($startDate && $endDate) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    $pedidosPorHora = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pedidosPorHora || empty($pedidosPorHora)) {
        $pedidosPorHora = [];
    }

    // Calcular Total Pedidos
    $totalPedidos = $pdo->prepare("SELECT COUNT(*) as total FROM registropedidos $dateFilter");
    if ($startDate && $endDate) {
        $totalPedidos->bindParam(':startDate', $startDate);
        $totalPedidos->bindParam(':endDate', $endDate);
    }
    $totalPedidos->execute();
    $totalPedidosResult = $totalPedidos->fetch(PDO::FETCH_ASSOC);
    $totalPedidosCount = $totalPedidosResult['total'] ?? 0;

    // Calcular Ingresos Totales
    $ingresosTotales = $pdo->prepare("SELECT SUM(total) as total FROM registropedidos $dateFilter");
    if ($startDate && $endDate) {
        $ingresosTotales->bindParam(':startDate', $startDate);
        $ingresosTotales->bindParam(':endDate', $endDate);
    }
    $ingresosTotales->execute();
    $ingresosTotalesResult = $ingresosTotales->fetch(PDO::FETCH_ASSOC);
    $ingresosTotalesSum = $ingresosTotalesResult['total'] ?? 0;

    // Calcular Ticket Promedio
    $ticketPromedio = $totalPedidosCount > 0 ? $ingresosTotalesSum / $totalPedidosCount : 0;

    // Generar respuesta JSON
    $response = [
        'productoMasVendido' => $productoMasVendido,
        'promedioPedidos' => $promedioPedidos,
        'pedidosPorDia' => $pedidosPorDia,
        'estadoPedidos' => $estadoPedidos,
        'ingresosPorDia' => $ingresosPorDia,
        'pedidosPorMesa' => $pedidosPorMesa,
        'pedidosPorHora' => $pedidosPorHora,
        'totalPedidos' => $totalPedidosCount,
        'ingresosTotales' => $ingresosTotalesSum,
        'ticketPromedio' => $ticketPromedio
    ];

    // Almacenar en caché utilizando archivos
    file_put_contents($cacheFile, json_encode($response));

    // Cambiar el símbolo de moneda a 'Q' en el frontend
    // Esto se puede manejar directamente en el frontend o en la respuesta JSON
    $response['ingresosTotales'] = 'Q' . number_format($response['ingresosTotales'], 2);
    $response['ticketPromedio'] = 'Q' . number_format($response['ticketPromedio'], 2);

    // Enviar respuesta JSON
    echo json_encode($response);
} catch (PDOException $e) {
    handleError('Error en la consulta SQL: ' . $e->getMessage());
}
