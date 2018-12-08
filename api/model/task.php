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
            $query = "SELECT * FROM ".$tb_name."
                    WHERE id = ".$id;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
        
    }
?>