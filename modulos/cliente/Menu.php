<?php
session_start();
require_once '../../config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Click&Serve - Menú</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Fondo pastel profesional */
        body {
            background-color: #fdf6f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        nav.navbar {
            background-color: #ffe4e1 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        nav.navbar .navbar-brand, nav.navbar .nav-link {
            color: #c85a70 !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        nav.navbar .nav-link:hover {
            color: #e76f88 !important;
        }

        .btn-carrito {
            background-color: #f7d8db;
            border: 2px solid #e76f88;
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            color: #c85a70;
            font-weight: 700;
            transition: all 0.4s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-carrito:hover {
            background-color: #e76f88;
            color: white;
            box-shadow: 0 0 15px rgba(231, 111, 136, 0.7);
            transform: scale(1.1) rotate(-2deg);
        }

        .container-menu {
            padding-top: 80px;
            padding-bottom: 40px;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .titulo-menu {
            text-align: center;
            margin: 40px auto;
            color: #c85a70;
            font-weight: 800;
            text-transform: uppercase;
            border-bottom: 4px solid #e76f88;
            padding-bottom: 12px;
            max-width: 600px;
            font-size: 2.6rem;
            letter-spacing: 2px;
            font-family: 'Poppins', sans-serif;
        }

        /* Grid con espacio amplio y tarjetas */
        .row.row-cols-1.row-cols-md-2.row-cols-lg-3.g-4 {
            margin-left: 0;
            margin-right: 0;
        }

        .categoria-card {
            background: #fff0f3;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(231, 111, 136, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            aspect-ratio: 1 / 1;
            min-height: 370px;
            height: 100%;
            border: 2px solid transparent;
        }

        .categoria-card:hover {
            transform: translateY(-10px) scale(1.07);
            box-shadow: 0 25px 45px rgba(231, 111, 136, 0.4);
            border-color: #e76f88;
            background: #ffe9f0;
        }

        .categoria-card a {
            color: inherit;
            text-decoration: none;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .categoria-img {
            width: 100%;
            height: 60%;
            object-fit: cover;
            border-top-left-radius: 25px;
            border-top-right-radius: 25px;
            flex-shrink: 0;
            filter: drop-shadow(0 3px 3px rgba(231, 111, 136, 0.15));
            transition: filter 0.3s ease;
        }

        .categoria-card:hover .categoria-img {
            filter: drop-shadow(0 6px 10px rgba(231, 111, 136, 0.35));
        }

        .categoria-body {
            padding: 25px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .categoria-title {
            font-size: 2.1rem;
            color: #c85a70;
            font-weight: 900;
            margin-bottom: 0.3rem;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 1px #f9cdd0;
        }

        .categoria-subtitle {
            font-weight: 700;
            color: #a56a7b;
            margin-bottom: 0.8rem;
            font-size: 1.3rem;
            font-style: italic;
            letter-spacing: 0.5px;
        }

        .categoria-desc {
            color: #8b6a74;
            font-size: 1.05rem;
            line-height: 1.4;
            padding: 0 10px;
        }

        /* Botón regresar */
        .text-center.mt-5 {
            margin-top: 3rem !important;
        }

        .btn-danger.btn-lg {
            border-radius: 50px;
            padding: 0.9rem 3rem;
            font-size: 1.25rem;
            font-weight: 700;
            background-color: #c85a70;
            border: none;
            transition: background-color 0.3s ease;
            box-shadow: 0 8px 15px rgba(200, 90, 112, 0.4);
        }

        .btn-danger.btn-lg:hover {
            background-color: #e76f88;
            box-shadow: 0 12px 25px rgba(231, 111, 136, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .titulo-menu {
                font-size: 2rem;
            }

            .categoria-card {
                min-height: 320px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container container-menu">
        <h1 class="titulo-menu">Menú de la Casa</h1>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            $categorias = [
                ["Desayunos", "https://comedera.com/wp-content/uploads/sites/9/2022/12/Desayono-americano-shutterstock_2120331371.jpg", "Lo mejor de la Casa", "Empieza tu día con nuestros deliciosos desayunos.", "Desayunos.php"],
                ["Platos Principales", "https://mandolina.co/wp-content/uploads/2024/06/carne-asada-a-la-parrilla-1080x550-1-1200x900.jpg", "El sabor de nuestro puerto", "Nuestros platos estrella preparados con las mejores recetas.", "PlatosPrincipales.php"],
                ["Antojos", "https://foodisafourletterword.com/wp-content/uploads/2020/09/Instant_Pot_Birria_Tacos_with_Consomme_Recipe_tacoplate.jpg", "Lo mejor de la Casa", "Deliciosos antojitos para compartir o disfrutar solo.", "Antojos.php"],
                ["Entradas", "https://www.recetasnestle.com.ec/sites/default/files/srh_recipes/4e4293857c03d819e4ae51de1e86d66a.jpg", "Para comenzar", "Perfectas para compartir mientras esperas tu plato principal.", "Entradas.php"],
                ["Bebidas", "https://www.tuhogar.com/content/dam/cp-sites/home-care/tu-hogar/es_mx/recetas/snacks-bebidas-y-postres/aprende-a-preparar-batidos-saludables/4-ideas-para-preparar-batidos-saludables-axion.jpg", "Refrescantes", "La mejor selección de bebidas para acompañar tu comida.", "bebidas.php"],
                ["Postres", "https://images.aws.nestle.recipes/resized/2024_10_23T08_34_55_badun_images.badun.es_pastelitos_de_chocolate_blanco_y_queso_con_fresas_1290_742.jpg", "Dulces tentaciones", "Termina tu comida con nuestros deliciosos postres caseros.", "Postres.php"]
            ];

            foreach ($categorias as $cat) {
                echo "<div class='col'>
                    <div class='categoria-card'>
                        <a href='{$cat[4]}'>
                            <img src='{$cat[1]}' alt='{$cat[0]}' class='categoria-img' />
                            <div class='categoria-body'>
                                <h3 class='categoria-title'>{$cat[0]}</h3>
                                <div class='categoria-subtitle'>{$cat[2]}</div>
                                <p class='categoria-desc'>{$cat[3]}</p>
                            </div>
                        </a>
                    </div>
                </div>";
            }
            ?>
        </div>

        <!-- Botón Regresar -->
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-danger btn-lg">Regresar al Inicio</a>
        </div>
    </div>
</body>
</html>
