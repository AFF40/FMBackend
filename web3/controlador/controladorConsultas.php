<?php
 include "../modelo/consultas.php";
 $per = new Consultas();
$data = json_decode(file_get_contents("php://input"), true);
if($data){
if(isset($data['transaccion']) && $data['transaccion']){
    if($data['transaccion']=="listarPersonas"){
        $per->listarPersonas();
    }else{
        echo json_encode(array('exito' => 0, 'msg' => "No hay esa transaccion"));
    }
}else{
    echo json_encode(array('exito' => 0, 'msg' => "No se envio la transaccion"));
}
}else{
    echo json_encode(array('exito' => 0, 'msg' => "No se enviaron datos"));
}