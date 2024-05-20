<?php
require "conexion/conexionBase.php";

class Login
{
    private $username;
    private $password;
    private $con;

    function __construct()
    {
        $this->username = "";
        $this->password = "";
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

        $sql = "select * from usuarios u join persona p on u.persona_idpersona=p.idpersona
        join rol r on r.idrol=u.rol_idrol where BINARY u.username=?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $resp = $stmt->get_result();
        $da = $resp->fetch_assoc();
        $re = $resp->num_rows;
        if ($re > 0) {
            $res=password_verify($this->password,$da['password']);
            if($res){
                echo json_encode(array('exito' => 1,'data'=>$da),JSON_UNESCAPED_UNICODE);
            }
            else {
                echo json_encode(array('exito' =>0,'msg'=>'Clave incorrecta'));
            }
        } else {
            echo json_encode(array('exito' => 0, 'msg' => "Usuario incorrecto"));
        }
        $stmt->close();
    }
}