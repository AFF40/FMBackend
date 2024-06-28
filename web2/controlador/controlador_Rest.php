<?php
require "../modelo/conexion/conexionBase.php";

class RestauranteTodos {
    private $con;

    function __construct() {
        $this->con = new conexionBase();
    }

    function mostrarEnJSON() {
        $this->con->CreateConnection();
        $sql = "SELECT id_rest, nom_rest, celular, tematica, ubicacion, imagen, estado FROM restaurantes";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);

        $restaurantes = array(); // Array para almacenar los datos

        if ($re > 0) {
            while ($row = $this->con->GetRows($resp)) {
                if (filter_var($row["ubicacion"], FILTER_VALIDATE_URL)) {
                    preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $row["ubicacion"], $matches);
                    $latitud = $matches[1] ?? '';
                    $longitud = $matches[2] ?? '';
                } else {
                    $ubicacion = explode(",", $row["ubicacion"]);
                    $latitud = $ubicacion[0];
                    $longitud = $ubicacion[1];
                }
                // Procesamiento de datos aquí
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
                $restaurantes[] = $restaurante;
            }
        }

        $json_data = json_encode($restaurantes);
        header('Content-Type: application/json');
        echo $json_data;
    }
}

// Crear una instancia y llamar al método para mostrar los datos en JSON
$restauranteTodos = new RestauranteTodos();
$restauranteTodos->mostrarEnJSON();
?>
