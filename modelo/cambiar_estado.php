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
