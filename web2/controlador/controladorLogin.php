<?php
include "../modelo/Login.php";
$per = new Login();

if ($_POST) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Validación de longitud del nombre de usuario
        if (strlen($username) < 6) {
            echo json_encode(array('exito' => 0, 'msg' => 'El nombre de usuario debe tener al menos 6 caracteres'));
            die();
        }

        // Validación de longitud de la contraseña
        if (strlen($password) <4) {
            echo json_encode(array('exito' => 0, 'msg' => 'La contraseña debe tener al menos 8 caracteres'));
            die();
        }

        $per->asignar("username", $username);
        $per->asignar("password", $password);
        $per->validar();
    } else {
        echo json_encode(array('exito' => 0, 'msg' => 'Faltan datos de entrada'));
    }
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizó la petición correctamente'));
}
?>
