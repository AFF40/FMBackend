<?php

require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión

// Crear una instancia de la clase ConexionBase
$conexionBase = new ConexionBase();

// Obtener la conexión
$conn = $conexionBase->getConnection();

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión a la base de datos fallida: " . $conn->connect_error);
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
