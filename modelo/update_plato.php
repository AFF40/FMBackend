<?php
// Verifica que la solicitud sea un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conecta a tu base de datos. Reemplaza los valores con los de tu configuración.
    $dbHost = 'localhost';
    $dbUser = 'root';
    $dbPass = '';
    $dbName = 'androidbd';
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        die("Conexión a la base de datos fallida: " . $conn->connect_error);
    }

    // Obtiene los datos del formulario POST
    $platoId = $_POST['plato_id'];
    $nombrePlato = $_POST['nombre_plato'];
    $descripcionPlato = $_POST['descripcion_plato'];
    $precioPlato = $_POST['precio_plato'];
    $imagenBase64 = $_POST['imagen'];

    // Decodifica la imagen Base64
    $imagen = base64_decode($imagenBase64);

    // Realiza la actualización en la base de datos
    $sql = "UPDATE platos SET nom_plato = ?, descripcion = ?, precio = ?, imagen = ? WHERE id_comida = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $nombrePlato, $descripcionPlato, $precioPlato, $imagen, $platoId);

    if ($stmt->execute()) {
        echo "Actualización exitosa";
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    // Cierra la conexión a la base de datos
    $stmt->close();
    $conn->close();
} else {
    echo "Método no permitido";
}
?>