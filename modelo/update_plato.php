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

$id_meplat = $data['id_meplat']; // id_producto recibido desde la app
$nombre_plato = $data['nombre_plato'];
$descripcion_plato = $data['descripcion_plato'];
$precio_plato = $data['precio_plato'];
$imagen_plato = $data['imagen_plato'];
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

    // Verificar si el nombre del plato ya existe en la tabla productos
    $sql_producto = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_producto = $conn->prepare($sql_producto);
    $stmt_producto->bind_param("s", $nombre_plato);
    $stmt_producto->execute();
    $result_producto = $stmt_producto->get_result();

    if ($result_producto->num_rows > 0) {
        // El nombre del plato ya existe en productos, usar id_producto existente
        $row_producto = $result_producto->fetch_assoc();
        $id_producto = $row_producto['id_producto'];
    } else {
        // Insertar el nuevo nombre_plato en la tabla productos
        $insert_sql_productos = "INSERT INTO productos (nombre, created_at, updated_at) VALUES (?, NOW() , NOW() )";
        $stmt_insert_productos = $conn->prepare($insert_sql_productos);
        $stmt_insert_productos->bind_param("s", $nombre_plato);
        if ($stmt_insert_productos->execute() === TRUE) {
            // Obtener el id_producto recién insertado del row
            $id_producto = $conn->insert_id;
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al insertar el nuevo plato en la tabla productos: " . $conn->error));
            exit;
        }
        $stmt_insert_productos->close();
    }
    $stmt_producto->close();

    // Obtener el id_meplat de la tabla meplat usando los datos POST
    $sql_meplat = "SELECT id_meplat FROM meplat WHERE descripcion = ? AND id_menu = ?";
    $stmt_meplat = $conn->prepare($sql_meplat);
    $stmt_meplat->bind_param("si", $descripcion_plato, $id_menu);
    $stmt_meplat->execute();
    $result_meplat = $stmt_meplat->get_result();

    if ($result_meplat->num_rows > 0) {
        $row_meplat = $result_meplat->fetch_assoc();
        $id_meplat = $row_meplat['id_meplat'];

        // Actualizar los detalles del plato en la tabla meplat
        $update_sql_meplat = "UPDATE meplat SET id_producto = ?, descripcion = ?, precio = ?, id_menu = ?, updated_at = NOW() WHERE id_meplat = ?";
        $stmt_update_meplat = $conn->prepare($update_sql_meplat);
        $stmt_update_meplat->bind_param("isdii", $id_producto, $descripcion_plato, $precio_plato, $id_menu, $id_meplat);
        if ($stmt_update_meplat->execute() === TRUE) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "error_message" => "Error al actualizar los detalles del plato: " . $conn->error));
        }
        $stmt_update_meplat->close();
    } else {
        echo json_encode(array("success" => false, "error_message" => "No se encontró el id_meplat correspondiente en la tabla meplat para los datos proporcionados"));
    }
    $stmt_meplat->close();
} else {
    echo json_encode(array("success" => false, "error_message" => "No se encontró el id_menu para el id_rest proporcionado"));
}
$stmt_menu->close();

// Cerrar la conexión
$conn->close();
?>
