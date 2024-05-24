<?php
require "conexion/conexionBase.php";

class Persona
{
    private $username;
    private $email;
    private $pass1;
    private $pass2;
    private $con;

    function __construct()
    {
        $this->username = "";
        $this->email = "";
        $this->pass1 = "";
        $this->pass2 = "";
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
        $sql = "SELECT * from user where username='$this->username' or email='$this->email'" ;
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);
        if ($re > 0) {
            echo json_encode(array('exito' => 0, 'msg' => "el usuario ya esta registrado"));
        } else {
            $this->registrarPersona();
        }
    }
    //registrar a la persona
    function registrarPersona()
    {

        $this->con->CreateConnection();
        $sql = "INSERT INTO user (username,email,pass1,pass2) VALUES ('$this->username','$this->email','$this->pass1','$this->pass2')";
        $resp = $this->con->ExecuteQuery($sql);
        if ($resp) {
            echo json_encode(array('exito' => 1, 'msg' => 'Persona registada correctamente'));
        } else {
            echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar a la persona', 'sql' => $sql));
        }
    }
    //funcion para mostrar datos de la base de datos
    //  function mostrar(){
    //      $this->con->CreateConnection();
    //      $sql="select * from personas";
    //      $resp=$this->con->ExecuteQuery($sql);
    //      $re= $this->con->GetCountAffectedRows($resp);
    //      if($re>0){
    //          while($row=$this->con->GetRows($resp)){
    //              echo $row[1];
    //              echo "<br>";
    //          }
    //      }
    // }
}
