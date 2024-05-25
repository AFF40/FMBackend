<?php
// Conexión a la base de datos (ajusta las credenciales según tu configuración)
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
    function validar() {
        $this->con->CreateConnection();
        $sql = "SELECT * FROM usuarios WHERE username = '$this->username'";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);
        
        if ($re > 0) {
            $usuario = mysqli_fetch_assoc($resp);
            $hashed_password = $usuario['password'];
            
            if (password_verify($this->password, $hashed_password)) {
                // Obtén el rol y el id del usuario
                $rol = $usuario['id_rol'];
                $id_usuario = $usuario['id_usuario'];
                
                // Devuelve el rol, el id del usuario y el mensaje de éxito
                echo json_encode(array('exito' => 1, 'msg' => "Bienvenido", 'id_rol' => $rol, 'id_usuario' => $id_usuario));
            } else {
                echo json_encode(array('exito' => 0, 'msg' => "Nombre de usuario o contraseña incorrecto"));
            }
        } else {
            echo json_encode(array('exito' => 0, 'msg' => "Nombre de usuario o contraseña incorrecto"));
        }
    }
}
