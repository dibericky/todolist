<?php
class User{
    private $conn;
    private $tb_name = "user";
    public $id;
    public $nickname;
    public $password;

    public function __construct($db){
        $this->conn = $db;
    }
    
    function getById($id){
        $id = htmlspecialchars(strip_tags($id));
        return $this->select("id=:id", array(":id"=>$id));
    }
    function getByNickname($nickname){
        $nickname = htmlspecialchars(strip_tags($nickname));
        return $this->select("nickname=:nick", array(":nick"=>$nickname));
    }
    private function select($whereString, $arrayMap){
        $query = "SELECT * FROM ".$this->tb_name."
                WHERE ".$whereString;
        $stmt = $this->conn->prepare($query);
        if($stmt->execute($arrayMap)){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return array();
    }
    public function create(){
        $query = "INSERT INTO ".$this->tb_name." SET nickname=:nickname, password=:password";
        $stmt = $this->conn->prepare($query);

        $this->nickname = htmlspecialchars(strip_tags($this->nickname));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        //bind values
        $stmt->bindParam(":nickname", $this->nickname);
        $stmt->bindParam(":password", $this->password);
        
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    private function exeQuery($query){
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getTasks($id){
        $query = "SELECT * FROM user RIGHT JOIN task on user.id = task.userId WHERE userId=:userId";
        $id = htmlspecialchars(strip_tags($id));
        $stmt = $this->conn->prepare($query);
        if($stmt->execute(array("userId"=>$id))){
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            $arr = array();
        }
        return $arr;
    }
}
?>