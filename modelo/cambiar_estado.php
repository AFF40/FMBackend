<?php
// Conexión a la base de datos (debes completar los detalles de la conexión)
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "androidbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si la conexión a la base de datos fue exitosa
if ($conn->connect_error) {
    die("Error en la conexión a la base de datos: " . $conn->connect_error);
}

// Obtén los parámetros GET
$restaurante_id = $_GET["restaurante_id"];
$estado = $_GET["estado"];
// Realiza la actualización en la base de datos
$sql = "UPDATE restaurantes SET estado = $estado WHERE id_rest = $restaurante_id";

if ($conn->query($sql) === TRUE) {
    echo "Actualización exitosa"; // Esto es opcional, puedes devolver cualquier mensaje que desees
} else {
    echo "Error en la actualización: " . $conn->error; // Manejo de errores si la actualización falla
}

// Cierra la conexión a la base de datos
$conn->close();
?>
