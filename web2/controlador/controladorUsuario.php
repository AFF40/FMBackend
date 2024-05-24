<?php
 include "../modelo/Usuario.php";
 $per = new Usuario();
$data = json_decode(file_get_contents("php://input"), true);
$nick="";
$pass="";
$idRol=null;
$idPersona=null;
if ($data) {
    if (isset($data['nick']) && $data['nick']) {
        $nick = htmlspecialchars($data['nick']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el nick'));
        die();
    }
    if (isset($data['pass']) && $data['pass']) {
        $pass = htmlspecialchars($data['pass']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el nick'));
        die();
    }
    if (isset($data['idRol']) && $data['idRol']) {
        $idRol = htmlspecialchars($data['idRol']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el id del Rol'));
        die();
    }
    if (isset($data['idPersona']) && $data['idPersona']) {
        $idPersona = htmlspecialchars($data['idPersona']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el id de la persona'));
        die();
    }
   
    $per->asignar("nick", $nick);
    $per->asignar("pass", $pass);
    $per->asignar("idRol", $idRol);
    $per->asignar("idPersona", $idPersona);
    $per->validar();
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizo la peticion correctamente'));
}
