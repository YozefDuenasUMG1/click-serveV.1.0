<?php
require_once 'functions.php';

if (!isset($_GET['id'])) {
    die('ID de factura no proporcionado');
}

$factura = obtenerFactura($_GET['id']);

if (!$factura) {
    die('Factura no encontrada');
}

// Los items y datos_restaurante ya vienen decodificados de la función obtenerFactura
$items = $factura['items'];
$datos_restaurante = $factura['datos_restaurante'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo htmlspecialchars($factura['numero_factura']); ?></title>
    <style>
        body { 
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .ticket { 
            width: 80mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-1 { margin-bottom: 10px; }
        table { 
            width: 100%;
            border-collapse: collapse;
        }
        th, td { 
            padding: 5px;
            text-align: left;
        }
        .total {
            border-top: 1px dashed #000;
            font-weight: bold;
        }
        @media print {
            @page { margin: 0; }
            body { margin: 10px; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="text-center mb-1">
            <h2><?php echo htmlspecialchars($datos_restaurante['nombre']); ?></h2>
            <p><?php echo htmlspecialchars($datos_restaurante['direccion']); ?></p>
            <p>Tel: <?php echo htmlspecialchars($datos_restaurante['telefono']); ?></p>
            <p>NIT: <?php echo htmlspecialchars($datos_restaurante['nit']); ?></p>
            <p>Factura No: <?php echo htmlspecialchars($factura['numero_factura']); ?></p>
            <p>Fecha: <?php echo date('d/m/Y H:i', strtotime($factura['fecha'])); ?></p>
        </div>
        <div class="mb-1">
            <p>Cliente: <?php echo htmlspecialchars($factura['cliente']); ?></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Descripción</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                    <td class="text-right">Q<?php echo number_format($item['precio'], 2); ?></td>
                    <td class="text-right">Q<?php echo number_format($item['cantidad'] * $item['precio'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Subtotal:</td>
                    <td class="text-right">Q<?php echo number_format($factura['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">IVA (12%):</td>
                    <td class="text-right">Q<?php echo number_format($factura['impuesto'], 2); ?></td>
                </tr>
                <tr class="total">
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">Q<?php echo number_format($factura['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="text-center mb-1" style="margin-top: 20px;">
            <p><?php echo htmlspecialchars($datos_restaurante['mensaje']); ?></p>
            <?php if ($factura['estado'] === 'anulada'): ?>
            <div style="color: red; border: 2px solid red; padding: 5px; margin-top: 10px;">
                FACTURA ANULADA
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
