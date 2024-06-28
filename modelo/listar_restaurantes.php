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

$sql = "SELECT id_rest, nom_rest, celular, tematica, ubicacion, imagen, estado FROM restaurantes";
$result = $conn->query($sql);
$restaurantes = array();
echo "lista de restaurantes",$result;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (filter_var($row["ubicacion"], FILTER_VALIDATE_URL)) {
            preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $row["ubicacion"], $matches);
            $latitud = $matches[1] ?? '';
            $longitud = $matches[2] ?? '';
        } else {
            $ubicacion = explode(",", $row["ubicacion"]);
            $latitud = $ubicacion[0];
            $longitud = $ubicacion[1];
        }

        $restaurante = array(
            "restaurante_id" => $row["id_rest"],
            "celular" => $row["celular"],
            "tematica" => $row["tematica"],
            "nom_rest" => $row["nom_rest"],
            "latitud" => $latitud,
            "longitud" => $longitud,
            "imagen" => $row["imagen"],
            "estado" => $row["estado"]
        );
        array_push($restaurantes, $restaurante);
    }
} else {
    error_log("No se encontraron restaurantes en la base de datos");
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($restaurantes);
?>