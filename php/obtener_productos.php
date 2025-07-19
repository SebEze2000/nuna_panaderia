
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "u666580602_nuna";
$pass = "@JuanJofre5450";
$db   = "u666580602_nunapasteleria";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

$resultado = $conn->query("SELECT * FROM productos");
$productos = [];

while($fila = $resultado->fetch_assoc()) {
  $productos[] = $fila;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($productos);
?>
