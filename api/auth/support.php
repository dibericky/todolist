<?php


    class AuthManager{
        private $connectedUser = array(
            "id"=>0,
            "nickname"=> "");
            
        private $user = null;
        
        public function __construct($user){
            $this->user = $user;
        }

        public function hashPwd($password){
            return md5($password);
        }
        public function getToken($id, $nickname){
            return $id."_".md5($id."stringa".$nickname);
        }
        private function isTokenValid($token){
            $arr = explode("_", $token);
            if(count($arr) != 2){
                return false;
            }
            $id = $arr[0];
            if($this->user->getById($id)){
                $nickname = $this->user->nickname;
                if($this->getToken($id, $nickname) == $token){
                        $this->connectedUser["id"] = $id;
                        $this->connectedUser["nickname"] = $nickname;

                        //echo $connectedUser["id"]." ".$connectedUser["nickname"];
                        return true;
                }
            }
            return false;
        }

        public function checkToken(){
            if(!isset($_GET['token']) || empty($_GET['token'])){
                return false;
            }
            return $this->isTokenValid($_GET['token']);
        }

        public function isUserConnectedById($id){
            if($this->connectedUser["id"] == 0){
                return false;
            }
            return $id == $this->connectedUser["id"];
        }
        public function getUserConnectedId(){
            return $this->connectedUser["id"];
        }
        
    }
?>