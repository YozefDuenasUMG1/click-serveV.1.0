<?php
// Verificación de sesión sin generar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navbar Componente Reutilizable -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="click&serveimg.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            Click&Serve
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Menu.php">Menú</a>
                </li>
                
                <?php if(isset($_SESSION['nombre_usuario'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['nombre_usuario']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="pedidos.php"></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <a class="dropdown-item text-danger" href="/click-serveBeta-main/auth/logout.php">Cerrar Sesión</a>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <button id="toggle-carrito-nav" class="btn btn-secondary position-relative">
                       <img src="/click-serveBeta-main/carrito.png" alt="Carrito" style="width: 30px; height: 30px;" class="rounded-full shadow-md hover:bg-blue-700 transition duration-300 cursor-pointer" />

                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Overlay y Carrito -->
<div class="overlay" id="overlay"></div>

<div id="carrito-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Carrito de Pedido</h4>
        <button class="btn-close" onclick="toggleCarrito()"></button>
    </div>
    
    <div class="form-group mb-3">
        <label for="mesa-select" class="form-label">Número de Mesa:</label>
        <select class="form-select" id="mesa-select">
            <option value="" selected disabled>Seleccionar mesa</option>
            <?php for($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>">Mesa <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>
    
    <p id="numero-mesa" class="fw-bold"></p>
    
    <h5 class="mb-3">Detalles del Pedido</h5>
    <div id="carrito" class="mb-4"></div>
    
    <div class="cart-total-section">
        <h5 class="d-flex justify-content-between">
            <span>Total:</span> 
            <span>Q<span id="total">0</span></span>
        </h5>
    </div>
    
    <div class="form-group mb-3 mt-3">
        <label for="detalle" class="form-label">Detalles adicionales:</label>
        <textarea class="form-control" id="detalle" rows="2" placeholder="Instrucciones especiales, alergias, etc."></textarea>
    </div>
    
    <button class="btn btn-primary mt-3 w-100" onclick="enviarPedido()">Enviar Pedido</button>
</div>

<!-- Modal para confirmación de pedido -->
<div class="modal fade" id="confirmacionPedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="mensajePedido" class="border p-3 bg-light" style="white-space: pre-wrap;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir pidiendo</button>
                <button type="button" class="btn btn-primary" onclick="confirmarPedido()">Confirmar Pedido</button>
            </div>
        </div>
    </div>
</div>
<

<style>
   /* Fuente estilizada */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap');

.navbar {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: black;
    padding: 0.8rem 1rem;
    font-family: 'Poppins', sans-serif;
}

.nav-link {
    font-weight: 600;
    font-size: 19px;
    letter-spacing: 0.5px;
    color: #333;
    transition: color 0.2s ease;
}

.nav-link:hover {
    color: #003d80;
}

/* Estilo para el nombre del cliente en el dropdown o navbar */
#userDropdown {
    font-weight: 600;
    font-size: 19px;
    color: #2b2b2b;
    font-family: 'Poppins', sans-serif;
}


    .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Carrito */
    #carrito-container {
        position: fixed;
        top: 0;
        right: -420px;
        width: 400px;
        height: 100vh;
        background: #fff;
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
        transition: right 0.3s ease-in-out;
        z-index: 1050;
        padding: 25px;
        overflow-y: auto;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    #carrito-container.mostrar {
        right: 0;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 1040;
        display: none;
    }

    .cart-item {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }

    .cart-item-header {
        font-weight: 600;
        font-size: 16px;
    }

    .cart-item-details {
        font-size: 13px;
        color: #555;
        margin-top: 5px;
    }

    .cart-total-section {
        border-top: 2px dashed #ccc;
        margin-top: 20px;
        padding-top: 15px;
    }

    /* Botones */
    .btn-close {
        background-color: #f0f0f0;
        border-radius: 50%;
        padding: 0.5rem;
    }

    .btn {
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-outline-secondary {
        border-radius: 6px;
    }

    /* Select y textarea */
    .form-select, .form-control {
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    /* Modal */
    .modal-content {
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        #carrito-container {
            width: 100%;
            right: -100%;
            border-radius: 0;
        }
    }
</style>


<!-- Scripts del Carrito -->
<script>
    // Variables globales
    let pedidoItems = [];
    let numeroMesa = "";
    
    // Función para mostrar/ocultar carrito
    function toggleCarrito() {
        const carrito = document.getElementById("carrito-container");
        const overlay = document.getElementById("overlay");
        
        carrito.classList.toggle("mostrar");
        overlay.style.display = carrito.classList.contains("mostrar") ? "block" : "none";
        
        if (carrito.classList.contains("mostrar")) {
            actualizarVistaCarrito();
        }
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar eventos
        document.getElementById("toggle-carrito-nav").addEventListener("click", function(e) {
            e.preventDefault();
            toggleCarrito();
        });
        
        document.getElementById("overlay").addEventListener("click", toggleCarrito);
        
        document.getElementById("mesa-select").addEventListener("change", function() {
            numeroMesa = this.value;
            document.getElementById("numero-mesa").textContent = `Mesa: ${numeroMesa}`;
            localStorage.setItem('mesaSeleccionada', numeroMesa);
        });
        
        // Cargar datos guardados
        const nombre = localStorage.getItem('nombre');
        const mesa = localStorage.getItem('mesaSeleccionada');
        
        if (nombre) {
            document.getElementById('mensaje').innerText = `Hola ${nombre}`;
        }
        
        if (mesa) {
            document.getElementById('mesa-select').value = mesa;
            numeroMesa = mesa;
            document.getElementById("numero-mesa").textContent = `Mesa: ${numeroMesa}`;
        }
        
        const itemsGuardados = localStorage.getItem('pedidoItems');
        if (itemsGuardados) {
            pedidoItems = JSON.parse(itemsGuardados);
            document.getElementById("cart-count").textContent = pedidoItems.length;
        }
    });
    
    // Funciones del carrito
    function agregarAlCarrito(nombre, descripcion, precio, ingredientesRemovidos = []) {
        const item = {
            nombre: nombre,
            descripcion: descripcion,
            precio: precio,
            cantidad: 1,
            ingredientes_removidos: ingredientesRemovidos
        };
        
        const itemExistente = pedidoItems.findIndex(i => i.nombre === nombre);
        
        if (itemExistente !== -1) {
            pedidoItems[itemExistente].cantidad += 1;
        } else {
            pedidoItems.push(item);
        }
        
        document.getElementById("cart-count").textContent = pedidoItems.length;
        localStorage.setItem('pedidoItems', JSON.stringify(pedidoItems));
        
        actualizarVistaCarrito();
    }
    
    function actualizarVistaCarrito() {
        const carritoElement = document.getElementById("carrito");
        const totalElement = document.getElementById("total");
        const cartCountElement = document.getElementById("cart-count");
        
        carritoElement.innerHTML = "";
        
        if (pedidoItems.length === 0) {
            carritoElement.innerHTML = "<p class='text-muted'>El carrito está vacío</p>";
            totalElement.textContent = "0.00";
            cartCountElement.textContent = "0";
            return;
        }
        
        let total = 0;
        
        pedidoItems.forEach((item, index) => {
            const itemTotal = item.precio * item.cantidad;
            total += itemTotal;
            
            let ingredientesRemovidosHtml = '';
            if (item.ingredientes_removidos && item.ingredientes_removidos.length > 0) {
                ingredientesRemovidosHtml = `
                    <div class="text-danger fst-italic">
                        <small>Sin: ${item.ingredientes_removidos.join(', ')}</small>
                    </div>`;
            }
            
            const div = document.createElement("div");
            div.className = "cart-item";
            div.innerHTML = `
                <div class="d-flex justify-content-between">
                    <div class="cart-item-header">${item.nombre} x${item.cantidad}</div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="ajustarCantidad(${index}, -1)">-</button>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="ajustarCantidad(${index}, 1)">+</button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarItem(${index})">X</button>
                    </div>
                </div>
                <div class="cart-item-details">
                    ${item.descripcion}
                    ${ingredientesRemovidosHtml}
                </div>
                <div class="d-flex justify-content-between">
                    <span>Precio unitario: Q${item.precio.toFixed(2)}</span>
                    <strong>Q${itemTotal.toFixed(2)}</strong>
                </div>
            `;
            carritoElement.appendChild(div);
        });
        
        totalElement.textContent = total.toFixed(2);
        cartCountElement.textContent = pedidoItems.length;
    }
    
    function ajustarCantidad(index, cambio) {
        pedidoItems[index].cantidad += cambio;
        
        if (pedidoItems[index].cantidad <= 0) {
            pedidoItems.splice(index, 1);
        }
        
        localStorage.setItem('pedidoItems', JSON.stringify(pedidoItems));
        actualizarVistaCarrito();
    }
    
    function eliminarItem(index) {
        pedidoItems.splice(index, 1);
        
        localStorage.setItem('pedidoItems', JSON.stringify(pedidoItems));
        actualizarVistaCarrito();
    }
    
    function enviarPedido() {
        if (numeroMesa === "") {
            alert("Por favor selecciona un número de mesa");
            return;
        }
        
        if (pedidoItems.length === 0) {
            alert("El carrito está vacío");
            return;
        }

        const detalle = document.getElementById("detalle").value;
        
        let mensaje = `Pedido para la mesa ${numeroMesa}\n\nDetalles del pedido:\n`;
        
        pedidoItems.forEach(item => {
            mensaje += `- ${item.nombre} x${item.cantidad}: Q${(item.precio * item.cantidad).toFixed(2)}\n`;
            if (item.ingredientes_removidos && item.ingredientes_removidos.length > 0) {
                mensaje += `  Sin: ${item.ingredientes_removidos.join(', ')}\n`;
            }
        });
        
        mensaje += `\nTotal: Q${document.getElementById("total").textContent}`;
        
        if (detalle.trim() !== "") {
            mensaje += `\n\nInstrucciones especiales: ${detalle}`;
        }
        
        document.getElementById('mensajePedido').textContent = mensaje;
        const modal = new bootstrap.Modal(document.getElementById('confirmacionPedidoModal'));
        modal.show();
        
        toggleCarrito();
    }
    
    function confirmarPedido() {
        const pedidoData = {
            mesa: numeroMesa,
            items: pedidoItems.map(item => ({
                ...item,
                ingredientes_removidos: item.ingredientes_removidos || []
            })),
            detalle: document.getElementById("detalle").value,
            total: parseFloat(document.getElementById("total").textContent)
        };

        fetch('../../guardar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(pedidoData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("¡Pedido confirmado! Se está preparando en cocina.");
                
                pedidoItems = [];
                localStorage.setItem('pedidoItems', JSON.stringify(pedidoItems));
                document.getElementById("detalle").value = "";
                actualizarVistaCarrito();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmacionPedidoModal'));
                modal.hide();
            } else {
                throw new Error(data.error || 'Error al procesar el pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Hubo un error al procesar el pedido. Por favor, intente de nuevo.");
        });
    }
    
    // Hacer funciones accesibles globalmente
    window.toggleCarrito = toggleCarrito;
    window.agregarAlCarrito = agregarAlCarrito;
    window.ajustarCantidad = ajustarCantidad;
    window.eliminarItem = eliminarItem;
    window.enviarPedido = enviarPedido;
    window.actualizarVistaCarrito = actualizarVistaCarrito;
    window.confirmarPedido = confirmarPedido;
</script>