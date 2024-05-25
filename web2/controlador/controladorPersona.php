<?php
require_once "../modelo/conexion/conexionBase.php";
require_once "../modelo/Persona.php";


$per = new Persona();

$username = "";
$celular = "";
$pass1 = "";
$pass2 = "";

if ($_POST) {
    if (isset($_POST['username']) && $_POST['username']) {
        $username = htmlspecialchars($_POST['username']);
    } else {
        echo json_encode(array('exito' => 0, 'msg' => 'No se envió el username'));
        die();
    }

    if (isset($_POST['celular']) && $_POST['celular']) {
        $celular = htmlspecialchars($_POST['celular']);
    } else {
        echo json_encode(array('exito' => 0, 'msg' => 'No se envió el celular'));
        die();
    }

    if (isset($_POST['pass1']) && isset($_POST['pass2']) && $_POST['pass1'] === $_POST['pass2']) {
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];
    } else {
        echo json_encode(array('exito' => 0, 'msg' => 'Las contraseñas no coinciden'));
        die();
    }

    // Asigna los datos para la recepción
    $per->asignar("username", $username);
    $per->asignar("celular", $celular);
    $per->asignar("password", $pass1); // Solo necesitas una contraseña, ya que las contraseñas deben coincidir

    // Realiza la validación y el registro del usuario
    $per->validar();
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizó la petición correctamente'));
}
?>
