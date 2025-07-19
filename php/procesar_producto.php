
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "u666580602_nuna";
$pass = "@JuanJofre5450";
$db   = "u666580602_nunapasteleria";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$img = $_POST['img'];

$stmt = $conn->prepare("INSERT INTO productos (nombre, precio, img) VALUES (?, ?, ?)");
$stmt->bind_param("sds", $nombre, $precio, $img);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../pages/admin.html");
exit();
?>
