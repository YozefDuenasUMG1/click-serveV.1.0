<?php
session_start();
$_SESSION['nombre_usuario'] = 'Admin'; // Temporal
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Administrador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
  <style>
    body {
      padding-top: 70px;
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
      font-family: 'Segoe UI', sans-serif;
    }

    .admin-panel {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 0;
      position: relative;
      height: 650px; /* suficiente altura para escalera */
    }

    .row-custom {
      position: relative;
      width: 100%;
      display: flex;
      justify-content: center;
      gap: 40px; /* separación horizontal */
    }

    .row-custom.top {
      margin-bottom: 20px;
      margin-top: -40px; /* sube fila superior */
    }

    .row-custom.bottom {
      margin-left: 100px;
      margin-top: 60px; /* baja fila inferior */
    }

    .card {
      border: none;
      border-radius: 16px;
      transition: all 0.3s ease;
      background: #ffffff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      position: relative;
      width: 320px; /* cards más grandes */
      height: 280px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      height: 6px;
      width: 100%;
      background: linear-gradient(to right, #0d6efd, #6610f2);
      transition: all 0.3s ease;
      opacity: 0;
    }

    .card:hover::before {
      opacity: 1;
    }

    .card-body {
      padding: 30px;
      text-align: center;
    }

    .card-title {
      font-size: 1.5rem;
      margin-bottom: 25px;
      color: #343a40;
    }

    .btn {
      border-radius: 8px;
      font-weight: 600;
      padding: 12px 24px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
    }

    .btn-success:hover {
      background-color: #157347;
    }

    .btn-info:hover {
      background-color: #0dcaf0;
    }

    .btn-warning:hover {
      background-color: #ffc107;
      color: #000;
    }

    .btn-secondary:hover {
      background-color: #6c757d;
    }

    .btn-dark:hover {
      background-color: #000;
    }

    h1 {
      font-weight: bold;
      color: #343a40;
      margin-bottom: 50px;
    }

    /* Iconos grandes en cards */
    .card-icon {
      font-size: 48px;
      color: #6610f2;
      margin-bottom: 15px;
      transition: color 0.3s ease;
    }
    .card:hover .card-icon {
      color: #0d6efd;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include '../admin/navbar_admin.php'; ?>
  <br>

  <!-- Panel de administración -->
  <div class="container admin-panel">
    <h1 class="text-center">Panel de Administrador</h1>

    <div class="row-custom top">
      <div class="card">
        <div class="card-body">
          <i class="bi bi-menu-button-wide card-icon"></i>
          <h5 class="card-title">Gestión de Menú</h5>
          <a href="../../restaurante/admin_menu.php" class="btn btn-primary mt-3" target="_blank">Administrar Productos</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <i class="bi bi-basket2 card-icon"></i>
          <h5 class="card-title">Sistema de Pedidos</h5>
          <a href="/click-serveBeta-main/modulos/cliente/index.php" class="btn btn-success mt-3" target="_blank">Ver Menú Cliente</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <i class="bi bi-people card-icon"></i>
          <h5 class="card-title">Gestión de Usuarios</h5>
          <a href="userscontrol.html" class="btn btn-info mt-3" target="_blank">Administrar Usuarios</a>
        </div>
      </div>
    </div>

    <div class="row-custom bottom">
      <div class="card">
        <div class="card-body">
          <i class="bi bi-fire card-icon"></i>
          <h5 class="card-title">Cocina</h5>
          <a href="/click-serveBeta-main/modulos/cocinero/cocinero_panel.php" class="btn btn-warning mt-3" target="_blank">Ver Cocina</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <i class="bi bi-bar-chart-line card-icon"></i>
          <h5 class="card-title">Estadisticas</h5>
          <a href="../../dashboard/analytics/stadistics.html" class="btn btn-secondary mt-3" target="_blank">Ver Estadísticas</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <i class="bi bi-cash-stack card-icon"></i>
          <h5 class="card-title">Módulo de Cajero</h5>
          <a href="/click-serveBeta-main/modulos/cajero/cajeropanel.php" class="btn btn-dark mt-3" target="_blank">Acceder a Cajero</a>
        </div>
      </div>
    </div>

    

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
