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
$sql_menu = "SELECT id_menu FROM menus WHERE id_rest = ?";
$stmt_menu = $conn->prepare($sql_menu);
$stmt_menu->bind_param("i", $id_rest);
$stmt_menu->execute();
$result_menu = $stmt_menu->get_result();

if ($result_menu->num_rows > 0) {
    $row_menu = $result_menu->fetch_assoc();
    $id_menu = $row_menu['id_menu'];

    // Verificar si el nombre de la bebida ya existe en la tabla productos
    $sql_producto = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_producto = $conn->prepare($sql_producto);
    $stmt_producto->bind_param("s", $nombre_bebida);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();

    if ($result_producto->num_rows > 0) {
        // El nombre de la bebida ya existe en productos, usar id_producto existente
        $row_producto = $result_producto->fetch_assoc();
        $id_producto = $row_producto['id_producto'];
    } else {
        // Insertar el nuevo nombre_bebida en la tabla productos
        $insert_sql_productos = "INSERT INTO productos (nombre, created_at, updated_at) VALUES (?, NOW(), NOW())";
        $stmt_insert_productos = $conn->prepare($insert_sql_productos);
        $stmt_insert_productos->bind_param("s", $nombre_bebida);
        if ($stmt_insert_productos->execute() === TRUE) {
            // Obtener el id_producto recién insertado
            $id_producto = $conn->insert_id;
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al insertar la nueva bebida en la tabla productos: " . $conn->error));
            exit;
        }
        $stmt_insert_productos->close();
    }
    $stmt_producto->close();

    // Obtener el id_mebeb de la tabla mebeb usando los datos POST
    $sql_mebeb = "SELECT id_mebeb FROM mebeb WHERE descripcion = ? AND id_menu = ?";
    $stmt_mebeb = $conn->prepare($sql_mebeb);
    $stmt_mebeb->bind_param("si", $descripcion_bebida, $id_menu);
    $stmt_mebeb->execute();
    $result_mebeb = $stmt_mebeb->get_result();

    if ($result_mebeb->num_rows > 0) {
        $row_mebeb = $result_mebeb->fetch_assoc();
        $id_mebeb = $row_mebeb['id_mebeb'];

        // Actualizar los detalles de la bebida en la tabla mebeb
        $update_sql_mebeb = "UPDATE mebeb SET id_producto = ?, descripcion = ?, precio = ?, id_menu = ?, updated_at = NOW() WHERE id_mebeb = ?";
        $stmt_update_mebeb = $conn->prepare($update_sql_mebeb);
        $stmt_update_mebeb->bind_param("isdii", $id_producto, $descripcion_bebida, $precio_bebida, $id_menu, $id_mebeb);
        if ($stmt_update_mebeb->execute() === TRUE) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al actualizar los detalles de la bebida: " . $conn->error));
        }
        $stmt_update_mebeb->close();
    } else {
        echo json_encode(array("success" => false, "error_message" => "No se encontró el id_mebeb correspondiente en la tabla mebeb para los datos proporcionados"));
    }
    $stmt_mebeb->close();
} else {
    echo json_encode(array("success" => false, "error_message" => "No se encontró el id_menu para el id_rest proporcionado"));
}
$stmt_menu->close();

// Cerrar la conexión
$conn->close();
?>
