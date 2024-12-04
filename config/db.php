<?php
class Database {
    private $host = "localhost";
    private $db_name = "hostdeprojetos_feitoamaoh";
    private $username = "hostdeprojetos_feitoamaoh";
    private $password = "feitoamao123@";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Erro na conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
