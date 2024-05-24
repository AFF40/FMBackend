<?php
require "conexion/conexionBase.php";
class Consultas
{
    private $con;

    function __construct()
    {
        $this->con = new conexionBase();
    }
    function listarPersonas()
    {
        $this->con->CreateConnection();
        $sql = "select * from persona";
        $resp = $this->con->ExecuteQuery($sql);
        $datos=array();
        while($row=mysqli_fetch_assoc($resp)){
            $datos[]=$row;
        }
        $re = $this->con->GetCountAffectedRows($resp);
        if ($re > 0) {
                echo json_encode(array('data' => $datos), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('exito' => 0, 'msg' => "No hay datos"));
        }
    }
}
