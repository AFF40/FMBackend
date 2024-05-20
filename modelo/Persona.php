<?php
require "conexion/conexionBase.php";

class Persona
{
    private $nombre;
    private $papellido;
    private $sapellido;
    private $celular;
    private $direccion;
    private $fechanac;
    private $con;

    function __construct()
    {
        $this->nombre = "";
        $this->papellido = "";
        $this->sapellido = "";
        $this->celular = 0;
        $this->direccion = "";
        $this->fechanac = "";
        $this->con = new conexionBase();
    }
    function asignar($nom, $valor)
    {
        $this->$nom = $valor;
    }


    //valida si la persona existe
    function validar()
    {
        $this->con->CreateConnection();
        $sql = "select * from persona where nombre='$this->nombre' and papellido='$this->papellido'";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);
        if ($re > 0) {
            echo json_encode(array('exito' => 0, 'msg' => "La persona ya esta registrada"));
        } else {
            $this->registrarPersona();
        }
    }
    //registrar a la persona
    function registrarPersona()
    {

        $this->con->CreateConnection();
        $sql = "insert into persona (nombre,papellido,sapellido,celular,direccion,fechanac) values('$this->nombre','$this->papellido','$this->sapellido',$this->celular,'$this->direccion','$this->fechanac')";
        $resp = $this->con->ExecuteQuery($sql);
        if ($resp) {
            echo json_encode(array('exito' => 1, 'msg' => 'Persona registada correctamente'));
        } else {
            echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar a la persona', 'sql' => $sql));
        }
    }
    //funcion para mostrar datos de la base de datos
    // function mostrar(){
    //     $this->con->CreateConnection();
    //     $sql="select * from personas";
    //     $resp=$this->con->ExecuteQuery($sql);
    //     $re= $this->con->GetCountAffectedRows($resp);
    //     if($re>0){
    //         while($row=$this->con->GetRows($resp)){
    //             echo $row[1];
    //             echo "<br>";
    //         }
    //     }
    // }
}
