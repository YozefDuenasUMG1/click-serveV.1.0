<?php
// Iniciamos la sesión
session_start();

// Importamos la configuración
require_once __DIR__ . '/../config.php';

// Variables para el formulario
$error = '';
$success = '';
$usuario = '';
$rol = '';

// Solo los administradores pueden crear usuarios (excepto si es la instalación inicial)
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
$isInitialSetup = !tableHasUsers(); // Función para verificar si ya hay usuarios en la tabla

// Procesamos el formulario de registro
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Desactiva temporalmente la validación del token CSRF para pruebas
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     $error = "Error de seguridad. Por favor, intente nuevamente.";
    // } else {
        // Capturamos y sanitizamos los datos del formulario
        $usuario = sanitizeInput($_POST['usuario']);
        $password = $_POST['password'];
        $confirmarPassword = $_POST['confirmar_password'];
        
        // Asegura que el rol se registre correctamente como 'cliente' si no se selecciona otro rol
        $rol = 'cliente';
        
        // Verifica que el campo 'rol' no esté vacío antes de la inserción
        if (empty($rol)) {
            $error = "Error: El rol no puede estar vacío.";
            header("Location: ../login.html?error=" . urlencode($error));
            exit();
        }
        
        // Validación de datos
        if (empty($usuario) || empty($password) || empty($confirmarPassword)) {
            $error = "Todos los campos son obligatorios";
        } elseif (strlen($usuario) < 4) {
            $error = "El nombre de usuario debe tener al menos 4 caracteres";
        } elseif (strlen($password) < 8) {
            $error = "La contraseña debe tener al menos 8 caracteres";
        } elseif ($password !== $confirmarPassword) {
            $error = "Las contraseñas no coinciden";
        } else {
            // Verificamos si el usuario ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "El nombre de usuario ya está registrado";
            } else {
                // Hash de la contraseña
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Ajustamos el registro para que coincida con la estructura de la tabla `usuarios`
                $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol, creado_en) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $usuario, $passwordHash, $rol);
                
                if ($stmt->execute()) {
                    $success = "¡Usuario registrado correctamente!";
                    
                    // Si es la instalación inicial, el primer usuario será admin
                    if ($isInitialSetup) {
                        // Actualizamos el rol a admin
                        $userId = $conn->insert_id;
                        $adminRole = 'admin';
                        $updateStmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                        $updateStmt->bind_param("si", $adminRole, $userId);
                        $updateStmt->execute();
                        
                        $success .= " Se ha configurado como administrador por ser el primer usuario.";
                    }
                    
                    // Limpiamos los campos del formulario
                    $usuario = $rol = '';

                    // Redirigir al login después de un registro exitoso con mensaje de éxito
                    header("Location: ../login.html?success=" . urlencode($success));
                    exit();
                } else {
                    $error = "Error al registrar el usuario: " . $stmt->error;
                }
            }
        }
    // }
}

// Generamos un nuevo token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Función para verificar si ya hay usuarios en la tabla
function tableHasUsers() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $data = $result->fetch_assoc();
    return $data['total'] > 0;
}

// Función para sanitizar entradas del usuario
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Obtenemos la lista de roles disponibles
$roles = ['admin', 'mesero', 'cocinero', 'cajero'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - RestaurantTech</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
     <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563EB',  // Azul neutro (blue-600)
            primaryLight: '#3B82F6', // Azul claro (blue-500)
            primaryDark: '#1E40AF',  // Azul oscuro (blue-800)
            background: '#F3F4F6', // Gris claro para fondo (gray-100)
          },
          fontFamily: {
            display: ['Playfair Display', 'serif'],
            sans: ['Lato', 'sans-serif'],
          },
        },
      },
    }
  </script>
</head>

