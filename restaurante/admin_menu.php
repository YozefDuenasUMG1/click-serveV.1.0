<?php
require_once '../config.php'; // Asegúrate de que la ruta sea correcta

// Obtener categorías e ingredientes para los selects
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
$ingredientes = $pdo->query("SELECT * FROM ingredientes")->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de agregar/editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $categoria_id = $_POST['categoria_id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen_url = $_POST['imagen_url'];
    $ingredientes_seleccionados = $_POST['ingredientes'] ?? [];
    
    if (empty($id)) {
        // Insertar nuevo producto
        $stmt = $pdo->prepare("INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$categoria_id, $nombre, $descripcion, $precio, $imagen_url]);
        $producto_id = $pdo->lastInsertId();
    } else {
        // Actualizar producto existente
        $stmt = $pdo->prepare("UPDATE productos SET categoria_id=?, nombre=?, descripcion=?, precio=?, imagen_url=? WHERE id=?");
        $stmt->execute([$categoria_id, $nombre, $descripcion, $precio, $imagen_url, $id]);
        $producto_id = $id;
        
        // Eliminar ingredientes anteriores
        $pdo->prepare("DELETE FROM producto_ingredientes WHERE producto_id = ?")->execute([$producto_id]);
    }
    
    // Insertar nuevos ingredientes
    foreach ($ingredientes_seleccionados as $ingrediente_id) {
        $pdo->prepare("INSERT INTO producto_ingredientes (producto_id, ingrediente_id) VALUES (?, ?)")
            ->execute([$producto_id, $ingrediente_id]);
    }
    
    header("Location: admin_menu.php");
    exit;
}

// Procesar eliminación de producto
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$id]);
    header("Location: admin_menu.php");
    exit;
}

