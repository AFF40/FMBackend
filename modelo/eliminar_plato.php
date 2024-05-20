<?php
// Configuración de la base de datos (ajústala según tus necesidades)
$servername = "localhost";
$username = "root";
$password = "";
$database = "androidbd";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprueba si se proporciona el ID del plato en la solicitud GET
if (isset($_GET['id_comida'])) {
    $id_comida = $_GET['id_comida'];

    // Sentencia SQL para eliminar el plato
    $sql = "DELETE FROM platos WHERE id_comida = $id_comida";

    if ($conn->query($sql) === TRUE) {
        echo "success"; // Eliminación exitosa
    } else {
        echo "error"; // Error al eliminar el plato
    }
} else {
    echo "ID del plato no proporcionado"; // No se proporcionó el ID del plato en la solicitud
}

// Cierra la conexión a la base de datos
$conn->close();
?>
