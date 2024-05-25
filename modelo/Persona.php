<?
require "conexion/conexionBase.php";

class Persona
{
    private $username;
    private $password;
    private $celular;
    private $con;

    function __construct()
    {
        $this->username = "";
        $this->password = "";
        $this->celular = "";
        $this->con = new conexionBase();
    }

    function asignar($nom, $valor)
    {
        if (property_exists($this, $nom)) {
            $this->$nom = $valor;
        } else {
            throw new Exception("La propiedad $nom no existe en la clase.");
        }
    }

    function validar()
    {
        $this->con->CreateConnection();
        $sql = "SELECT * FROM usuarios WHERE username = '$this->username' OR celular = '$this->celular'";
        $resp = $this->con->ExecuteQuery($sql);
        $re = $this->con->GetCountAffectedRows($resp);
        if ($re > 0) {
            echo json_encode(array('exito' => 0, 'msg' => "El username o el celular ya están registrados"));
        } else {
            $this->registrarUsuario();
        }
    }

    function registrarUsuario()
    {
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $this->con->CreateConnection();
        $sql = "INSERT INTO usuarios (username, password, celular, id_rol) VALUES ('$this->username', '$hashed_password', '$this->celular', 1)";
        $resp = $this->con->ExecuteQuery($sql);
        if ($resp) {
            echo json_encode(array('exito' => 1, 'msg' => 'Usuario registrado correctamente'));
        } else {
            echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar al usuario', 'sql' => $sql));
        }
    }
}
