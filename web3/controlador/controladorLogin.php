<?php
include "../modelo/Login.php";
$per = new Login();

if ($_POST) {
    if (isset($_POST['username']) && $_POST['username']) {
        $username = htmlspecialchars($_POST['username']);
        // Validación de longitud del nombre de usuario
        if (strlen($username) < 6) {
            echo json_encode(array('exito' => 0, 'msg' => 'El nombre de usuario debe tener al menos 6 caracteres'));
            die();
        }
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el username'));
        die();
    }
    if (isset($_POST['password']) && $_POST['password']) {
        $password = htmlspecialchars($_POST['password']);
        
        // Validación de longitud de la contraseña 
        if (strlen($password) < 4) {
            echo json_encode(array('exito' => 0, 'msg' => 'La contraseña debe tener al menos 8 caracteres'));
            die();
        }
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el password'));
        die();
    }
    
    $per->asignar("username", $username);
    $per->asignar("password", $password);
    $per->validar();
    
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizo la peticion correctamente'));
}