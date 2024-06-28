<?php

class conexionBase {
    private $host;
    private $user;
    private $password;
    private $database;
    private $conn;

    public function getConnection() {
        $this->CreateConnection();
        return $this->conn;
    }

    public function __construct() {
        require_once "configDb.php";
        $this->host = HOST;
        $this->user = USER;
        $this->password = PASSWORD;
        $this->database = DATABASE;
    }

    public function CreateConnection() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);

        if ($this->conn->connect_errno) {
            die("Error al conectarse a MariaDB: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);
        }

        // Establecer el juego de caracteres a UTF-8 (opcional, pero recomendado)
        $this->conn->set_charset("utf8");
    }

    public function CloseConnection() {
        $this->conn->close();
    }

    public function ExecuteQuery($sql) {
        $result = $this->conn->query($sql);
        return $result;
    }

    public function GetCountAffectedRows($result) {
        return $this->conn->affected_rows;
    }

    public function GetRows($result) {
        return $result->fetch_assoc(); // Usar fetch_assoc para obtener un arreglo asociativo
    }

    public function SetFreeResult($result) {
        $result->free_result();
    }
}
?>
