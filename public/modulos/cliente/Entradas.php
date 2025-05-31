<?php
session_start();
require_once '../../config.php';

// Obtener la categoría "Entradas" desde la base de datos
$categoria = $pdo->prepare("
    SELECT id, nombre, descripcion 
    FROM categorias 
    WHERE nombre = 'Entradas'
");
$categoria->execute();
$categoria = $categoria->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    die("Categoría 'Entradas' no encontrada en la base de datos");
}

// Obtener productos de la categoría con sus ingredientes
$productos = $pdo->prepare("
    SELECT p.*, GROUP_CONCAT(i.nombre SEPARATOR ', ') AS ingredientes
    FROM productos p
    LEFT JOIN producto_ingredientes pi ON p.id = pi.producto_id
    LEFT JOIN ingredientes i ON pi.ingrediente_id = i.id
    WHERE p.categoria_id = ?
    GROUP BY p.id
");
$productos->execute([$categoria['id']]);
$productos = $productos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #4a4a4a;
            font-weight: bold;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            color: #6c757d;
            font-size: 1.1em;
            text-align: center;
        }

        .menu-item {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 15px;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .menu-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .menu-item div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .banner-container {
            margin-top: 40px;
            border-radius: 12px;
            overflow: hidden;
        }

        .owl-carousel img {
            border-radius: 12px;
            height: 300px;
            object-fit: cover;
        }

        .modal-content {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: #ffffff;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .modal-header .btn-close {
            color: #333;
        }

        .modal-body {
            padding: 25px;
        }

        #modalNombre {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        #modalDescripcion {
            font-size: 1.2em;
            color: #6c757d;
            text-align: center;
            margin-top: 10px;
        }

        #modalPrecio {
            font-size: 1.5em;
            color: #28a745;
            text-align: center;
            margin-top: 15px;
        }

        #ingredientesContainer {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        #ingredientesContainer label {
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6c757d;
        }

        #ingredientesContainer input {
            margin-right: 10px;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            transition: background-color 0.3s ease;
        }

        #ingredientesContainer input:checked {
            background-color: #28a745;
        }

        #btnAgregarCarrito {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            width: 100%;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        #btnAgregarCarrito:hover {
            background-color: #0056b3;
        }

        .card-redirect {
            background: linear-gradient(145deg, #fff, #f0f0f0);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 400px;
            margin: 60px auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-redirect:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 35px rgba(0,0,0,0.2);
        }

        .card-redirect h4 {
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #d32f2f;
        }

        .btn-animado {
            background: #d32f2f;
            color: #fff;
            padding: 0.9rem 2rem;
            font-size: 1.1rem;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-animado::after {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.5s;
        }

        .btn-animado:hover::after {
            left: 100%;
        }

        .btn-animado:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>
    <!-- Incluir el Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Modal para detalles del producto -->
    <div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImagen" src="" class="img-fluid mb-3" alt="Producto">
                    <h5 id="modalNombre" class="mt-3"></h5>
                    <p id="modalDescripcion" class="text-muted"></p>
                    <p id="modalPrecio" class="fw-bold"></p>
                    <div class="text-start">
                        <h6>Ingredientes:</h6>
                        <div id="ingredientesContainer">
                            <!-- Los ingredientes se llenarán dinámicamente con JavaScript -->
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" id="btnAgregarCarrito">Agregar al Carrito</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 pt-5">
        <h2 class="text-center"><?= htmlspecialchars($categoria['nombre']) ?></h2>
        <p class="text-center"><?= htmlspecialchars($categoria['descripcion']) ?></p>
        
        <div class="banner-container mt-3">
            <div class="owl-carousel">
                <img src="https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg" alt="Entrada de empanadas">
                <img src="https://www.comedera.com/wp-content/uploads/2022/09/teque%C3%B1os-venezolanos.jpg" alt="Tequeños venezolanos">
                <img src="https://www.laylita.com/recetas/wp-content/uploads/2018/06/1-Empanadas-de-carne.jpg" alt="Empanadas de carne">
            </div>
        </div>
        
        <div class="list-group mt-3">
            <?php foreach ($productos as $producto): ?>
            <div class="list-group-item list-group-item-action menu-item hover-item" 
                 onclick="mostrarDetallesProducto(
                    '<?= addslashes($producto['nombre']) ?>', 
                    '<?= addslashes($producto['descripcion']) ?>', 
                    <?= $producto['precio'] ?>, 
                    '<?= addslashes($producto['imagen_url']) ?>',
                    '<?= isset($producto['ingredientes']) ? addslashes($producto['ingredientes']) : '' ?>'
                 )">
                <img src="<?= htmlspecialchars($producto['imagen_url']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                <div>
                    <strong><?= htmlspecialchars($producto['nombre']) ?></strong><br>
                    Q<?= number_format($producto['precio'], 2) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card-redirect">
        <h4>¿Terminaste tu pedido?</h4>
        <button class="btn-animado" onclick="window.location.href='Menu.php'">
            Regresar al Menú
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar detalles del producto en modal
        function mostrarDetallesProducto(nombre, descripcion, precio, imagen, ingredientes) {
            productoActual = {
                nombre: nombre,
                descripcion: descripcion,
                precio: precio,
                imagen: imagen,
                ingredientes: ingredientes ? ingredientes.split(', ') : []
            };
            
            document.getElementById('modalNombre').textContent = nombre;
            document.getElementById('modalDescripcion').textContent = descripcion;
            document.getElementById('modalPrecio').textContent = `Q${precio.toFixed(2)}`;
            document.getElementById('modalImagen').src = imagen;
            
            const ingredientesContainer = document.getElementById('ingredientesContainer');
            ingredientesContainer.innerHTML = '';
            
            if (ingredientes && ingredientes.trim() !== '') {
                const ingredientesArray = ingredientes.split(', ');
                ingredientesArray.forEach(ing => {
                    const label = document.createElement('label');
                    label.innerHTML = `<input type="checkbox" class="ingredient-checkbox" checked data-ingrediente="${ing}"> ${ing}`;
                    ingredientesContainer.appendChild(label);
                    ingredientesContainer.appendChild(document.createElement('br'));
                });
            } else {
                ingredientesContainer.innerHTML = '<p>No se especificaron ingredientes</p>';
            }
            
            document.getElementById('btnAgregarCarrito').onclick = function() {
                const ingredientesRemovidos = [];
                const checkboxes = ingredientesContainer.querySelectorAll('.ingredient-checkbox');
                checkboxes.forEach(checkbox => {
                    if (!checkbox.checked) {
                        ingredientesRemovidos.push(checkbox.dataset.ingrediente);
                    }
                });
                
                agregarAlCarrito(nombre, descripcion, precio, ingredientesRemovidos);
                var modal = bootstrap.Modal.getInstance(document.getElementById('productoModal'));
                modal.hide();
            };
            
            var modal = new bootstrap.Modal(document.getElementById('productoModal'));
            modal.show();
        }

        $(document).ready(function(){
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: false,
                items: 1,
                autoplay: true
            });
        });
    </script>
</body>
</html>