<?php
// Verifica si se reciben los datos del formulario a través de POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lee los datos JSON del cuerpo de la solicitud
    $json_data = file_get_contents('php://input');

    // Verifica si la cadena JSON está vacía
    if (empty($json_data)) {
        $response = array(
            "error_code" => 400,
            "error_message" => "Error: No se recibieron datos JSON."
        );
        echo json_encode($response);
        exit; // Termina el script
    }

    // Decodifica los datos JSON en un arreglo asociativo
    $data = json_decode($json_data, true);

    // Verifica si se pudo decodificar el JSON correctamente
    if ($data === null) {
        $response = array(
            "error_code" => 400,
            "error_message" => "Error: No se pudieron decodificar los datos JSON."
        );
        echo json_encode($response);
        exit; // Termina el script
    }

    // Recupera los valores del arreglo
    $nombre = isset($data["nombre"]) ? htmlspecialchars($data["nombre"]) : "";
    $descripcion = isset($data["descripcion"]) ? htmlspecialchars($data["descripcion"]) : "";
    $precio = isset($data["precio"]) ? floatval($data["precio"]) : 0;
    $restaurante_nombre = isset($data["restaurante_nombre"]) ? htmlspecialchars($data["restaurante_nombre"]) : "";
    $restaurante_id = isset($data["restaurante_id"]) ? htmlspecialchars($data["restaurante_id"]) : "";
    $imagen_base64 = isset($data["imagen"]) ? $data["imagen"] : "";

    // Realiza la validación de los datos (puedes agregar más validaciones según tus necesidades)
    if (empty($nombre) || empty($descripcion) || $precio <= 0 || empty($imagen_base64) || empty($restaurante_nombre) || empty($restaurante_id)) {
        $response = array(
            //imprimir el json con los datos que se recibieron
            "error_code" => 400,
            "error_message" => "Error: Por favor, llena todos los campos requeridos." . json_encode($data)
        );
        echo json_encode($response);
        exit; // Termina el script
    }
    
    // Decodifica la imagen Base64
    $imagen = base64_decode($imagen_base64);

    // Ruta donde se guardará la imagen
    $ruta_imagen = "/foodmapsBD/restaurantes/" . preg_replace('/[^A-Za-z0-9\-]/', '', $restaurante_nombre) . "/platos/";

    // Verifica si la carpeta existe, si no existe, la crea
    $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $ruta_imagen;
    if (!file_exists($ruta_completa)) {
        if (!mkdir($ruta_completa, 0777, true)) {
            $response = array(
                "error_code" => 500,
                "error_message" => "Error: No se pudo crear la carpeta de la imagen."
            );
            echo json_encode($response);
            exit; // Termina el script
        }
    }

    // Nombre del archivo de imagen (producto_"numero_asendente"))
    $nombre_imagen = "plato_" . uniqid() . ".jpg"; // Puedes cambiar la extensión según el tipo de imagen

    // Ruta completa donde se guardará la imagen
    $ruta_completa_imagen = $ruta_imagen . $nombre_imagen;

    // Guarda la imagen en el servidor
    if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . $ruta_completa_imagen, $imagen)) {
        $response = array(
            "error_code" => 500,
            "error_message" => "Error: No se pudo guardar la imagen en el servidor."
        );
        echo json_encode($response);
        exit; // Termina el script
    }

    
require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión

// Crear una instancia de la clase ConexionBase
$conexionBase = new ConexionBase();

// Obtener la conexión
$conn = $conexionBase->getConnection();

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión a la base de datos fallida: " . $conn->connect_error);
}

    // Prepara la consulta SQL para insertar datos en la tabla "platos"
    $sql = "INSERT INTO platos (id_rest, nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?, ?)";

    // Prepara una sentencia SQL
    $stmt = $conn->prepare($sql);
    $ruta_completa_imagen = "http://localhost".$ruta_imagen . $nombre_imagen;

    // Vincula los parámetros
    $stmt->bind_param("issis", $restaurante_id, $nombre, $descripcion, $precio, $ruta_completa_imagen);
    
if ($stmt->execute()) {
    // Éxito: Los datos se han insertado correctamente
    $id_plato = $conn->insert_id; // Obtiene el ID del plato insertado

    // Prepara la consulta SQL para obtener el id_menu
    $sql_menu = "SELECT id_menu FROM menus WHERE id_rest = ?";
    $stmt_menu = $conn->prepare($sql_menu);
    $stmt_menu->bind_param("i", $restaurante_id);
    $stmt_menu->execute();
    $result_menu = $stmt_menu->get_result();
    $row_menu = $result_menu->fetch_assoc();
    $id_menu = $row_menu['id_menu'];

    // Prepara la consulta SQL para insertar datos en la tabla "menu_platos"
    $sql_menu_platos = "INSERT INTO menu_platos (id_menu, id_plato) VALUES (?, ?)";
    $stmt_menu_platos = $conn->prepare($sql_menu_platos);
    $stmt_menu_platos->bind_param("ii", $id_menu, $id_plato);

    if ($stmt_menu_platos->execute()) {
        // Éxito: Los datos se han insertado correctamente en la tabla "menu_platos"
        $response = array(
            "success" => true,
            "message" => "Plato y menú agregados con éxito"
        );
        echo json_encode($response);
    } else {
        // Error: No se pudieron insertar los datos en la tabla "menu_platos"
        $response = array(
            "error_code" => 500,
            "error_message" => "Error al insertar en la tabla menu_platos: " . $stmt_menu_platos->error
        );
        echo json_encode($response);
    }

    $stmt_menu_platos->close();
    $stmt_menu->close();
} else {
    // Error: No se pudieron insertar los datos
    $response = array(
        "error_code" => 500,
        "error_message" => "Error al insertar el plato: " . $stmt->error
    );
    echo json_encode($response);
}
    // Cierra la conexión a la base de datos
    $stmt->close();
    $conn->close();

} else {
    // Si no se recibieron los datos por POST, muestra un mensaje de error
    $response = array(
        "error_code" => 400,
        "error_message" => "Error: Este script solo acepta solicitudes POST."
    );
    echo json_encode($response);
}
?>
