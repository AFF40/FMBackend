<?php
require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión

// Crear una instancia de la clase ConexionBase
$conexionBase = new ConexionBase();

// Obtener la conexión
$conn = $conexionBase->getConnection();

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(array("success" => false, "error_message" => "Conexión a la base de datos fallida: " . $conn->connect_error)));
}

// Obtener los datos enviados desde la aplicación Android
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(array("success" => false, "error_message" => "Datos no válidos"));
    exit;
}

$id_mebeb = $data['id_mebeb']; // id_producto recibido desde la app
$nombre_bebida = $data['nombre_bebida'];
$descripcion_bebida = $data['descripcion_bebida'];
$precio_bebida = $data['precio_bebida'];
$imagen_bebida = $data['imagen_bebida'];
$id_rest = $data['restaurante_id']; // Asumiendo que id_rest está presente en los datos enviados

// Obtener el id_menu de la tabla menus utilizando el id_rest
$sql_menu = "SELECT id_menu FROM menus WHERE id_rest = $id_rest";
$result_menu = $conn->query($sql_menu);

if ($result_menu->num_rows > 0) {
    $row_menu = $result_menu->fetch_assoc();
    $id_menu = $row_menu['id_menu'];

    // Verificar si el nombre de la bebida ya existe en la tabla productos
    $sql_producto = "SELECT id_producto FROM productos WHERE nombre = '$nombre_bebida'";
    $result_producto = $conn->query($sql_producto);

    if ($result_producto->num_rows > 0) {
        // El nombre de la bebida ya existe en productos, usar id_producto existente
        $row_producto = $result_producto->fetch_assoc();
        $id_producto = $row_producto['id_producto'];
    } else {
        // Insertar el nuevo nombre_bebida en la tabla productos
        $insert_sql_productos = "INSERT INTO productos (nombre, created_at, updated_at) VALUES ('$nombre_bebida', NOW(), NOW())";
        if ($conn->query($insert_sql_productos) === TRUE) {
            // Obtener el id_producto recién insertado del row  
            $id_producto = $conn->insert_id;     
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al insertar la nueva bebida en la tabla productos: " . $conn->error));
            exit;
        }
    }

    // Volver a consultar para obtener el id_producto recién insertado
    $sql_producto_nuevo = "SELECT id_producto FROM productos WHERE nombre = '$nombre_bebida'";
    $result_producto_nuevo = $conn->query($sql_producto_nuevo);

    if ($result_producto_nuevo->num_rows > 0) {
        $row_producto_nuevo = $result_producto_nuevo->fetch_assoc();
        $id_productonuevo = $row_producto_nuevo['id_producto'];
    }
    // Obtener el id_mebeb de la tabla mebeb usando los datos POST
    $sql_mebeb = "SELECT id_mebeb FROM mebeb WHERE descripcion = '$descripcion_bebida' AND id_menu = $id_menu ";
    $result_mebeb = $conn->query($sql_mebeb);

    if ($result_mebeb->num_rows > 0) {
        $row_mebeb = $result_mebeb->fetch_assoc();
        $id_mebeb = $row_mebeb['id_mebeb'];

        // Actualizar los detalles de la bebida en la tabla mebeb
        $update_sql_mebeb = "UPDATE mebeb SET id_producto = $id_producto, descripcion = '$descripcion_bebida', precio = $precio_bebida, id_menu = $id_menu, updated_at = NOW() WHERE id_mebeb = $id_mebeb";
        if ($conn->query($update_sql_mebeb) === TRUE) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al actualizar los detalles de la bebida: " . $conn->error));
        }
    } else {
        echo json_encode(array("success" => false, "error_message" => "No se encontró el id_mebeb correspondiente en la tabla mebeb para los datos proporcionados"));
    }
} else {
    echo json_encode(array("success" => false, "error_message" => "No se encontró el id_menu para el id_rest proporcionado"));
}

// Cerrar la conexión
$conn->close();
?>
