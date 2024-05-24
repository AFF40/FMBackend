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

// Obtener los datos del formulario
$username = $_POST["username"];
$celular = $_POST["celular"];
$passwordD = $_POST["passwordD"];

// Validar la longitud del nombre de usuario
if (strlen($username) < 6) {
    die("El nombre de usuario debe tener al menos 6 caracteres.");
}

// Verificar si el número de teléfono ya existe en la base de datos
$sql_check_phone = "SELECT id_usuario FROM usuarios WHERE celular = ?";
$stmt = $conn->prepare($sql_check_phone);
$stmt->bind_param("i", $celular);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("El número de teléfono ya está registrado.");
}

// Insertar los datos en la tabla "usuarios"
$sql = "INSERT INTO usuarios (username, celular, password, id_rol) VALUES (?, ?, ?, 2)";

// Preparar la declaración
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("sis", $username, $celular, $passwordD);
    if ($stmt->execute()) {
        echo "Dueño registrado correctamente.";
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
} else {
    echo "Error en la preparación de la consulta: " . $conn->error;
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