<body class="bg-background font-sans min-h-screen flex items-center justify-center p-6">
  <div class="w-full max-w-2xl">
    <div class="bg-white border border-blue-200 rounded-2xl shadow-xl p-10">
      <!-- Título -->
      <div class="text-center mb-10">
        <h1 class="text-5xl font-display font-bold text-primaryDark drop-shadow-sm">🍽️ Click&serve</h1>
        <p class="text-blue-600 mt-2 text-lg"><?= $isInitialSetup ? 'Configuración Inicial - Crear Administrador' : 'Registro de Nuevo Usuario' ?></p>
      </div>

      <!-- Mensajes de error y éxito (igual) -->

      <!-- Formulario -->
      <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="registroForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Usuario -->
          <div>
            <label for="usuario" class="block text-blue-700 text-lg font-semibold mb-3">Usuario</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-300">
                <!-- Icono -->
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.474M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
                </svg>
              </span>
              <input type="text" name="usuario" id="usuario" required
                value="<?= htmlspecialchars($usuario) ?>"
                class="w-full pl-14 pr-5 py-4 text-lg border border-blue-300 rounded-lg focus:outline-none focus:ring-3 focus:ring-primaryLight transition" />
            </div>
          </div>

          <!-- Contraseña -->
          <div>
            <label for="password" class="block text-blue-700 text-lg font-semibold mb-3">Contraseña</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-300">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3m12 0c0-1.657-1.343-3-3-3s-3 1.343-3 3m0 4v4" />
                </svg>
              </span>
              <input type="password" name="password" id="password" required
                class="w-full pl-14 pr-5 py-4 text-lg border border-blue-300 rounded-lg focus:outline-none focus:ring-3 focus:ring-primaryLight transition" />
            </div>
            <p class="text-blue-400 mt-1 text-sm">Mínimo 8 caracteres</p>
          </div>

          <!-- Confirmar contraseña -->
          <div>
            <label for="confirmar_password" class="block text-blue-700 text-lg font-semibold mb-3">Confirmar Contraseña</label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-300">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3m12 0c0-1.657-1.343-3-3-3s-3 1.343-3 3m0 4v4" />
                </svg>
              </span>
              <input type="password" name="confirmar_password" id="confirmar_password" required
                class="w-full pl-14 pr-5 py-4 text-lg border border-blue-300 rounded-lg focus:outline-none focus:ring-3 focus:ring-primaryLight transition" />
            </div>
          </div>

          <!-- Rol (oculto) -->
          <div>
            <label class="block text-blue-700 text-lg font-semibold mb-3">Rol</label>
            <input type="text" disabled value="Cliente"
              class="w-full px-5 py-4 bg-blue-50 border border-blue-200 text-blue-400 rounded-lg cursor-not-allowed text-lg" />
            <input type="hidden" name="rol" value="cliente" />
          </div>
        </div>

        <!-- Botones -->
        <div class="flex flex-col sm:flex-row justify-between items-center mt-10 gap-6">
          <a href="../login.html"
            class="w-full sm:w-auto text-center bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold py-4 px-10 rounded-lg transition text-lg">
            Cancelar
          </a>

          <button type="submit"
            class="w-full sm:w-auto bg-primary hover:bg-primaryDark text-white font-semibold py-4 px-10 rounded-lg transition transform active:scale-95 text-lg">
            <?= $isInitialSetup ? 'Crear Administrador' : 'Registrar Usuario' ?>
          </button>
        </div>
      </form>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 text-blue-400 text-sm">
      &copy; <?= date('Y') ?> RestaurantTech
    </div>
  </div>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registroForm');
            
            form.addEventListener('submit', function(e) {
                const usuario = document.getElementById('usuario');
                const password = document.getElementById('password');
                const confirmarPassword = document.getElementById('confirmar_password');
                
                // Validación básica
                if (usuario.value.trim().length < 4) {
                    e.preventDefault();
                    alert('El nombre de usuario debe tener al menos 4 caracteres');
                    usuario.focus();
                    return false;
                }
                
                if (password.value.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres');
                    password.focus();
                    return false;
                }
                
                if (password.value !== confirmarPassword.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    confirmarPassword.focus();
                    return false;
                }
            });
            
            // Validación en vivo para las contraseñas
            const password = document.getElementById('password');
            const confirmarPassword = document.getElementById('confirmar_password');
            
            function validarCoincidencia() {
                if (confirmarPassword.value === '') {
                    confirmarPassword.style.borderColor = '';
                    return;
                }
                
                if (password.value === confirmarPassword.value) {
                    confirmarPassword.parentElement.style.borderColor = '#22c55e'; // Verde para coincidencia
                } else {
                    confirmarPassword.parentElement.style.borderColor = '#ef4444'; // Rojo para no coincidencia
                }
            }
            
            password.addEventListener('keyup', validarCoincidencia);
            confirmarPassword.addEventListener('keyup', validarCoincidencia);
        });
    </script>
</body>
</html>