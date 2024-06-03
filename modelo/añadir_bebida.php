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
    $restaurante_id = isset($data["restaurante_id"]) ? intval($data["restaurante_id"]) : 0;
    $imagen_base64 = isset($data["imagen"]) ? $data["imagen"] : "";

    // Realiza la validación de los datos (puedes agregar más validaciones según tus necesidades)
    if (empty($nombre) || empty($descripcion) || $precio <= 0 || empty($imagen_base64) || empty($restaurante_nombre) || empty($restaurante_id)) {
        $response = array(
            "error_code" => 400,
            "error_message" => "Error: Por favor, llena todos los campos requeridos." . json_encode($data)
        );
        echo json_encode($response);
        exit; // Termina el script
    }

    // Decodifica la imagen Base64
    $imagen = base64_decode($imagen_base64);

    // Ruta donde se guardará la imagen
    $ruta_imagen = "/foodmapsBD/restaurantes/" . preg_replace('/[^A-Za-z0-9\-]/', '', $restaurante_nombre) . "/bebidas/";

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

    // Nombre del archivo de imagen (bebida_"numero_aleatorio"))
    $nombre_imagen = "bebida_" . uniqid() . ".jpg"; // Puedes cambiar la extensión según el tipo de imagen

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

    // Verificar si el producto ya existe en la tabla "productos"
    $sql_check = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $nombre);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // El producto ya existe
        $row = $result_check->fetch_assoc();
        $producto_id = $row['id_producto'];
    } else {
        // El producto no existe, inserta un nuevo producto
        $sql_insert_product = "INSERT INTO productos (nombre, created_at, updated_at) VALUES (?, NOW(), NOW())";
        $stmt_insert_product = $conn->prepare($sql_insert_product);
        $stmt_insert_product->bind_param("s", $nombre);

        if ($stmt_insert_product->execute()) {
            // Éxito: El producto se ha insertado correctamente
            $producto_id = $conn->insert_id;
        } else {
            // Error: No se pudieron insertar los datos del producto
            $response = array(
                "error_code" => 500,
                "error_message" => "Error al insertar el producto: " . $stmt_insert_product->error
            );
            echo json_encode($response);
            $stmt_insert_product->close();
            $conn->close();
            exit; // Termina el script
        }

        $stmt_insert_product->close();
    }

    $stmt_check->close();

    // Obtener el id_menu
    $sql_menu = "SELECT id_menu FROM menus WHERE id_rest = ?";
    $stmt_menu = $conn->prepare($sql_menu);
    $stmt_menu->bind_param("i", $restaurante_id);
    $stmt_menu->execute();
    $result_menu = $stmt_menu->get_result();
    $row_menu = $result_menu->fetch_assoc();
    $id_menu = $row_menu['id_menu'];
    $ruta_completa_imagen = "http://localhost" . $ruta_completa_imagen;

    // Insertar en la tabla "mebeb"
    $sql_mebeb = "INSERT INTO mebeb (id_menu, id_producto, descripcion, precio, disponible, imagen, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_mebeb = $conn->prepare($sql_mebeb);
    $disponible = 1; // Asumimos que la bebida está disponible
    $stmt_mebeb->bind_param("iisdss", $id_menu, $producto_id, $descripcion, $precio, $disponible, $ruta_completa_imagen);
    
    if ($stmt_mebeb->execute()) {
        // Éxito: Los datos se han insertado correctamente en la tabla "mebeb"
        $response = array(
            "success" => true,
            "message" => "Bebida agregada con éxito"
        );
        echo json_encode($response);
    } else {
        // Error: No se pudieron insertar los datos en la tabla "mebeb"
        $response = array(
            "error_code" => 500,
            "error_message" => "error: " . $stmt_mebeb->error
        );
        echo json_encode($response);
    }
    
    $stmt_mebeb->close();
    $stmt_menu->close();
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
