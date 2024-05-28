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
if (isset($_GET['id_bebida'])) {
    $id_bebida = $_GET['id_bebida'];

    // Sentencia SQL para eliminar los registros relacionados en la tabla menu_bebidas
    $sql_delete_menu_bebidas = "DELETE FROM mebeb WHERE id_mebeb = $id_bebida";

    if ($conn->query($sql_delete_menu_bebidas) === TRUE) {
        // Ahora que los registros relacionados en menu_bebidas han sido eliminados, procedemos con la eliminación de la bebida en sí
        $sql_delete_bebida = "DELETE FROM mebeb WHERE id_mebeb = $id_bebida";

        if ($conn->query($sql_delete_bebida) === TRUE) {
            echo "success"; // Eliminación exitosa
        } else {
            echo "error al eliminar la bebida"; // Error al eliminar la bebida
        }
    } else {
        echo "error al eliminar los registros relacionados en menu_bebidas"; // Error al eliminar los registros relacionados en menu_bebidas
    }
} else {
    echo "ID de la bebida no proporcionado"; // No se proporcionó el ID de la bebida en la solicitud
}

// Cierra la conexión a la base de datos
$conn->close();
?>