// Obtener todos los productos para mostrar en la tabla
$productos = $pdo->query("
    SELECT p.*, c.nombre AS categoria_nombre 
    FROM productos p 
    JOIN categorias c ON p.categoria_id = c.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Menú</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       /* Fondo y tipografía */
body {
  background: linear-gradient(135deg, #f0f4ff, #d9e7ff);
  font-family: 'Poppins', sans-serif;
  color: #222;
}

/* Contenedor principal */
.container {
  max-width: 1200px;
  margin: auto;
}

/* Título principal */
h1 {
  font-weight: 900;
  font-size: 3rem;
  color: #3b49df;
  text-align: center;
  margin-bottom: 3rem;
  text-shadow: 1px 1px 5px rgba(59,73,223,0.5);
}

/* Card lista de productos */
.card {
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(59,73,223,0.1);
  background: #fff;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 30px 50px rgba(59,73,223,0.2);
}

/* Card header */
.card-header h3 {
  font-weight: 700;
  color: #3b49df;
  border-bottom: 3px solid #3b49df;
  padding-bottom: 10px;
}

/* Tabla */
.table {
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(59,73,223,0.1);
}
.table thead {
  background: linear-gradient(90deg, #3b49df, #6177ff);
  color: white;
  font-weight: 600;
  font-size: 1rem;
}
.table tbody tr:hover {
  background-color: #f0f4ff;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.table td, .table th {
  vertical-align: middle;
  text-align: center;
  font-weight: 500;
  font-size: 1rem;
}

/* Botones de acción en la tabla */
.btn-sm {
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 6px 14px;
  border-radius: 12px;
  transition: all 0.3s ease;
}
.btn-primary {
  background: linear-gradient(45deg, #3b49df, #6177ff);
  border: none;
  box-shadow: 0 6px 15px rgba(59,73,223,0.3);
}
.btn-primary:hover {
  background: linear-gradient(45deg, #2a37b8, #4250d4);
  box-shadow: 0 10px 25px rgba(42,55,184,0.5);
}
.btn-danger {
  background: linear-gradient(45deg, #df3b3b, #ff6161);
  border: none;
  box-shadow: 0 6px 15px rgba(223,59,59,0.3);
}
.btn-danger:hover {
  background: linear-gradient(45deg, #b82a2a, #d44242);
  box-shadow: 0 10px 25px rgba(184,42,42,0.5);
}

/* Formulario contenedor */
.form-container {
  background: white;
  padding: 30px 25px;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(59,73,223,0.1);
  transition: box-shadow 0.3s ease;
}
.form-container:hover {
  box-shadow: 0 30px 60px rgba(59,73,223,0.15);
}

/* Título formulario */
.form-container h3 {
  color: #3b49df;
  font-weight: 700;
  margin-bottom: 2rem;
  text-align: center;
  letter-spacing: 1.5px;
}

/* Labels */
.form-label {
  font-weight: 600;
  color: #444;
}

/* Inputs, textarea y select */
input[type="text"],
input[type="number"],
textarea,
select {
  border-radius: 12px;
  border: 2px solid #d0d7ff;
  padding: 12px 15px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
  width: 100%;
  box-sizing: border-box;
  color: #222;
  background-color: #f8faff;
  font-weight: 500;
}
input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus,
select:focus {
  border-color: #3b49df;
  outline: none;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(59,73,223,0.2);
}

/* Textarea */
textarea {
  resize: vertical;
}

/* Contenedor ingredientes */
.ingredientes-container {
  max-height: 220px;
  overflow-y: auto;
  border: 1px solid #d0d7ff;
  border-radius: 12px;
  padding: 15px;
  background: #f8faff;
  box-shadow: inset 0 3px 6px rgba(59,73,223,0.05);
}

/* Checkboxes personalizados */
.ingrediente-checkbox {
  appearance: none;
  -webkit-appearance: none;
  width: 22px;
  height: 22px;
  border: 2.5px solid #3b49df;
  border-radius: 50%;
  display: inline-block;
  position: relative;
  margin-right: 12px;
  cursor: pointer;
  vertical-align: middle;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}
.ingrediente-checkbox:checked {
  background: #3b49df;
  border-color: #3b49df;
}
.ingrediente-checkbox:checked::after {
  content: '✓';
  color: white;
  position: absolute;
  top: 1px;
  left: 6px;
  font-size: 16px;
  font-weight: 700;
}

/* Labels para checkbox */
.form-check-label {
  font-weight: 600;
  color: #333;
  user-select: none;
  cursor: pointer;
}

/* Botón submit */
button[type="submit"] {
  background: linear-gradient(45deg, #3b49df, #6177ff);
  border: none;
  padding: 14px;
  font-size: 1.2rem;
  font-weight: 700;
  color: white;
  border-radius: 14px;
  width: 100%;
  cursor: pointer;
  transition: background 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 8px 20px rgba(59,73,223,0.3);
  margin-top: 10px;
}
button[type="submit"]:hover {
  background: linear-gradient(45deg, #2a37b8, #4250d4);
  box-shadow: 0 12px 35px rgba(42,55,184,0.5);
}

/* Botón cancelar */
a.btn-secondary {
  display: block;
  margin-top: 12px;
  font-weight: 700;
  border-radius: 14px;
  padding: 12px 0;
  text-align: center;
  background: #888d9e;
  color: white;
  transition: background 0.3s ease;
  box-shadow: 0 6px 15px rgba(136,141,158,0.3);
}
a.btn-secondary:hover {
  background: #62677f;
  text-decoration: none;
}

/* Scrollbar personalizado para ingredientes */
.ingredientes-container::-webkit-scrollbar {
  width: 8px;
}
.ingredientes-container::-webkit-scrollbar-track {
  background: #f0f4ff;
  border-radius: 12px;
}
.ingredientes-container::-webkit-scrollbar-thumb {
  background: #3b49df;
  border-radius: 12px;
}

    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">Administración de Menú</h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Lista de Productos</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                    <td><?= htmlspecialchars($producto['categoria_nombre']) ?></td>
                                    <td>Q<?= number_format($producto['precio'], 2) ?></td>
                                    <td>
                                        <a href="?edit=<?= $producto['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="?delete=<?= $producto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-container">
                    <h3><?= isset($_GET['edit']) ? 'Editar Producto' : 'Agregar Producto' ?></h3>
                    
                    <?php
                    $producto_editar = null;
                    $ingredientes_producto = [];
                    
                    if (isset($_GET['edit'])) {
                        $id = $_GET['edit'];
                        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                        $stmt->execute([$id]);
                        $producto_editar = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($producto_editar) {
                            $stmt = $pdo->prepare("
                                SELECT ingrediente_id 
                                FROM producto_ingredientes 
                                WHERE producto_id = ?
                            ");
                            $stmt->execute([$id]);
                            $ingredientes_producto = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        }
                    }
                    ?>
                    
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $producto_editar['id'] ?? '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" 
                                    <?= ($producto_editar['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?= htmlspecialchars($producto_editar['nombre'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($producto_editar['descripcion'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Precio (Q)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" 
                                   value="<?= $producto_editar['precio'] ?? '' ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL de la Imagen</label>
                            <input type="text" name="imagen_url" class="form-control" 
                                   value="<?= htmlspecialchars($producto_editar['imagen_url'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ingredientes</label>
                            <div class="ingredientes-container">
                                <?php foreach ($ingredientes as $ingrediente): ?>
                                <div class="form-check">
                                    <input class="ingrediente-checkbox" type="checkbox" name="ingredientes[]" 
                                           value="<?= $ingrediente['id'] ?>" id="ing<?= $ingrediente['id'] ?>"
                                           <?= in_array($ingrediente['id'], $ingredientes_producto) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ing<?= $ingrediente['id'] ?>">
                                        <?= htmlspecialchars($ingrediente['nombre']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <?= isset($_GET['edit']) ? 'Actualizar Producto' : 'Agregar Producto' ?>
                        </button>
                        
                        <?php if (isset($_GET['edit'])): ?>
                        <a href="admin_menu.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>