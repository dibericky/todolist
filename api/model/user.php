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
        $this->id = $id;
        return $this->select("id=:id", array(":id"=>$id));
    }
    function getByNickname($nickname){
        $nickname = htmlspecialchars(strip_tags($nickname));
        $this->nickname = $nickname;
        return $this->select("nickname=:nick", array(":nick"=>$nickname));
    }
    private function select($whereString, $arrayMap){
        $query = "SELECT * FROM ".$this->tb_name."
                WHERE ".$whereString;
        $stmt = $this->conn->prepare($query);
        if($stmt->execute($arrayMap)){
            if($stmt->rowCount() == 0){
                return false;
            }
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->nickname = $data['nickname'];
            $this->id = $data['id'];
            $this->password = $data['password'];
            return true;
        }
        return false;
    }
    public function create(){
        $query = "INSERT INTO ".$this->tb_name." SET nickname=:nickname, password=:password";
        $stmt = $this->conn->prepare($query);

        $this->nickname = htmlspecialchars(strip_tags($this->nickname));
        
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

    public function getTasks($id, $state){
        $state_str = "";
        if($state > 0){
            $state_str = " AND state=:state";
            $arr_query = array("userId"=> "", "state"=>$state);
        }else{
            $arr_query = array("userId"=> "");
        }
        $selQ = "task.id, title, state, date, description, userId";
        $query = "SELECT ".$selQ." FROM user JOIN task on user.id = task.userId WHERE userId=:userId".$state_str;
        $id = htmlspecialchars(strip_tags($id));
        $arr_query["userId"] = $id;
        $stmt = $this->conn->prepare($query);
        if($stmt->execute($arr_query)){
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            $arr = array();
        }
        return $arr;
    }

    public function getRepresentation(){
        return array(
            "id"=>$this->id,
            "nickname"=>$this->nickname);
    }
    public function getRepresentationUnsafe(){
        return array(
            "id"=>$this->id,
            "nickname"=>$this->nickname,
            "password"=>$this->password);
    }
}
?>