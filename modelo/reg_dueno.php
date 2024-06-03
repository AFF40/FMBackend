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
    die(json_encode(array('exito' => 0, 'msg' => "El nombre de usuario debe tener al menos 6 caracteres.")));
}

// Validar la longitud del número de teléfono
if (strlen($celular) < 8 || !is_numeric($celular)) {
    die(json_encode(array('exito' => 0, 'msg' => "El número de teléfono debe tener al menos 8 dígitos y solo contener números.")));
}

// Verificar si el nombre de usuario ya existe en la base de datos
$sql_check_username = "SELECT id_usuario FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql_check_username);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(array('exito' => 0, 'msg' => "El nombre de usuario ya está registrado.")));
}

// Verificar si el número de teléfono ya existe en la base de datos
$sql_check_phone = "SELECT id_usuario FROM usuarios WHERE celular = ?";
$stmt = $conn->prepare($sql_check_phone);
$stmt->bind_param("s", $celular);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(array('exito' => 0, 'msg' => "El número de teléfono ya está registrado.")));
}

// Hash de la contraseña
$hashed_password = password_hash($passwordD, PASSWORD_DEFAULT);

// Insertar los datos en la tabla "usuarios"
$sql = "INSERT INTO usuarios (username, celular, password, id_rol) VALUES (?, ?, ?, 2)";

// Preparar la declaración
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Vincular parámetros y ejecutar la consulta
    $stmt->bind_param("sss", $username, $celular, $hashed_password);
    if ($stmt->execute()) {
        echo json_encode(array('exito' => 1, 'msg' => " registrado correctamente."));
    } else {
        echo json_encode(array('exito' => 0, 'msg' => "Error al registrar el usuario: " . $stmt->error));
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
} else {
    echo json_encode(array('exito' => 0, 'msg' => "Error en la preparación de la consulta: " . $conn->error));
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
