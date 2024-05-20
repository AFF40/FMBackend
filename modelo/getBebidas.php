<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "androidbd";

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verifica si se recibió el parámetro 'restaurante_id' en la solicitud
if (isset($_GET['restaurante_id'])) {
    $restaurante_id = $_GET['restaurante_id'];
} else {
    die("ID de restaurante no proporcionado en la URL");
}

// Realiza la consulta SQL para obtener las bebidas del restaurante
$sql = "SELECT b.*, m.*, mb.* FROM bebidas b JOIN menu_bebidas mb ON b.id_bebida = mb.id_bebida JOIN menus m ON mb.id_menu = m.id_menu WHERE m.id_rest = '$restaurante_id';";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Prepara un arreglo para almacenar los resultados
$bebidas = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bebidas[] = $row;
    }
}


// Devuelve los resultados en formato JSON
echo json_encode($bebidas);

// Cierra la conexión a la base de datos
$conn->close();
?>
