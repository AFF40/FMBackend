<?php
require_once "conexion/conexionBase.php"; // Incluir el archivo de conexión

// Verificar si se reciben datos por método POST y que es un JSON
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && json_last_error() === JSON_ERROR_NONE) {

    // Crear una instancia de la clase ConexionBase
    $conexionBase = new ConexionBase();

    // Obtener la conexión
    $conn = $conexionBase->getConnection();

    // Verificar la conexión
    if ($conn->connect_error) {
        die(json_encode(array("success" => false, "error_message" => "Conexión a la base de datos fallida: " . $conn->connect_error)));
    }

    // Obtener los datos de la bebida desde la solicitud POST en formato JSON
    $id_mebeb = isset($data['id_mebeb']) ? $data['id_mebeb'] : '';
    $nombre_bebida = isset($data['nombre_bebida']) ? $data['nombre_bebida'] : '';
    $descripcion_bebida = isset($data['descripcion_bebida']) ? $data['descripcion_bebida'] : '';
    $precio_bebida = isset($data['precio_bebida']) ? $data['precio_bebida'] : 0.0; // Cambia esto según el tipo de dato en tu base de datos
    $imagen_bebida = isset($data['imagen_bebida']) ? $data['imagen_bebida'] : '';
    $nombre_restaurante = isset($data['nombre_restaurante']) ? $data['nombre_restaurante'] : '';
    $restaurante_id = isset($data['restaurante_id']) ? $data['restaurante_id'] : 0; // Cambia esto según el tipo de dato en tu base de datos

    // Verificar cada campo uno por uno y si alguno está vacío, enviar un mensaje de error
    if (empty($nombre_bebida)) {
        echo json_encode(array('success' => false, 'error_message' => 'El nombre de la bebida no puede estar vacío.'));
        die();
    }
    if (empty($descripcion_bebida)) {
        echo json_encode(array('success' => false, 'error_message' => 'La descripción de la bebida no puede estar vacía.'));
        die();
    }
    if ($precio_bebida <= 0) {
        echo json_encode(array('success' => false, 'error_message' => 'El precio de la bebida debe ser mayor a cero.'));
        die();
    }
    if (empty($imagen_bebida)) {
        echo json_encode(array('success' => false, 'error_message' => 'La imagen de la bebida no puede estar vacía.'));
        die();
    }
    if (empty($nombre_restaurante)) {
        echo json_encode(array('success' => false, 'error_message' => 'El nombre del restaurante no puede estar vacío.'));
        die();
    }
    if ($restaurante_id <= 0) {
        echo json_encode(array('success' => false, 'error_message' => 'El id del restaurante debe ser mayor a cero.'));
        die();
    }

    // Verificar si el nombre de la bebida ya existe en la tabla "productos"
    $sql_check_producto = "SELECT id_producto FROM productos WHERE nombre = ?";
    $stmt_check_producto = $conn->prepare($sql_check_producto);
    $stmt_check_producto->bind_param("s", $nombre_bebida);
    $stmt_check_producto->execute();
    $stmt_check_producto->store_result();

    if ($stmt_check_producto->num_rows > 0) {
        // El producto ya existe, obtener su ID
        $stmt_check_producto->bind_result($id_producto);
        $stmt_check_producto->fetch();
        $stmt_check_producto->close();
    } else {
        // El producto no existe, insertarlo en la tabla "productos" y obtener el ID insertado
        $stmt_check_producto->close();

        // Insertar el nuevo producto en la tabla "productos"
        $sql_insert_producto = "INSERT INTO productos (nombre,created_at,updated_at ) VALUES (?, NOW(), NOW())";
        $stmt_insert_producto = $conn->prepare($sql_insert_producto);
        $stmt_insert_producto->bind_param("s", $nombre_bebida);
        $stmt_insert_producto->execute();

        // Obtener el ID del producto insertado
        $id_producto = $stmt_insert_producto->insert_id;
        $stmt_insert_producto->close();
    }

    // Obtener el id_menu mediante un JOIN con la tabla "menus"
    $sql_get_menu_id = "SELECT m.id_menu 
                        FROM menus m
                        JOIN restaurantes r ON m.id_rest = r.id_rest
                        WHERE r.nom_rest = ?";
    $stmt_get_menu_id = $conn->prepare($sql_get_menu_id);
    $stmt_get_menu_id->bind_param("s", $nombre_restaurante);
    $stmt_get_menu_id->execute();
    $result_menu_id = $stmt_get_menu_id->get_result();

    if ($result_menu_id->num_rows > 0) {
        $row_menu_id = $result_menu_id->fetch_assoc();
        $id_menu = $row_menu_id['id_menu'];
    } else {
        // Si no se encuentra el menú para el restaurante especificado, maneja el error
        echo json_encode(array('success' => false, 'error_message' => 'No se encontró el menú para el restaurante especificado.'));
        die();
    }

    $stmt_get_menu_id->close();

    // Limpieza y normalización del nombre del restaurante para la carpeta
    $nombre_restaurante_clean = limpiarNombre($nombre_restaurante);

    // Decodificar la imagen Base64
    $imagen_decodificada = base64_decode($imagen_bebida);

    // Nombre del archivo de imagen (bebida_{id_mebeb}.jpg)
    $nombre_archivo = "bebida_" . uniqid() . ".jpg"; // Puedes cambiar la extensión según el tipo de imagen

    // Ruta donde se guardará la imagen
    $ruta_imagen = "/foodmapsBD/restaurantes/{$nombre_restaurante_clean}/bebidas/{$nombre_archivo}";

    // Ruta absoluta en el servidor
    $ruta_absoluta_imagen = $_SERVER['DOCUMENT_ROOT'] . $ruta_imagen;

    // Guardar la imagen en el servidor
    if (!file_put_contents($ruta_absoluta_imagen, $imagen_decodificada)) {
        echo json_encode(array('success' => false, 'error_message' => 'Error al guardar la imagen de la bebida.'));
        die();
    }

    // Construir la URL completa de la imagen
    $url_imagen = "http://localhost" . $ruta_imagen;

    // Preparar la consulta SQL para actualizar la bebida en la tabla "mebeb"
    $sql_update_bebida = "UPDATE mebeb SET descripcion = ?, precio = ?, imagen = ?, id_producto = ?, id_menu = ?, updated_at=NOW() WHERE id_mebeb = ?";
    $stmt_update_bebida = $conn->prepare($sql_update_bebida);
    $stmt_update_bebida->bind_param("sdssii", $descripcion_bebida, $precio_bebida, $url_imagen, $id_producto, $id_menu, $id_mebeb);

    // Ejecutar la actualización de la bebida
    if ($stmt_update_bebida->execute()) {
        // Si la actualización fue exitosa
        echo json_encode(array('success' => true));
    } else {
        // Si hubo un error en la ejecución de la sentencia
        echo json_encode(array('success' => false, 'error_message' => 'Error al actualizar la bebida.'));
    }

    // Cerrar la conexión
    $stmt_update_bebida->close();
    $conn->close();

} else {
    // Si la solicitud no es de tipo POST o no es un JSON válido
    echo json_encode(array('success' => false, 'error_message' => 'Método no permitido o JSON no válido.'));
}

// Función para limpiar y normalizar nombres de carpeta
function limpiarNombre($cadena) {
    // Define los caracteres permitidos en el nombre de carpeta
    $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_-\ñÑ";

    // Elimina caracteres no permitidos y espacios
    $cadena = trim($cadena);
    $cadena = preg_replace('/[^\ñ\Ñ'.$permitidos.']/', '', $cadena);

    return $cadena;
}
?>
