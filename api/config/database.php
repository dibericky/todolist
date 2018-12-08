<?php
class Database{
    private $hostname = "localhost";
    private $db_name = "my_db";
    private $user = "root";
    private $pass = "";
    private $conn;

    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO ("mysql:host=".$this->hostname.";dbname=".$this->db_name, $this->user, $this->pass);
            if($this->conn == null){
                http_response_code(500);
                echo "Connection error: ".$exception->getMessage();
                die();
            }

        }catch(PDOException $exception){
            http_response_code(500);
            echo "Connection error: ".$exception->getMessage();
            die();
        }
        return $this->conn;
    }

    
}
?>