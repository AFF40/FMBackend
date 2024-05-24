<?php
 include "../modelo/Persona.php";
 $per = new Persona();

//$data = json_decode(file_get_contents("php://input"), true);
//echo json_encode(array('exito' => 0, 'msg' => $_POST['username']));
//die();
$username="";
$email="";
$pass1="";
$pass2="";
if ($_POST) {
    if (isset($_POST['username']) && $_POST['username']) {
        $username = htmlspecialchars($_POST['username']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el username'));
        die();
    }
    if (isset($_POST['email']) && $_POST['email']) {
        // Validar la dirección de correo electrónico
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('exito' => 0, 'msg' => 'La dirección de correo electrónico no es válida'));
            die();
        }
    } else {
        echo json_encode(array('exito' => 0, 'msg' => 'No se envió el correo electrónico'));
        die();
    }
    if($_POST['pass1']==$_POST['pass2']){
    if (isset($_POST['pass1']) && $_POST['pass1']) {
        $pass1=$_POST['pass1'];
    }else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el pass1'));
        die();
    }
    if (isset($_POST['pass2']) && $_POST['pass2']) {
        $pass2=$_POST['pass2'];
    }else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el pass2'));
        die();
    }
}else{
    echo json_encode(array('exito' => 0, 'msg' => 'las contraseñas no coinciden'));
        die();
}
    //asigna los datos para la recepcion
    $per->asignar("username", $username);
    $per->asignar("email", $email);
    $per->asignar("pass1", $pass1);
    $per->asignar("pass2", $pass2);
    $per->validar();
    
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizo la peticion correctamente'));
}
