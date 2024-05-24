<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$mysqli = new mysqli("localhost", "root", "1234", "androidbd");

if ($mysqli->connect_error) {
    die("Error en la conexión: " . $mysqli->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$admin = $_POST['id_admin'];
$nomRest = $_POST['nom_rest'];
$ubicacion = $_POST['ubicacion'];
$celularRest = $_POST['celularRest'];
$tematica = $_POST['tematica'];

if (strlen($username) < 6) {
    die("El nombre de usuario debe tener al menos 6 caracteres.");
}

if (empty($username) || empty($password)) {
    die("Por favor, complete todos los campos.");
}

$sql_select_user = "SELECT id_usuario, password FROM usuarios WHERE username = ?";
$stmt = $mysqli->prepare($sql_select_user);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_usuario = $row['id_usuario'];
    $hashed_password = $row['password'];

    $sql_select_restaurante = "SELECT id_rest FROM restaurantes WHERE id_usuario = ?";
    $stmt = $mysqli->prepare($sql_select_restaurante);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result2 = $stmt->get_result();

    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();
        $id_rest = $row['id_rest'];
        if (!verificarEnExcel($admin, $nomRest)) {
            guardarEnExcel($admin, $nomRest, $ubicacion, $celularRest, $tematica);
        }
        echo "El usuario ya tiene un restaurante asociado.";
    } else {
        $nomRest = $_POST['nom_rest'];
        $ubicacion = $_POST['ubicacion'];
        $celularRest = $_POST['celularRest'];
        $tematica = $_POST['tematica'];

        $carpetaRest = preg_replace('/[^A-Za-z0-9\-]/', '', $nomRest);
        $directorioRestaurante = $_SERVER['DOCUMENT_ROOT'] . "/foodmapsBD/restaurantes/$carpetaRest";

        if (!file_exists($directorioRestaurante)) {
            if (mkdir($directorioRestaurante, 0777, true)) {
                echo "Directorio $directorioRestaurante creado con éxito.";
            } else {
                echo "Error al crear el directorio $directorioRestaurante.";
            }
        }

        $directorioIconos = $directorioRestaurante . '/iconos';
        if (!file_exists($directorioIconos)) {
            mkdir($directorioIconos, 0777, true);
        }

        if (isset($_POST['imagen'])) {
            $imagenDecodificada = base64_decode($_POST['imagen']);
            $nombreImagen = uniqid('img_') . '.jpg';
            $rutaFisicaImagen = $directorioIconos . '/' . $nombreImagen;
            $rutaImagen = "http://" . $_SERVER['HTTP_HOST'] . "/foodmapsBD/restaurantes/$carpetaRest/iconos/$nombreImagen";  // Usar la URL completa

            if (file_put_contents($rutaFisicaImagen, $imagenDecodificada)) {
                $sql_insert_restaurante = "INSERT INTO restaurantes (id_usuario, nom_rest, ubicacion, celular, tematica, imagen) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql_insert_restaurante);

                if ($stmt === false) {
                    die("Error en la consulta: " . $mysqli->error);
                } else {
                    $stmt->bind_param("isssss", $id_usuario, $nomRest, $ubicacion, $celularRest, $tematica, $rutaImagen);
                    $stmt->execute();

                    $id_rest = $mysqli->insert_id;
                    $sql_insert_menu = "INSERT INTO menus (id_rest) VALUES (?)";
                    $stmt = $mysqli->prepare($sql_insert_menu);
                    $stmt->bind_param("i", $id_rest);
                    $stmt->execute();

                    echo "Restaurante y menú registrados correctamente.";
                    guardarEnExcel($admin, $nomRest, $ubicacion, $celularRest, $tematica);
                }
            } else {
                echo "Error al guardar la imagen. Verifique los permisos del directorio y la ruta de la imagen.";
            }
        } else {
            echo "No se proporcionó ninguna imagen.";
        }
    }
} else {
    echo "No se encontró el usuario con los datos proporcionados.";
}

$mysqli->close();

function guardarEnExcel($admin, $nomRest, $ubicacion, $celularRest, $tematica) {
    $filePath = 'C:/laragon/www/foodmapsBD/administradores/registro_rest.xlsx';             // Ruta del archivo Excel
    $dirPath = dirname($filePath);

    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    // Verificar si el archivo existe
    if (!file_exists($filePath)) {
        // Crear un nuevo archivo de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID Admin');
        $sheet->setCellValue('B1', 'Nombre Restaurante');
        $sheet->setCellValue('C1', 'Ubicación');
        $sheet->setCellValue('D1', 'Celular');
        $sheet->setCellValue('E1', 'Temática');
        $sheet->setCellValue('F1', 'Fecha y Hora de Registro');  // Nueva columna para la fecha y hora

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    try {
        $spreadsheet = IOFactory::load($filePath);
    } catch (Exception $e) {
        die("Error loading file: " . $e->getMessage());
    }

    $sheet = $spreadsheet->getActiveSheet();
    
    // Obtener la última fila con datos en la columna A
    $lastRow = $sheet->getCell('A' . $sheet->getHighestRow())->getRow();

    // Insertar los nuevos datos en la siguiente fila disponible
    $newRow = $lastRow + 1;

    $sheet->setCellValue('A' . $newRow, $admin);
    $sheet->setCellValue('B' . $newRow, $nomRest);
    $sheet->setCellValue('C' . $newRow, $ubicacion);
    $sheet->setCellValue('D' . $newRow, $celularRest);
    $sheet->setCellValue('E' . $newRow, $tematica);
    $sheet->setCellValue('F' . $newRow, date('Y-m-d H:i:s'));  // Guardar la fecha y hora actual

    $writer = new Xlsx($spreadsheet);
    try {
        $writer->save($filePath);
        echo "Datos guardados en el archivo Excel correctamente.";
    } catch (Exception $e) {
        die("Error saving file: " . $e->getMessage());
    }
}

function verificarEnExcel($admin, $nomRest) {
    $filePath = 'C:/laragon/www/foodmapsBD/administradores/registro_rest.xlsx';                   // Ruta del archivo Excel
    $dirPath = dirname($filePath);

    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    // Verificar si el archivo existe
    if (!file_exists($filePath)) {
        // Crear un nuevo archivo de Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID Admin');
        $sheet->setCellValue('B1', 'Nombre Restaurante');
        $sheet->setCellValue('C1', 'Ubicación');
        $sheet->setCellValue('D1', 'Celular');
        $sheet->setCellValue('E1', 'Temática');
        $sheet->setCellValue('F1', 'Fecha y Hora de Registro');  // Nueva columna para la fecha y hora

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    try {
        $spreadsheet = IOFactory::load($filePath);
    } catch (Exception $e) {
        die("Error loading file: " . $e->getMessage());
    }

    $sheet = $spreadsheet->getActiveSheet();
    $lastRow = $sheet->getHighestRow();

    for ($row = 2; $row <= $lastRow; $row++) {
        $existingAdmin = $sheet->getCell('A' . $row)->getValue();
        $existingNomRest = $sheet->getCell('B' . $row)->getValue();

        if ($existingAdmin == $admin && $existingNomRest == $nomRest) {
            return true;
        }
    }

    return false;
}

?>
