<?php
require "conexion/conexionBase.php";

class Usuario
{
    private $nick;
    private $pass;
    private $activo;
    private $idRol;
    private $idPersona;
    private $con;

    function __construct()
    {
        $this->nick = "";
        $this->pass = "";
        $this->activo = 1;
        $this->idRol = null;
        $this->idPersona = null;
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
         $sql = "select * from usuario where nick='$this->nick' and rol_idrol='$this->idRol'";
         $resp = $this->con->ExecuteQuery($sql);
         $re = $this->con->GetCountAffectedRows($resp);
         if ($re > 0) {
             echo json_encode(array('exito' => 0, 'msg' => "Nick no disponible"));
         } else {
             $this->registrarUsuario();
         }
     }
     //registrar a la persona
     function registrarUsuario()
     {
        $password_segura=password_hash($this->pass, PASSWORD_BCRYPT,['cost'=>4]);
        $token=password_hash($password_segura, PASSWORD_BCRYPT,['cost'=>4]);
         $this->con->CreateConnection();
         $sql = "insert into usuario (nick,pass,activo,token,rol_idrol,persona_idpersona) values ('$this->nick','$password_segura',$this->activo,'$token',$this->idRol,$this->idPersona)";
         $resp = $this->con->ExecuteQuery($sql);
         if ($resp) {
             echo json_encode(array('exito' => 1, 'msg' => 'Usuario registado correctamente'));
         } else {
             echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar a la persona', 'sql' => $sql));
         }
     }

}