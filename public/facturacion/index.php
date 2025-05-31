<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación para Restaurantes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Facturación para Restaurantes</h1>
        </header>
        
        <div class="main-content">
            <div class="panel products-panel">
                <h2>Agregar Productos</h2>
                <div class="form-group">
                    <label for="product-name">Producto:</label>
                    <input type="text" id="product-name" placeholder="Nombre del producto">
                    <label for="product-price">Precio:</label>
                    <input type="number" id="product-price" min="0" step="0.01" placeholder="0.00">
                    <label for="product-quantity">Cantidad:</label>
                    <input type="number" id="product-quantity" min="1" value="1">
                    <button id="add-product">Agregar al Ticket</button>
                </div>

                <div class="customer-info">
                    <h3>Información del Cliente</h3>
                    <div class="form-group">
                        <label for="customer-name">Nombre del Cliente:</label>
                        <input type="text" id="customer-name" placeholder="Consumidor Final">
                        <label for="customer-nit">NIT/DPI:</label>
                        <input type="text" id="customer-nit" placeholder="C/F">
                    </div>
                </div>
            </div>
            
            <div class="panel ticket-panel">
                <h2>Vista Previa del Ticket</h2>
                
                <div class="ticket-container" id="ticket">
                    <!-- El contenido del ticket se generará dinámicamente -->
                </div>
                
                <div class="ticket-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">Q0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>IVA (12%):</span>
                        <span id="tax">Q0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">Q0.00</span>
                    </div>
                </div>
                
                <div class="actions">
                    <button id="print-ticket">Imprimir Ticket</button>
                    <button id="save-ticket">Guardar Factura</button>
                    <button id="new-ticket">Nuevo Ticket</button>
                    <button id="cancel-ticket" class="cancel-btn" style="display: none;">Anular Factura</button>
                </div>
            </div>
            
            <!-- Nuevo panel para listar facturas -->
            <div class="panel invoices-panel">
                <h2>Facturas Emitidas</h2>
                <div class="form-group">
                    <label for="search-invoice">Buscar Factura:</label>
                    <input type="text" id="search-invoice" placeholder="Número de factura o cliente">
                    <button id="search-btn">Buscar</button>
                </div>
                
                <table class="invoices-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="invoices-list">
                        <!-- Las facturas se cargarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>