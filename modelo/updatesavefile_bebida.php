<?php
/*
require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión

// Establecer la cabecera de contenido como JSON
header('Content-Type: application/json');

// Verificar que la solicitud sea un POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error_message" => "Error: Este script solo acepta solicitudes POST."]);
    exit;
}

// Leer los datos JSON del cuerpo de la solicitud
$json_data = file_get_contents('php://input');
error_log("JSON Data: " . $json_data); // Línea de depuración

// Verificar si la cadena JSON está vacía
if (empty($json_data)) {
    echo json_encode(["success" => false, "error_message" => "Error: No se recibieron datos JSON."]);
    exit;
}

// Decodificar los datos JSON en un arreglo asociativo
$data = json_decode($json_data, true);
error_log("Decoded Data: " . print_r($data, true)); // Línea de depuración

// Verificar si se pudo decodificar el JSON correctamente
if (is_null($data)) {
    echo json_encode(["success" => false, "error_message" => "Error: No se pudieron decodificar los datos JSON."]);
    exit;
}

// Recuperar los valores del arreglo
$bebida_id = isset($data["bebida_id"]) ? intval($data["bebida_id"]) : 0;
$nombre = isset($data["nombre_bebida"]) ? htmlspecialchars($data["nombre_bebida"]) : "";
$descripcion = isset($data["descripcion_bebida"]) ? htmlspecialchars($data["descripcion_bebida"]) : "";
$precio = isset($data["precio_bebida"]) ? floatval($data["precio_bebida"]) : 0;
$imagen_base64 = isset($data["imagen_bebida"]) ? $data["imagen_bebida"] : "";
$restaurante_nombre = isset($data["nombre_restaurante"]) ? htmlspecialchars($data["nombre_restaurante"]) : "";

// Verificar los datos recibidos
if ($bebida_id === 0) {
    echo json_encode(["success" => false, "error_message" => "Error: ID de bebida no proporcionado."]);
    exit;
}
if (empty($nombre)) {
    echo json_encode(["success" => false, "error_message" => "Error: Nombre de la bebida no proporcionado."]);
    exit;
}
if (empty($descripcion)) {
    echo json_encode(["success" => false, "error_message" => "Error: Descripción de la bebida no proporcionada."]);
    exit;
}
if ($precio === 0) {
    echo json_encode(["success" => false, "error_message" => "Error: Precio de la bebida no proporcionado."]);
    exit;
}
if (empty($imagen_base64)) {
    echo json_encode(["success" => false, "error_message" => "Error: Imagen de la bebida no proporcionada."]);
    exit;
}
if (empty($restaurante_nombre)) {
    echo json_encode(["success" => false, "error_message" => "Error: Nombre del restaurante no proporcionado."]);
    exit;
}

// Decodificar la imagen Base64
$imagen = base64_decode($imagen_base64);

// Verificar si la imagen se decodificó correctamente
if ($imagen === false) {
    echo json_encode(["success" => false, "error_message" => "Error: La imagen proporcionada no es válida."]);
    exit;
}

// Ruta donde se guardará la imagen
$ruta_imagen = "/foodmapsBD/restaurantes/" . preg_replace('/[^A-Za-z0-9\-]/', '', $restaurante_nombre) . "/bebidas/";

// Verificar si la carpeta existe, si no existe, crearla
$ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $ruta_imagen;
if (!file_exists($ruta_completa) && !mkdir($ruta_completa, 0777, true)) {
    echo json_encode(["success" => false, "error_message" => "Error: No se pudo crear la carpeta de la imagen."]);
    exit;
}

// Nombre del archivo de imagen (bebida_"numero_asendente")
$nombre_imagen = "bebida_" . uniqid() . ".jpg";
$ruta_completa_imagen = $ruta_imagen . $nombre_imagen;

// Guardar la imagen en el servidor
if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . $ruta_completa_imagen, $imagen)) {
    echo json_encode(["success" => false, "error_message" => "Error: No se pudo guardar la imagen en el servidor."]);
    exit;
}

// Conexión a la base de datos
require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión
$conexionBase = new ConexionBase();
$mysqli = $conexionBase->getConnection();

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "error_message" => "Error: Conexión fallida: " . $mysqli->connect_error]);
    exit;
}

// Verificar si el nombre de la bebida ya existe
$sql_producto = "SELECT id_bebida FROM bebidas WHERE nombre = ?";
$stmt_producto = $mysqli->prepare($sql_producto);
$stmt_producto->bind_param("s", $nombre);
$stmt_producto->execute();
$result_producto = $stmt_producto->get_result();

if ($result_producto->num_rows > 0) {
    // El nombre de la bebida ya existe, actualizar los detalles de la bebida
    $row_producto = $result_producto->fetch_assoc();
    $id_bebida_existente = $row_producto['id_bebida'];

    // Prepara la consulta SQL para actualizar los datos en la tabla "bebidas"
    $sql_actualizar_bebida = "UPDATE bebidas SET descripcion = ?, precio = ?, imagen = ? WHERE id_bebida = ?";

    // Prepara una sentencia SQL
    $stmt_actualizar_bebida = $mysqli->prepare($sql_actualizar_bebida);
    $stmt_actualizar_bebida->bind_param("sdsi", $descripcion, $precio, $ruta_completa_imagen, $id_bebida_existente);

    // Ejecutar la sentencia SQL para actualizar los datos de la bebida existente
    if ($stmt_actualizar_bebida->execute()) {
        // Éxito: Los datos se han actualizado correctamente
        echo json_encode(["success" => true]);
    } else {
        // Error: No se pudieron actualizar los datos
        echo json_encode(["success" => false, "error_message" => "Error al actualizar la bebida existente: " . $stmt_actualizar_bebida->error]);
    }

    // Cierra la sentencia SQL para actualizar
    $stmt_actualizar_bebida->close();
} else {
    // El nombre de la bebida no existe, insertar una nueva bebida
    $sql_insertar_bebida = "INSERT INTO bebidas (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)";

    // Prepara una sentencia SQL
    $stmt_insertar_bebida = $mysqli->prepare($sql_insertar_bebida);
    $stmt_insertar_bebida->bind_param("ssds", $nombre, $descripcion, $precio, $ruta_completa_imagen);

    // Ejecutar la sentencia SQL para insertar una nueva bebida
    if ($stmt_insertar_bebida->execute()) {
        // Éxito: La nueva bebida se ha insertado correctamente
        echo json_encode(["success" => true]);
    } else {
        // Error: No se pudo insertar la nueva bebida
        echo json_encode(["success" => false, "error_message" => "Error al insertar una nueva bebida: " . $stmt_insertar_bebida->error]);
    }

    // Cierra la sentencia SQL para insertar
    $stmt_insertar_bebida->close();
}

// Cierra la conexión a la base de datos
$conexionBase->closeConnection();

   
*/