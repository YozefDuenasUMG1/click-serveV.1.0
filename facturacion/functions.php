<?php
require_once '../config.php';

function generarNumeroFactura() {
    global $conn;
    
    try {
        $year = date('Y');
        $query = "SELECT MAX(numero_factura) as ultima FROM facturas WHERE numero_factura LIKE 'FACT-{$year}-%'";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['ultima']) {
                $ultimo_numero = intval(explode('-', $row['ultima'])[2]);
                return 'FACT-' . $year . '-' . str_pad($ultimo_numero + 1, 5, '0', STR_PAD_LEFT);
            }
        }
        
        return 'FACT-' . $year . '-00001';
    } catch (Exception $e) {
        error_log("Error generando número de factura: " . $e->getMessage());
        throw $e;
    }
}

function guardarFactura($data) {
    global $conn;
    
    try {
        // Comenzar transacción
        $conn->begin_transaction();

        // Generar número de factura
        $numero = generarNumeroFactura();
        
        // Preparar la consulta
        $sql = "INSERT INTO facturas (
            numero_factura, 
            fecha, 
            cliente, 
            subtotal,
            impuesto,
            total,
            items,
            datos_restaurante,
            estado
        ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, 'activa')";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }
        
        // Asegurarse de que los datos existan
        $cliente = isset($data['cliente']) ? $data['cliente'] : 'Consumidor Final';
        $subtotal = isset($data['subtotal']) ? floatval($data['subtotal']) : 0;
        $impuesto = isset($data['impuesto']) ? floatval($data['impuesto']) : 0;
        $total = isset($data['total']) ? floatval($data['total']) : 0;
        $items = json_encode($data['items']);
        $datos_restaurante = json_encode(isset($data['datos_restaurante']) ? $data['datos_restaurante'] : []);
        
        // Bind parameters
        $stmt->bind_param(
            'ssdddss',
            $numero,
            $cliente,
            $subtotal,
            $impuesto,
            $total,
            $items,
            $datos_restaurante
        );
        
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        
        // Obtener el ID insertado
        $id = $conn->insert_id;
        
        // Confirmar la transacción
        $conn->commit();
        
        return [
            'success' => true,
            'id' => $id,
            'numero_factura' => $numero,
            'mensaje' => 'Factura guardada correctamente'
        ];
        
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        if (isset($conn)) {
            $conn->rollback();
        }
        
        error_log("Error guardando factura: " . $e->getMessage());
        
        return [
            'success' => false,
            'mensaje' => 'Error al guardar la factura: ' . $e->getMessage()
        ];
    }
}

function obtenerFactura($id) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM facturas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }
        
        $stmt->bind_param('i', $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $factura = $result->fetch_assoc();
        
        if ($factura) {
            // Decodificar campos JSON
            $factura['items'] = json_decode($factura['items'], true);
            $factura['datos_restaurante'] = json_decode($factura['datos_restaurante'], true);
        }
        
        return $factura;
        
    } catch (Exception $e) {
        error_log("Error obteniendo factura: " . $e->getMessage());
        return null;
    }
}

function buscarFacturas($termino = '') {
    global $conn;
    
    try {
        if ($termino) {
            $sql = "SELECT * FROM facturas 
                    WHERE numero_factura LIKE ? OR cliente LIKE ?
                    ORDER BY fecha DESC, numero_factura DESC";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }
            
            $termino = "%$termino%";
            $stmt->bind_param('ss', $termino, $termino);
        } else {
            $sql = "SELECT * FROM facturas 
                    ORDER BY fecha DESC, numero_factura DESC";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conn->error);
            }
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error buscando facturas: " . $e->getMessage());
        return [];
    }
}

function anularFactura($id) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        $sql = "UPDATE facturas SET estado = 'anulada' WHERE id = ? AND estado = 'activa'";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conn->error);
        }
        
        $stmt->bind_param('i', $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("La factura no existe o ya está anulada");
        }
        
        $conn->commit();
        
        return [
            'success' => true,
            'mensaje' => 'Factura anulada correctamente'
        ];
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        
        error_log("Error anulando factura: " . $e->getMessage());
        
        return [
            'success' => false,
            'mensaje' => 'Error al anular la factura: ' . $e->getMessage()
        ];
    }
}
?>