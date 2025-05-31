<?php
require_once '../config.php';

try {
    $sql = "ALTER TABLE facturas
            DROP COLUMN IF EXISTS hora,
            MODIFY COLUMN fecha DATETIME NOT NULL,
            ADD COLUMN IF NOT EXISTS items TEXT NOT NULL AFTER total,
            ADD COLUMN IF NOT EXISTS datos_restaurante TEXT NOT NULL AFTER items,
            ADD COLUMN IF NOT EXISTS estado ENUM('activa', 'anulada') DEFAULT 'activa' AFTER datos_restaurante,
            CHANGE COLUMN iva impuesto DECIMAL(10,2) NOT NULL";

    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        
        echo "Tabla facturas actualizada correctamente\n";
    } else {
        throw new Exception($conn->error);
    }
    
} catch (Exception $e) {
    echo "Error actualizando la tabla: " . $e->getMessage() . "\n";
}
?>
