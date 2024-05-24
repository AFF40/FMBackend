<?php
require "conexion/conexionBase.php";
class Rol
{
    private $nombre;
    private $con;

    function __construct()
    {
        $this->nombre = "";
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
        $sql = "select * from rol where nombre='$this->nombre'";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);
        if ($re > 0) {
            echo json_encode(array('exito' => 0, 'msg' => "Rol ya esta registrado"));
        } else {
            $this->registrarRol();
        }
    }
    //registrar a la persona
    function registrarRol()
    {

        $this->con->CreateConnection();
        $sql = "insert into rol (nombre) values ('$this->nombre')";
        $resp = $this->con->ExecuteQuery($sql);
        if ($resp) {
            echo json_encode(array('exito' => 1, 'msg' => 'Rol registado correctamente'));
        } else {
            echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar al Rol fue erroneo', 'sql' => $sql));
        }
    }
}