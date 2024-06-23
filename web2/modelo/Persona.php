<?php
require_once "conexion/conexionBase.php";

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
        $conn = $this->con->getConnection(); // Obtenemos la conexión
        $sql = "SELECT * FROM usuarios WHERE username = ? OR celular = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $this->username, $this->celular);
        $stmt->execute();
        $result = $stmt->get_result();
        $re = $result->num_rows;
        if ($re > 0) {
            echo json_encode(array('exito' => 0, 'msg' => "El username o el celular ya están registrados"));
        } else {
            $this->registrarUsuario();
        }
        $stmt->close();
    }

    function registrarUsuario()
    {
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $this->con->CreateConnection();
        $conn = $this->con->getConnection(); // Obtenemos la conexión
        $sql = "INSERT INTO usuarios (username, password, celular, id_rol) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $this->username, $hashed_password, $this->celular);
        $resp = $stmt->execute();
        if ($resp) {
            echo json_encode(array('exito' => 1, 'msg' => 'Usuario registrado correctamente'));
        } else {
            echo json_encode(array('exito' => 0, 'msg' => 'Error al registrar al usuario', 'error' => $stmt->error));
        }
        $stmt->close();
    }
}
?>
