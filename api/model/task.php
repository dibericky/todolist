<?php
    class Task{
        private $conn;
        private $tb_name = "task";

        public $id;
        public $state;
        public $date;
        public $title;
        public $description;
        public $userId;

        public function __construct($db){
            $this->conn = $db;
        }

        function getById($id){
            $query = "SELECT * FROM ".$this->tb_name."
                    WHERE id = ".$id;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        function create(){
            $query = "INSERT INTO ".$this->tb_name." SET 
                state=:state, title=:title, description=:description, userId=:userId";
            $stmt = $this->conn->prepare($query);

            $this->state = htmlspecialchars(strip_tags($this->state));
            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->userId = htmlspecialchars(strip_tags($this->userId));
            //bind values
            $stmt->bindParam(":state", $this->state);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":userId", $this->userId);
          /*  $this->cleanAndBind($stmt, ":state", $this->state);
            $this->cleanAndBind($stmt, ":title", $this->title);
            $this->cleanAndBind($stmt, ":description", $this->description);
            $this->cleanAndBind($stmt, ":userId", $this->userId);*/
            
            if($stmt->execute()){
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        }
        private function cleanAndBind($stmt, $string, $obj){
            $obj = htmlspecialchars(strip_tags($obj));
            $stmt->bindParam($string, $obj);
        }
        
    }
?>