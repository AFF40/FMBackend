<?php

require_once "conexion/conexionBase.php"; 

// Crear una instancia de la clase ConexionBase
$conexionBase = new ConexionBase();

// Obtener la conexión
$conn = $conexionBase->getConnection();

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión a la base de datos fallida: " . $conn->connect_error);
}

// Verifica si se recibió el parámetro 'restaurante_id' en la solicitud
if (isset($_GET['restaurante_id'])) {
    $restaurante_id = $_GET['restaurante_id'];
} else {
    die("ID de restaurante no proporcionado en la URL");
}

// Realiza la consulta SQL para obtener los platos del restaurante
//$sql = "SELECT id_comida,nom_plato, descripcion, precio,disponible, imagen FROM platos WHERE restaurante_id = $restaurante_id";
$sql = "SELECT p.*, m.*, mp.* , res.id_rest FROM platos p JOIN menu_platos mp ON p.id_plato = mp.id_plato 
JOIN menus m ON mp.id_menu = m.id_menu JOIN restaurantes res ON m.id_rest = res.id_rest 
WHERE p.id_rest AND res.id_rest = '$restaurante_id';";

$result = $conn->query($sql);
    
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Prepara un arreglo para almacenar los resultados
$platos = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $platos[] = $row;
    }
}



// Devuelve los resultados en formato JSON
echo json_encode($platos);

// Cierra la conexión a la base de datos
$conn->close();
?>
