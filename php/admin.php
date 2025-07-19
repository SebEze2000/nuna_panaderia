<?php
$host = "localhost";
$user = "u666580602_nuna";
$pass = "@JuanJofre5450";
$db   = "u666580602_nunapasteleria";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// AGREGAR PRODUCTO
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['accion'] == 'agregar') {
  $nombre = $_POST['nombre'];
  $precio = $_POST['precio'];

  // Procesar imagen
  if ($_FILES['imagen']['error'] == 0) {
    $imagenNombre = uniqid() . "_" . basename($_FILES['imagen']['name']);
    $destino = "uploads/" . $imagenNombre;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
  } else {
    $imagenNombre = ""; // Sin imagen
  }

  $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, img) VALUES (?, ?, ?)");
  $stmt->bind_param("sds", $nombre, $precio, $imagenNombre);
  $stmt->execute();
  $stmt->close();

  header("Location: admin.php");
  exit();
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);

  // Eliminar imagen física
  $res = $conn->query("SELECT img FROM productos WHERE id = $id");
  $imgRow = $res->fetch_assoc();
  if ($imgRow && !empty($imgRow['img']) && file_exists("uploads/" . $imgRow['img'])) {
    unlink("uploads/" . $imgRow['img']);
  }

  $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  header("Location: admin.php");
  exit();
}

// EDITAR
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['accion'] == 'editar') {
  $id = intval($_POST['id']);
  $nombre = $_POST['nombre'];
  $precio = $_POST['precio'];
  $imagenNombre = $_POST['imagen_actual'];

  if ($_FILES['imagen']['error'] == 0) {
    // Eliminar anterior
    if (!empty($imagenNombre) && file_exists("uploads/" . $imagenNombre)) {
      unlink("uploads/" . $imagenNombre);
    }

    // Subir nueva
    $imagenNombre = uniqid() . "_" . basename($_FILES['imagen']['name']);
    move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/" . $imagenNombre);
  }

  $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, img=? WHERE id=?");
  $stmt->bind_param("sdsi", $nombre, $precio, $imagenNombre, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: admin.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administrador - Nuna Pastelería</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
   <style>
    /* ==== RESETEO ==== */
    * {
      margin: 0;
      margin-top: 5px;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      height: 100%;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #faf7f7;
      color: #333;
      padding: 20px;
    }

    h1 {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 10px;
      color: #ff7ca8;
    }

    h2 {
      font-size: 1.4rem;
      color: #444;
      margin-bottom: 15px;
      text-align: center;
    }

    a.volver {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      color: #ff7ca8;
      font-weight: bold;
    }

    /* ==== FORMULARIOS ==== */
    form {
      background: #fff;
      padding: 10px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      max-width: 500px;
      width: 100%;
      margin: 0 auto 30px auto;
      text-align: center;
    }

    form input,
    form button {
      width: 90%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 10px;
      font-size: 1rem;
      border: 1px solid #ddd;
    }

    form input:focus {
      outline: none;
      border-color: #ff7ca8;
      box-shadow: 0 0 5px rgba(255, 124, 168, 0.4);
    }

    form button {
      background: #ff7ca8;
      color: #fff;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    form button:hover {
      background: #ff4f8b;
    }

    /* ==== GRID PRODUCTOS ==== */
    .productos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .producto {
      background: #fff;
      padding: 15px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .producto:hover {
      transform: translateY(-5px);
    }

    .producto img {
      max-width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .producto strong {
      font-size: 1.2rem;
      color: #333;
      display: block;
      margin-bottom: 5px;
    }

    .producto p {
      font-size: 1rem;
      color: #777;
      margin-bottom: 15px;
    }

    .acciones {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .acciones a,
    .acciones span {
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 8px;
      transition: 0.3s;
    }

    .acciones .eliminar {
      color: #fff;
      background: #ff4b4b;
    }

    .acciones .eliminar:hover {
      background: #e63c3c;
    }

    .acciones .editar {
      color: #fff;
      background: #ff7ca8;
    }

    .acciones .editar:hover {
      background: #ff4f8b;
    }

    /* ==== FORM EDITAR ==== */
    #form-editar {
      display: none;
      margin-top: 30px;
    }

    /* ==== RESPONSIVE ==== */
    @media (max-width: 768px) {
      body {
        padding: 0;
      }

      form {
        max-width: none;
        width: 100vw;            /* ocupa todo el ancho del viewport */
        border-radius: 0;
        padding: 20px;
      }

      h1 {
        font-size: 1.5rem;
      }

      h2 {
        font-size: 1.2rem;
      }

      .productos-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }

      .producto img {
        height: 150px;
      }

      .acciones {
        flex-direction: column;
        gap: 10px;
      }

      .acciones a,
      .acciones span {
        width: 100%;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.3rem;
      }

      form input,
      form button {
        font-size: 0.9rem;
        padding: 10px;
      }

      .producto img {
        height: 120px;
      }
    }
  </style>

  <script>
    function editarProducto(id, nombre, precio, img) {
      document.getElementById("form-editar").style.display = "block";
      document.getElementById("editar-id").value = id;
      document.getElementById("editar-nombre").value = nombre;
      document.getElementById("editar-precio").value = precio;
      document.getElementById("imagen-actual").value = img;
      window.scrollTo(0, document.body.scrollHeight);
    }
  </script>
</head>
<body>
  <a href="../index.html" class="volver">← Volver al inicio</a>
  <h1>Panel de Administrador</h1>

  <h2>Agregar nuevo producto</h2>
  <form method="POST" action="admin.php" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="agregar">
    <input type="text" name="nombre" placeholder="Nombre del producto" required>
    <input type="number" name="precio" placeholder="Precio" step="0.01" min="0" required>
    <input type="file" name="imagen" accept="image/*" required>
    <button type="submit">Agregar producto</button>
  </form>

  <h2>Productos actuales</h2>
  <div class="productos-grid">
    <?php
    $res = $conn->query("SELECT * FROM productos ORDER BY id DESC");
    while ($row = $res->fetch_assoc()) {
      echo "<div class='producto'>";
      if (!empty($row['img'])) {
        echo "<img src='uploads/{$row['img']}' alt='imagen'>";
      }
      echo "<strong>{$row['nombre']}</strong>";
      echo "<p>\$ {$row['precio']}</p>";
      echo "<div class='acciones'>";
      echo "<a class='eliminar' href='admin.php?eliminar={$row['id']}' onclick=\"return confirm('¿Eliminar este producto?')\">Eliminar</a>";
      echo "<span class='editar' onclick=\"editarProducto('{$row['id']}', '".htmlspecialchars($row['nombre'])."', '{$row['precio']}', '{$row['img']}')\">Editar</span>";
      echo "</div></div>";
    }
    ?>
  </div>

  <!-- Formulario Editar -->
  <div id="form-editar">
    <h2>Editar producto</h2>
    <form method="POST" action="admin.php" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" id="editar-id" name="id">
      <input type="hidden" id="imagen-actual" name="imagen_actual">
      <input type="text" id="editar-nombre" name="nombre" placeholder="Nombre del producto" required>
      <input type="number" id="editar-precio" name="precio" placeholder="Precio" step="0.01" min="0" required>
      <input type="file" name="imagen" accept="image/*">
      <button type="submit">Guardar cambios</button>
    </form>
  </div>
</body>
</html>
