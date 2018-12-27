<?php
// generate json web token
include_once $lib.'firebase/php-jwt/src/BeforeValidException.php';
include_once $lib.'firebase/php-jwt/src/ExpiredException.php';
include_once $lib.'firebase/php-jwt/src/SignatureInvalidException.php';
include_once $lib.'firebase/php-jwt/src/JWT.php';

use \Firebase\JWT\JWT;


    class AuthManager{
        
        private $key = "example_key";

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
            $token = $this->getArrayJwt($id, $nickname);
            return JWT::encode($token, $this->key);
        }
        private function isTokenValid($token){
            try{
                $decoded = JWT::decode($token, $this->key, array('HS256'));
                $this->connectedUser["id"] = $decoded->data->id;
                $this->connectedUser["nickname"] = $decoded->data->username;
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        private function _isTokenValid($token){
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
        
        private function getArrayJwt($id, $nickname){
            return array(
                "data" => array(
                    "id" => $id,
                    "username" => $nickname
                )
             );
        }
    }
?>