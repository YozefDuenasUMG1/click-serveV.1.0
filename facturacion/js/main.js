// Variables y funciones globales
window.currentTicket = {
    items: [],
    subtotal: 0,
    tax: 0,
    total: 0,
    customer: 'Consumidor Final',
    nit: 'C/F'
};

window.formatMoney = function(amount) {
    return Number(amount).toFixed(2);
};

window.formatDate = function(date) {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    return date instanceof Date && !isNaN(date) ? 
        date.toLocaleDateString('es-GT', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'Fecha inválida';
};

window.updateTicketDisplay = function() {
    const ticketContainer = document.getElementById('ticket');
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');

    if (!window.currentTicket) return;

    const ticketHTML = `
        <div class="ticket-header">
            <h3>Click&Serve Restaurant</h3>
            <p>Dirección del Restaurante</p>
            <p>Tel: (502) XXXX-XXXX</p>
            ${window.currentTicket.numero_factura ? 
                `<p>Factura No: ${window.currentTicket.numero_factura}</p>` : ''}
            <p>Fecha: ${window.formatDate(new Date())}</p>
            <p>Cliente: ${window.currentTicket.customer}</p>
            <p>NIT/DPI: ${window.currentTicket.nit}</p>
        </div>
        <table class="ticket-items">
            <thead>
                <tr>
                    <th>Cant</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                ${window.currentTicket.items.map(item => `
                    <tr>
                        <td>${item.cantidad}</td>
                        <td>${item.descripcion}</td>
                        <td>Q${window.formatMoney(item.precio)}</td>
                        <td>Q${window.formatMoney(item.total)}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

    if (ticketContainer) ticketContainer.innerHTML = ticketHTML;
    if (subtotalElement) subtotalElement.textContent = `Q${window.formatMoney(window.currentTicket.subtotal)}`;
    if (taxElement) taxElement.textContent = `Q${window.formatMoney(window.currentTicket.tax)}`;
    if (totalElement) totalElement.textContent = `Q${window.formatMoney(window.currentTicket.total)}`;
};

// Función para actualizar información del cliente
window.updateCustomerInfo = function() {
    const customerName = document.getElementById('customer-name');
    const customerNIT = document.getElementById('customer-nit');
    
    if (window.currentTicket) {
        window.currentTicket.customer = customerName.value.trim() || 'Consumidor Final';
        window.currentTicket.nit = customerNIT.value.trim() || 'C/F';
        window.updateTicketDisplay();
    }
};

// Función para agregar productos al ticket
window.addProductToTicket = function() {
    const productName = document.getElementById('product-name');
    const productPrice = document.getElementById('product-price');
    const productQuantity = document.getElementById('product-quantity');

    const name = productName.value.trim();
    const price = parseFloat(productPrice.value);
    const quantity = parseInt(productQuantity.value);

    if (!name || isNaN(price) || isNaN(quantity) || price <= 0 || quantity <= 0) {
        alert('Por favor complete todos los campos correctamente');
        return;
    }

    const item = {
        descripcion: name,
        precio: price,
        cantidad: quantity,
        total: price * quantity
    };

    window.currentTicket.items.push(item);
    
    // Actualizar totales
    window.currentTicket.subtotal = window.currentTicket.items.reduce((sum, item) => sum + item.total, 0);
    window.currentTicket.tax = window.currentTicket.subtotal * 0.12;
    window.currentTicket.total = window.currentTicket.subtotal + window.currentTicket.tax;

    // Actualizar vista previa
    window.updateTicketDisplay();

    // Limpiar campos
    productName.value = '';
    productPrice.value = '';
    productQuantity.value = '1';
    productName.focus();
};

// Funciones globales para manejar las facturas
window.viewInvoice = async function(id) {
    try {
        const response = await fetch(`get_invoices.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            window.currentTicket = {
                id: data.invoice.id,
                numero_factura: data.invoice.numero_factura,
                items: data.invoice.items, // Los items ya vienen parseados desde PHP
                customer: data.invoice.cliente,
                nit: data.invoice.nit,
                subtotal: parseFloat(data.invoice.subtotal),
                tax: parseFloat(data.invoice.impuesto),
                total: parseFloat(data.invoice.total),
                estado: data.invoice.estado,
                datos_restaurante: data.invoice.datos_restaurante // Ya viene parseado desde PHP
            };
            
            window.updateTicketDisplay();
            const cancelButton = document.getElementById('cancel-ticket');
            if (cancelButton) {
                cancelButton.style.display = window.currentTicket.estado === 'activa' ? 'inline-block' : 'none';
            }
        } else {
            throw new Error(data.message || 'Error al cargar la factura');
        }
    } catch (error) {
        console.error('Error al cargar la factura:', error);
        alert(error.message || 'Error al cargar la factura');
    }
};

window.printInvoice = async function(id) {
    window.open(`imprimir_factura.php?id=${id}`, '_blank');
};

window.cancelInvoice = async function(id) {
    if (!confirm('¿Está seguro de que desea anular esta factura?')) {
        return;
    }

    try {
        const response = await fetch('cancel_invoice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Factura anulada correctamente');
            if (window.loadInvoices) {
                window.loadInvoices();
            }
            if (window.currentTicket && window.currentTicket.id === id) {
                window.currentTicket.estado = 'anulada';
                if (window.updateTicketDisplay) {
                    window.updateTicketDisplay();
                }
                document.getElementById('cancel-ticket').style.display = 'none';
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al anular factura:', error);
        alert('Error al anular la factura');
    }
};

// Make loadInvoices globally accessible
window.loadInvoices = async function(searchTerm = '') {
    try {
        const url = searchTerm 
            ? `get_invoices.php?search=${encodeURIComponent(searchTerm)}`
            : 'get_invoices.php';
            
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            const invoicesList = document.getElementById('invoices-list');
            if (invoicesList) {
                invoicesList.innerHTML = data.invoices.map(invoice => `
                    <tr>
                        <td>${invoice.numero_factura}</td>
                        <td>${window.formatDate(invoice.fecha)}</td>
                        <td>${invoice.cliente || 'Consumidor Final'}</td>
                        <td class="text-right">Q${window.formatMoney(invoice.total)}</td>
                        <td>
                            <span class="status-${invoice.estado}">
                                ${invoice.estado.charAt(0).toUpperCase() + invoice.estado.slice(1)}
                            </span>
                        </td>
                        <td>
                            <button onclick="window.viewInvoice(${invoice.id})" class="btn-view">Ver</button>
                            <button onclick="window.printInvoice(${invoice.id})" class="btn-print">Imprimir</button>
                            ${invoice.estado === 'activa' ? 
                                `<button onclick="window.cancelInvoice(${invoice.id})" class="btn-cancel">Anular</button>` : 
                                ''}
                        </td>
                    </tr>
                `).join('');
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al cargar facturas:', error);
        alert('Error al cargar las facturas');
    }
};

// Funciones para manejar acciones del ticket
window.handlePrintTicket = function() {
    if (!window.currentTicket.items.length) {
        alert('No hay productos en el ticket para imprimir');
        return;
    }

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>Ticket de Venta</title>
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
                    .header { margin-bottom: 20px; }
                    table { 
                        width: 100%;
                        border-collapse: collapse;
                        margin: 10px 0;
                    }
                    th, td { 
                        text-align: left;
                        padding: 3px;
                    }
                    .total-row {
                        border-top: 1px dashed #000;
                        font-weight: bold;
                    }
                    .footer {
                        margin-top: 20px;
                        text-align: center;
                    }
                    @media print {
                        @page { margin: 0; }
                        body { margin: 10px; }
                    }
                </style>
            </head>
            <body>
                <div class="ticket">
                    <div class="header text-center">
                        <h2>Click&Serve Restaurant</h2>
                        <p>Dirección del Restaurante</p>
                        <p>Tel: (502) XXXX-XXXX</p>
                        ${window.currentTicket.numero_factura ? 
                            `<p>Factura No: ${window.currentTicket.numero_factura}</p>` : ''}
                        <p>Fecha: ${window.formatDate(new Date())}</p>
                        <p>Cliente: ${window.currentTicket.customer}</p>
                        <p>NIT/DPI: ${window.currentTicket.nit}</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Cant</th>
                                <th>Descripción</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${window.currentTicket.items.map(item => `
                                <tr>
                                    <td>${item.cantidad}</td>
                                    <td>${item.descripcion}</td>
                                    <td class="text-right">Q${window.formatMoney(item.precio)}</td>
                                    <td class="text-right">Q${window.formatMoney(item.total)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Subtotal:</td>
                                <td class="text-right">Q${window.formatMoney(window.currentTicket.subtotal)}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">IVA (12%):</td>
                                <td class="text-right">Q${window.formatMoney(window.currentTicket.tax)}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3" class="text-right">Total:</td>
                                <td class="text-right">Q${window.formatMoney(window.currentTicket.total)}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="footer">
                        <p>¡Gracias por su preferencia!</p>
                        ${window.currentTicket.estado === 'anulada' ? 
                            '<p style="color: red; border: 2px solid red; padding: 5px;">FACTURA ANULADA</p>' : 
                            ''}
                    </div>
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 100);
                    };
                </script>
            </body>
        </html>
    `);
};

window.handleSaveTicket = async function() {
    if (!validateTicket()) {
        return;
    }

    try {
        const response = await fetch('save_invoice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cliente: window.currentTicket.customer,
                nit: window.currentTicket.nit,
                items: window.currentTicket.items,
                subtotal: window.currentTicket.subtotal,
                impuesto: window.currentTicket.tax,
                total: window.currentTicket.total,
                datos_restaurante: {
                    nombre: 'Click&Serve Restaurant',
                    direccion: 'Dirección del Restaurante',
                    telefono: '(502) XXXX-XXXX',
                    mensaje: '¡Gracias por su preferencia!'
                }
            })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Factura guardada correctamente');
            window.currentTicket.numero_factura = data.numero_factura;
            window.currentTicket.id = data.invoice_id;
            window.updateTicketDisplay();
            window.loadInvoices();
            document.getElementById('cancel-ticket').style.display = 'inline-block';
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al guardar factura:', error);
        alert('Error al guardar la factura');
    }
};

window.handleNewTicket = function() {
    window.currentTicket = {
        items: [],
        subtotal: 0,
        tax: 0,
        total: 0,
        customer: 'Consumidor Final',
        nit: 'C/F'
    };
    
    const customerName = document.getElementById('customer-name');
    const customerNIT = document.getElementById('customer-nit');
    if (customerName) customerName.value = '';
    if (customerNIT) customerNIT.value = '';
    
    window.updateTicketDisplay();
    document.getElementById('cancel-ticket').style.display = 'none';
};

window.handleCancelTicket = async function() {
    if (!window.currentTicket || !window.currentTicket.id) {
        alert('No hay factura para anular');
        return;
    }

    if (!confirm('¿Está seguro de que desea anular esta factura?')) {
        return;
    }

    try {
        const response = await fetch('cancel_invoice.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: window.currentTicket.id })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('Factura anulada correctamente');
            window.loadInvoices();
            window.handleNewTicket();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al anular factura:', error);
        alert('Error al anular la factura');
    }
};

// Función de validación del ticket
window.validateTicket = function() {
    if (!window.currentTicket.items.length) {
        alert('Debe agregar al menos un producto al ticket');
        return false;
    }
    return true;
};

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const addProductBtn = document.getElementById('add-product');
    const customerName = document.getElementById('customer-name');
    const customerNIT = document.getElementById('customer-nit');
    const printTicketBtn = document.getElementById('print-ticket');
    const saveTicketBtn = document.getElementById('save-ticket');
    const newTicketBtn = document.getElementById('new-ticket');
    const cancelTicketBtn = document.getElementById('cancel-ticket');
    const searchInput = document.getElementById('search-invoice');
    const searchBtn = document.getElementById('search-btn');

    // Event Listeners
    if (addProductBtn) addProductBtn.addEventListener('click', window.addProductToTicket);
    if (customerName) customerName.addEventListener('change', window.updateCustomerInfo);
    if (customerNIT) customerNIT.addEventListener('change', window.updateCustomerInfo);
    if (printTicketBtn) printTicketBtn.addEventListener('click', window.handlePrintTicket);
    if (saveTicketBtn) saveTicketBtn.addEventListener('click', window.handleSaveTicket);
    if (newTicketBtn) newTicketBtn.addEventListener('click', window.handleNewTicket);
    if (cancelTicketBtn) cancelTicketBtn.addEventListener('click', window.handleCancelTicket);
    if (searchBtn) searchBtn.addEventListener('click', window.handleSearch);
    if (searchInput) searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') window.handleSearch();
    });

    // Mostrar la vista previa inicial del ticket
    window.updateTicketDisplay();
    
    // Cargar facturas al iniciar
    window.loadInvoices();

});