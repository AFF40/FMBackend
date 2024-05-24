<?php
class ConexionBase {
    private $host;
    private $user;
    private $password;
    private $database;
    private $conn;
    
    public function getConnection() {
        $this->createConnection();
        return $this->conn;
    }

    public function __construct(){
        require_once "configDb.php"; // Incluir el archivo de configuración
        $this->host = HOST;
        $this->user = USER;
        $this->password = PASSWORD;
        $this->database = DATABASE;
    }

    private function createConnection(){
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if($this->conn->connect_errno) {
            die("Error al conectarse a MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);
        }
    }

    public function closeConnection(){
        $this->conn->close();
    }

    public function executeQuery($sql){
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getCountAffectedRows(){
        return $this->conn->affected_rows;
    }

    public function getRows($result){
        return $result->fetch_row();
    }

    public function setFreeResult($result){
        $result->free_result();
    }
}
?>
