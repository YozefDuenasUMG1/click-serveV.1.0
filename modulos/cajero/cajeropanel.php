<?php
session_start();
$_SESSION['nombre_usuario'] = 'Cajero'; // Temporal
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Cajero</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
  <style>
    body {
      padding-top: 70px;
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
      font-family: 'Segoe UI', sans-serif;
    }

    .cajero-panel {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px 0;
      text-align: center;
    }

    .btn {
      border-radius: 8px;
      font-weight: 600;
      padding: 12px 24px;
      font-size: 1.2rem;
      margin: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
    }

    .btn-success:hover {
      background-color: #157347;
    }

    h1 {
      font-weight: bold;
      color: #343a40;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include '../admin/navbar_admin.php'; ?>
  <br>

  <!-- Panel de cajero -->
  <div class="container cajero-panel">
    <h1>Panel de Cajero</h1>
    <a href="../../dashboard/analytics/dashboard.html" class="btn btn-primary">Ver Estadísticas</a>
    <a href="/click-serveBeta-main/facturacion/index.php" class="btn btn-success">Facturación</a>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>