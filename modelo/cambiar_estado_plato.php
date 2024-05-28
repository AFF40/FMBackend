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

// Obtiene el ID del plato desde la solicitud GET
if (isset($_GET['id_comida'])) {
    $id_comida = $_GET['id_comida'];

    // Consulta para obtener el valor actual de "disponible"
    $selectQuery = "SELECT disponible FROM meplat WHERE id_producto = $id_comida"; 
    
    $result = $conn->query($selectQuery);

    if ($result) {
        $row = $result->fetch_assoc();
        $disponible = $row['disponible'];

        // Actualiza el estado del plato
        if ($disponible == 1) {
            $updateQuery = "UPDATE platos SET meplat = 0 WHERE id_producto = $id_comida ";
        } else {
            $updateQuery = "UPDATE platos SET meplat = 1 WHERE id_producto = $id_comida ";
        }

        if ($conn->query($updateQuery) === TRUE) {
            echo "El estado del plato se actualizó correctamente.";
        } else {
            echo "Error al actualizar el estado del plato: " . $conn->error;
        }
    } else {
        echo "Error al obtener el valor de 'disponible': " . $conn->error;
    }
} else {
    echo "ID del plato no proporcionado en la solicitud.";
}

// Cierra la conexión a la base de datos
$conn->close();
?>
