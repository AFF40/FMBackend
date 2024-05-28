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

// Comprueba si se proporciona el ID del plato en la solicitud GET
if (isset($_GET['id_comida'])) {
    $id_comida = $_GET['id_comida'];

    // Sentencia SQL para eliminar el plato
    $sql = "DELETE FROM meplat WHERE id_mepla = $id_comida";
    echo $sql;

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
