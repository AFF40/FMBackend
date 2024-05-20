<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "androidbd";

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el id de la solicitud GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    die("ID de restaurante no proporcionado en la URL");
}

$sql = "SELECT imagen FROM restaurantes WHERE id_rest = $id";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagen = $row["imagen"];
} else {
    echo "Imagen no encontrada";
}

if (isset($imagen)) {
    header("Content-Type: image/jpg"); // Cambia el tipo de contenido según el formato de tu imagen
    echo $imagen;
}

$conn->close();
?>
