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

// Obtener el username desde la solicitud POST
if (isset($_POST['username'])) {
    $usernamee = $_POST['username'];
} else {
    // Manejar el caso en el que no se proporcionó 'username'
    die("No se proporcionó 'username'");
}

$sql = "SELECT id_rest FROM usuarios u JOIN restaurantes res ON u.id_usuario = res.id_usuario WHERE u.username = '$usernamee'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener el valor de restaurante_id
    $row = $result->fetch_assoc();
    $restaurante_id = $row["id_rest"];
    // Crear un array asociativo para la respuesta JSON
    $response = array("exito" => 1, "msg" => "Id_rest obtenido correctamente", "id_rest" => $restaurante_id);
} else {
    // Si no se encontró el usuario, enviar un mensaje de error
    $response = array("exito" => 0, "msg" => "No se encontró el id_rest para este usuario");
}

// Devolver la respuesta como JSON
echo json_encode($response);

// Cerrar la conexión a la base de datos
$conn->close();
?>
x