<?php
require "conexion/conexionBase.php";

class Restaurante
{
    private $con;

    function __construct()
    {
        $this->con = new conexionBase();
    }

    // Método para obtener los datos de la tabla restaurantes en formato JSON
    function mostrarEnJSON()
    {
        $this->con->CreateConnection();
        $sql = "SELECT nom_rest, ubicacion,restaurante_id,celular,estado FROM restaurantes";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);

        $restaurantes = array(); // Array para almacenar los datos

        if ($re > 0) {
            while ($row = $this->con->GetRows($resp)) {
                // Parsear la ubicación de la URL y obtener latitud y longitud
                $ubicacionURL = $row[1];
            
                // Buscar la posición de la "@" en la URL
                $posicionArroba = strpos($ubicacionURL, "@");
            
                if ($posicionArroba !== false) {
                    // Si se encontró una "@", obtener todo después de ella
                    $coordenadasTexto = substr($ubicacionURL, $posicionArroba + 1);
            
                    // Dividir las coordenadas por la coma para obtener latitud y longitud
                    list($latitud, $longitud) = explode(",", $coordenadasTexto);
            
                    $restaurante = array(
                        "nom_rest" => $row[0],
                        "ubicacion" => array(
                            "latitud" => $latitud,
                            "longitud" => $longitud
                        ),
                        "restaurante_id"=>$row[2],
                        "celular"=>$row[3],
                        "estado"=>$row[4],
                    );
                } else {
                    // Manejar el caso en el que no se encontró una "@"
                    // Puedes asignar valores predeterminados o manejarlo según tus necesidades.
                    $restaurante = array(
                        "nom_rest" => $row[0],
                        "ubicacion" => array(
                            "latitud" => 0,
                            "longitud" => 0
                        ),
                        "restaurante_id"=>$row[2],
                        "celular"=>$row[3],
                        "estado"=>$row[4]
                    );
                }
                
                $restaurantes[] = $restaurante;
            }
            
        }

        // Convertir el array de datos a formato JSON
        $json_data = json_encode($restaurantes);

        // Devolver el JSON como respuesta
        header('Content-Type: application/json');
        echo $json_data;
    }
}


class Restaurante_completo
{
    private $con;

    function __construct()
    {
        $this->con = new conexionBase();
    }

    // Método para obtener los datos de la tabla restaurantes en formato JSON
    function mostrarEnJSON()
    {
        // Verificar si se proporcionó un parámetro "restaurante_id" en la URL
        if (isset($_GET["restaurante_id"])) {
            $restaurante_id = $_GET["restaurante_id"];
            $this->con->CreateConnection();
            $sql = "SELECT nom_rest, ubicacion,id_rest,celular,estado FROM restaurantes WHERE id_rest = $restaurante_id";
            $resp = $this->con->ExecuteQuery($sql);
            $re = $this->con->GetCountAffectedRows($resp);

            $restaurantes = array(); // Array para almacenar los datos

            if ($re > 0) {
                while ($row = $this->con->GetRows($resp)) {
                    // Parsear la ubicación de la URL y obtener latitud y longitud
                    $ubicacionURL = $row[1];

                    // Buscar la posición de la "@" en la URL
                    $posicionArroba = strpos($ubicacionURL, "@");

                    if ($posicionArroba !== false) {
                        // Si se encontró una "@", obtener todo después de ella
                        $coordenadasTexto = substr($ubicacionURL, $posicionArroba + 1);

                        // Dividir las coordenadas por la coma para obtener latitud y longitud
                        list($latitud, $longitud) = explode(",", $coordenadasTexto);

                        $restaurante = array(
                            "nom_rest" => $row[0],
                            "ubicacion" => array(
                                "latitud" => $latitud,
                                "longitud" => $longitud
                            ),
                            "restaurante_id"=>$row[2],
                            "celular"=>$row[3],
                            "estado"=>$row[4],
                        );
                    } else {
                        // Manejar el caso en el que no se encontró una "@"
                        // Puedes asignar valores predeterminados o manejarlo según tus necesidades.
                        $restaurante = array(
                            "nom_rest" => $row[0],
                            "ubicacion" => array(
                                "latitud" => 0,
                                "longitud" => 0
                            ),
                            "restaurante_id"=>$row[2],
                            "celular"=>$row[3],
                            "estado"=>$row[4]
                        );
                    }

                    $restaurantes[] = $restaurante;
                }
            }

            // Convertir el array de datos a formato JSON
            $json_data = json_encode($restaurantes);

            // Devolver el JSON como respuesta
            header('Content-Type: application/json');
            echo $json_data;
        } else {
            // Si no se proporcionó el parámetro "restaurante_id", devolver un error o un mensaje según tus necesidades
            header('Content-Type: application/json');
            echo json_encode(array("error" => "No se proporcionó el parámetro 'restaurante_id' en la URL."));
        }
    }
}


// Función para obtener las coordenadas de latitud y longitud desde una URL
function obtenerCoordenadasDesdeURL($url)
{
    // Realiza el procesamiento necesario para obtener las coordenadas de la URL
    // Por ejemplo, puedes usar expresiones regulares o métodos de análisis de URL
    // Aquí asumimos que $url contiene las coordenadas en el formato deseado.
    // Debes ajustar esta función según la estructura de tus URLs.

    // Ejemplo de procesamiento:
    $coordenadas = explode(",", $url);
    $latitud = $coordenadas[0];
    $longitud = $coordenadas[1];

    return array($latitud, $longitud);
}
