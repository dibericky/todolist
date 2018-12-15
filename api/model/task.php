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
            if($stmt->rowCount() == 0){
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row["id"];
            $this->state = $row["state"];
            $this->date = $row["date"];
            $this->title = $row["title"];
            $this->description = $row["description"];
            $this->userId = $row["description"];

            return true;
        }

        function create(){
            $this->date = date_create('now')->format('Y-m-d H:i:s');

            $query = "INSERT INTO ".$this->tb_name." SET 
                state=:state, title=:title, description=:description, userId=:userId, date=:date";
            $stmt = $this->conn->prepare($query);

            $this->cleanAndBind($stmt, ":state", $this->state);
            $this->cleanAndBind($stmt, ":title", $this->title);
            $this->cleanAndBind($stmt, ":description", $this->description);
            $this->cleanAndBind($stmt, ":userId", $this->userId);
            $this->cleanAndBind($stmt, ":date", $this->date);
            
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

        public function deleteById($id){
            $taskToDel = $this->getById($id);
            if(!$taskToDel){
                return false;
            }
            $id = htmlspecialchars(strip_tags($id));
            $query = "DELETE FROM ".$this->tb_name."
                    WHERE id = ".$id;
            
            $stmt = $this->conn->prepare($query);
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        public function updateById($id, $arr){
            $query = "UPDATE ".$this->tb_name." SET 
                state=:state, title=:title, description=:description WHERE id =:id";
            $stmt = $this->conn->prepare($query);
            $this->cleanAndBind($stmt, ":state", $arr["state"]);
            $this->cleanAndBind($stmt, ":title",  $arr["title"]);
            $this->cleanAndBind($stmt, ":description",  $arr["description"]);
            $this->cleanAndBind($stmt, ":id",  $id);
            if($stmt->execute()){
                $this->state = $arr["state"];
                $this->description = $arr["description"];
                $this->title = $arr["title"];
                return true;
            }
            return false;
        }

        public function getRepresentation(){
            return array(
                "id"=>$this->id,
                "userId"=>$this->userId,
                "title"=>$this->title,
                "description"=>$this->description,
                "state"=>$this->state,
                "date"=>$this->date);
        }
        
    }
?>