<?php
 include "../modelo/Rol.php";
 $per = new Rol();
$data = json_decode(file_get_contents("php://input"), true);
$rol="";
if ($data) {
    if (isset($data['rol']) && $data['rol']) {
        $rol = htmlspecialchars($data['rol']);
    } else{
        echo json_encode(array('exito' => 0, 'msg' => 'No se envio el nombre del rol'));
        die();
    }
   
    $per->asignar("nombre", $rol);
    $per->validar();
} else {
    echo json_encode(array('exito' => 0, 'msg' => 'No se realizo la peticion correctamente'));
}
