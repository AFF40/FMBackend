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
if (isset($_GET['id_bebida'])) {
    $id_bebida = $_GET['id_bebida'];

    // Sentencia SQL para eliminar el plato
    $sql = "DELETE FROM bebidas WHERE id_bebida = $id_bebida";

    if ($conn->query($sql) === TRUE) {
        echo "success"; // Eliminación exitosa
    } else {
        echo "error"; // Error al eliminar el plato
    }
} else {
    echo "ID de la bebida no proporcionado"; // No se proporcionó el ID del plato en la solicitud
}

// Cierra la conexión a la base de datos
$conn->close();
?>
