<?php
session_start();
require_once '../../config.php';

// Obtener la categoría "Promociones"
$categoria = $pdo->prepare("SELECT id, nombre FROM categorias WHERE nombre = 'Promociones'");
$categoria->execute();
$categoria = $categoria->fetch(PDO::FETCH_ASSOC);

if (!$categoria) die("Categoría 'Promociones' no encontrada");

// Obtener productos de la categoría
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
    <title>Click&Serve</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hover-grow {
            transition: transform 0.3s ease;
        }
        .hover-grow:hover {
            transform: scale(1.05);
        }
        
          .gift-card {
    background: #fff;
    padding: 60px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .gift-card__content {
    display: flex;
    flex-wrap: wrap;
    max-width: 1200px;
    align-items: center;
    gap: 40px;
  }
  
  .gift-card__image img {
    max-width: 500px;
    width: 100%;
    border-radius: 12px;
  }
  
  .gift-card__text {
    flex: 1;
    text-align: center;
  }
  
  .gift-card__text h2 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 20px;
  }
  
  .gift-card__text p {
    font-size: 1.1rem;
    margin-bottom: 30px;
  }
  
  .gift-card__text button {
    padding: 12px 30px;
    background: transparent;
    border: 2px solid #000;
    color: #000;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .gift-card__text button:hover {
    background-color: #000;
    color: #fff;
  }
  
    .hero {
      background: url('steak.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      position: relative;
      transition: background 0.5s ease-in-out;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 4rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
      margin: 20px 0;
    }

    .hero button {
      padding: 12px 25px;
      border: 2px solid white;
      background: transparent;
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    .hero button:hover {
      background: white;
      color: #000;
    }

    .hero {
      background: url('steak.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      position: relative;
      transition: background 0.5s ease-in-out;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 4rem;
      font-weight: bold;
    }

    .hero p {
      font-size: 1.2rem;
      margin: 20px 0;
    }

    .hero button {
      padding: 12px 25px;
      border: 2px solid white;
      background: transparent;
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    .hero button:hover {
      background: white;
      color: #000;
    }
     .carousel-indicators {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 30px;
      margin-bottom: 30px;
    }

    .carousel-indicators span {
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0 10px;
      user-select: none;
    }

    .dots {
      display: flex;
      gap: 10px;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #ccc;
    }

    .dot.active {
      background-color: #a87b26;
    }
    .dining-content h2 {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .dining-content p {
      max-width: 700px;
      margin: 0 auto 40px;
      line-height: 1.6;
    }

    .dining-gallery {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: -80px;
    }

    .dining-gallery img {
      width: 30%;
      min-width: 200px;
      border-radius: 8px;
    }
   
    h3.mt-4.text-danger {
  font-size: 65px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #ff758f; /* Rosa pastel */
  text-align: center;
  font-weight: 900;
  margin-bottom: 40px;
  text-shadow: 1px 1px 4px rgba(255, 117, 143, 0.6);
}


  .position-relative.rounded-4 {
  background: #fff0f6; /* Rosa pastel muy suave */
  border-radius: 1.5rem !important;
  box-shadow: 0 8px 20px rgba(255, 117, 143, 0.15);
  cursor: pointer;
  overflow: hidden;
  transition: transform 0.35s ease, box-shadow 0.35s ease;
  border: none;
}

  .position-relative.rounded-4:hover {
  transform: translateY(-10px) scale(1.05);
  box-shadow: 0 20px 40px rgba(255, 117, 143, 0.35);
}


.position-relative.rounded-4 img {
  border-top-left-radius: 1.5rem;
  border-top-right-radius: 1.5rem;
  transition: transform 0.5s ease;
}

.position-relative.rounded-4:hover img {
  transform: scale(1.1);
  filter: brightness(1.1);
}


.position-absolute.bottom-0.w-100.text-white.text-center.p-2 {
  background: linear-gradient(180deg, transparent 0%, rgba(255, 117, 143, 0.9) 90%);
  font-size: 1.6rem;
  font-weight: 700;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #fff;
  letter-spacing: 0.06em;
  user-select: none;
}

@media (max-width: 768px) {
  h3.mt-4.text-danger {
    font-size: 45px;
  }
  .position-absolute.bottom-0.w-100.text-white.text-center.p-2 {
    font-size: 1.1rem;
  }
}
.creative-title {
  font-size: 3rem;
  font-family: 'Poppins', 'Segoe UI', sans-serif;
  font-weight: 900;
  background: linear-gradient(90deg, #c85a70 20%, #eab308 60%, #ffb6b9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-fill-color: transparent;
  letter-spacing: 3px;
  text-shadow: 2px 4px 18px #ffe4e1, 0 2px 8px #c85a70;
  animation: pop-in 1.2s cubic-bezier(.68,-0.55,.27,1.55);
  margin-bottom: 20px;
  display: inline-block;
}

.creative-title .amp {
  color: #fff;
  background: #c85a70;
  border-radius: 50%;
  padding: 0 12px;
  margin: 0 8px;
  font-size: 2.2rem;
  box-shadow: 0 2px 12px #c85a7040;
  vertical-align: middle;
  font-weight: 800;
  animation: bounce 1.5s infinite alternate;
}

@keyframes pop-in {
  0% { transform: scale(0.7) translateY(40px); opacity: 0; }
  80% { transform: scale(1.1) translateY(-8px); opacity: 1; }
  100% { transform: scale(1) translateY(0); }
}
@keyframes bounce {
  0% { transform: translateY(0);}
  100% { transform: translateY(-8px);}
}



    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <!-- Modal Detalles Producto -->
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
                        <div id="ingredientesContainer"></div>
                    </div>
                    <button class="btn btn-primary mt-3" id="btnAgregarCarrito">Agregar al Carrito</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
  <header class="hero">
    <div class="hero-content">
      <h1>Bienvenidos</h1>
      <br>
      <a href="Menu.php"><button>MENU</button></a>
    </div>
  </header>

  <section class="fine-dining">
    <div class="carousel-indicators">
      <span id="left-arrow">&larr;</span>
      <div class="dots">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot active"></span>
      </div>
      <span id="right-arrow">&rarr;</span>
    </div>
    <br>

    <section>
        <div class="dining-gallery">
      <img src="https://cdn7.kiwilimon.com/ss_secreto/3010/3010.jpg" alt="Dining Room" />
      <img src="https://www.shutterstock.com/image-photo/elegant-restaurant-dining-scene-featuring-600nw-2541042231.jpg" alt="Dish" />
      <img src="https://img.freepik.com/foto-gratis/primer-plano-cristaleria-brillante-pie-detras-placa-cena_8353-664.jpg" alt="Guests" />
        </div>
  </section>
  

   <section style="margin-top:100px;">
        <div class="dining-gallery">
      <img src="https://media.istockphoto.com/id/1411971240/es/foto/copa-de-vino-y-champain-en-bodas-y-eventos-de-lujo.jpg?s=612x612&w=0&k=20&c=u_hEm_WfnmQtfl9SCsaJ8zNI0BXpj_hCOSt-pd6ezFU=" alt="Dining Room" />
      <img src="https://i0.wp.com/foodandpleasure.com/wp-content/uploads/2022/08/restaurantes-de-lujo-bajel-sofitel.jpg?fit=1280%2C868&ssl=1" alt="Dish" />
      <img src="https://animalgourmet.com/wp-content/uploads/2018/01/jay-wennington-2065-1-e1516220610269.jpg" alt="Guests" />
        </div>
  </section>

    </section>

    <!-- Contenido principal -->
    <div class="container text-center" style="padding-top: 80px;">

        <div class="row mt-4">
            <div class="col-6"  style="margin-bottom: -90px;" >
                
            </div>
            <div class="col-6">
            </div>
        </div>

        <!-- Ofertas -->
        <h3 class="mt-4 text-danger" style="font-size: 65px;" >Ofertas para ti</h3>
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg hover-grow" onclick="mostrarDetallesProducto('2 Tocino Ranch', 'Promoción especial: Dos hamburguesas con tocino, queso, ranch y lechuga.', 75.00, 'https://img.freepik.com/fotos-premium/hamburguesa-mucho-humo-sobre-fondo-oscuro_856795-3589.jpg', 'Tocino, Queso, Ranch, Lechuga')">
                    <img src="https://img.freepik.com/fotos-premium/hamburguesa-mucho-humo-sobre-fondo-oscuro_856795-3589.jpg" class="img-fluid w-100" style="height: 250px; object-fit: cover;">
                    <div class="position-absolute bottom-0 w-100 text-white text-center p-2" style="background: rgba(0,0,0,0.6);">
                        <strong>2 Tocino Ranch x 75</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg hover-grow" onclick="mostrarDetallesProducto('Hamburguesa de Pollo', 'Pechuga de pollo empanizada, lechuga, tomate y mayonesa especial.', 29.00, 'https://tofuu.getjusto.com/orioneat-local/resized2/YKpAjwPmaEDuAhzpS-800-x.webp', 'Pollo, Lechuga, Tomate, Mayonesa')">
                    <img src="https://tofuu.getjusto.com/orioneat-local/resized2/YKpAjwPmaEDuAhzpS-800-x.webp" class="img-fluid w-100" style="height: 250px; object-fit: cover;">
                    <div class="position-absolute bottom-0 w-100 text-white text-center p-2" style="background: rgba(0,0,0,0.6);">
                        <strong>Hamburguesa de Pollo x 29</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

<section class="fade-section">
  <section class="gift-card">
    <div class="gift-card__content">
      <div class="gift-card__image">
        <img style="width: 300px;" src="click&serveimg.png" alt="Bebida refrescante" />
      </div>
      <div class="gift-card__text">
        <h2 class="creative-title">CLICK<span class="amp">&</span>SERVE</h2>
        <p style="font-size: 22px; color: #333; line-height: 1.6; max-width: 800px; margin: 20px auto; text-align: center;">
          <span style="font-weight: bold; color: #eab308;">Click&Serve</span> optimiza la experiencia gastronómica al permitir que los clientes realicen sus pedidos directamente desde la mesa, reduciendo la necesidad de personal adicional y agilizando el servicio.
        </p>
      </div>
    </div>
  </section>
</section>


    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <h5 class="fw-bold">Click&Serve</h5>
            <p>Síguenos en redes sociales</p>
            <div class="d-flex justify-content-center gap-3 mb-3">
                <a href="#" class="text-light fs-4"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-light fs-4"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-light fs-4"><i class="fab fa-whatsapp"></i></a>
            </div>
            <small>&copy; 2025 Click&Serve. Todos los derechos reservados.</small>
        </div>
    </footer>
    <div class="footer-bottom">
        <div class="cards" style="font-size: 2rem; color: white; display: flex; gap: 20px;">
          <i class="fab fa-cc-mastercard"></i>
          <i class="fab fa-cc-visa"></i>
          <i class="fab fa-cc-amex"></i>
          <i class="fab fa-cc-discover"></i>
        </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
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
                ingredientes.split(', ').forEach(ing => {
                    const label = document.createElement('label');
                    label.innerHTML = `<input type="checkbox" class="ingredient-checkbox" checked data-ingrediente="${ing}"> ${ing}`;
                    ingredientesContainer.appendChild(label);
                    ingredientesContainer.appendChild(document.createElement('br'));
                });
            } else {
                ingredientesContainer.innerHTML = '<p>No se especificaron ingredientes</p>';
            }

            document.getElementById('btnAgregarCarrito').onclick = function() {
                const removidos = [];
                ingredientesContainer.querySelectorAll('.ingredient-checkbox').forEach(c => {
                    if (!c.checked) removidos.push(c.dataset.ingrediente);
                });
                agregarAlCarrito(nombre, descripcion, precio, removidos);
                bootstrap.Modal.getInstance(document.getElementById('productoModal')).hide();
            };

            new bootstrap.Modal(document.getElementById('productoModal')).show();
        }

        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 10,
                nav: false,
                items: 1,
                autoplay: true
            });
        });

    const hero = document.querySelector('.hero');
    const dots = document.querySelectorAll('.dot');
    const leftArrow = document.getElementById('left-arrow');
    const rightArrow = document.getElementById('right-arrow');
  
    const backgrounds = [
      'url("https://foodandpleasure.com/wp-content/uploads/2022/04/terrazaz-masaryk-cuernomasaryk.jpg")',
      'url("https://img.hellofresh.com/w_3840,q_auto,f_auto,c_fill,fl_lossy/hellofresh_s3/image/HF_Y24_R16_W02_ES_ESSGB17598-4_Main_high-48eefd40.jpg")',
      'url("https://dynamic-media-cdn.tripadvisor.com/media/photo-o/19/c8/10/88/salao-do-vista.jpg?w=1200&h=1200&s=1")'
    ];
  
    let currentIndex = 0;
  
    function updateBackground() {
      hero.style.background = `${backgrounds[currentIndex]} no-repeat center center/cover`;
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
      });
    }
  
    leftArrow.addEventListener('click', () => {
      currentIndex = (currentIndex - 1 + backgrounds.length) % backgrounds.length;
      updateBackground();
    });
  
    rightArrow.addEventListener('click', () => {
      currentIndex = (currentIndex + 1) % backgrounds.length;
      updateBackground();
    });
  

    setInterval(() => {
      currentIndex = (currentIndex + 1) % backgrounds.length;
      updateBackground();
    }, 5000);

    updateBackground();
  </script>
    </script>
</body>
</html>
