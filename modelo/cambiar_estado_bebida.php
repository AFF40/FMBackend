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
if (isset($_GET['id_bebida'])) {
    $id_bebida = $_GET['id_bebida'];

    // Consulta para obtener el valor actual de "disponible"
    $selectQuery = "SELECT disponible FROM mebeb WHERE id_mebeb = $id_bebida";
    
    $result = $conn->query($selectQuery);

    if ($result) {
        $row = $result->fetch_assoc();
        $disponible = $row['disponible'];

        // Actualiza el estado del plato
        if ($disponible == 1) {
            $updateQuery = "UPDATE mebeb SET disponible = 0 WHERE id_mebeb = $id_bebida ";
        } else {
            $updateQuery = "UPDATE mebeb SET disponible = 1 WHERE id_mebeb = $id_bebida ";
        }

        if ($conn->query($updateQuery) === TRUE) {
            echo "El estado de la bebida se actualizó correctamente.";
        } else {
            echo "Error al actualizar el estado de la bebida: " . $conn->error;
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
