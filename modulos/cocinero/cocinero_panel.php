<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Redirección a Cocina</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e0eafc, #cfdef3);
    }

    .card {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 1.5rem;
      padding: 3rem;
      max-width: 400px;
      width: 100%;
      text-align: center;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    }

    .card h2 {
      color: #222;
      font-size: 1.9rem;
      margin-bottom: 1.5rem;
      font-weight: 600;
    }

    .btn {
      padding: 0.9rem 2.4rem;
      font-size: 1.1rem;
      font-weight: 500;
      color: white;
      background: linear-gradient(to right, #4facfe, #00f2fe);
      border: none;
      border-radius: 0.75rem;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 18px rgba(0, 123, 255, 0.4);
    }

    .btn:disabled {
      background: #999;
      box-shadow: none;
      cursor: not-allowed;
    }

    .loading {
      margin-top: 1.5rem;
      font-size: 1rem;
      color: #333;
      display: none;
    }
  </style>
</head>
<body>

  <div class="card">
    <h2>Ir a la Vista de Cocina</h2>
    <button class="btn" id="btnIr">Ir a Cocina</button>
    <div class="loading" id="loadingText">Redirigiendo...</div>
  </div>

  <script>
    const btn = document.getElementById('btnIr');
    const loadingText = document.getElementById('loadingText');

    btn.addEventListener('click', () => {
      btn.disabled = true;
      loadingText.style.display = 'block';

      setTimeout(() => {
        window.location.href = '../../cocina.html';
      }, 1200);
    });
  </script>

</body>
</html>
