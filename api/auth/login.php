<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    include_once '../config/database.php';
    include_once '../model/task.php';
    include_once '../model/user.php';
    include_once '../model/response.php';
    include_once 'support.php';

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $user = new User($db);
    $auth = new AuthManager($user);

    $method = $_SERVER['REQUEST_METHOD'];

    if($method == "POST"){ //login
        if(isParamSet()){
            if(isValidData($_POST['nickname'], $_POST['password'], $user, $auth)){
                http_response_code(200);
                $data = array(
                    "id"=> $user->id, 
                    "username"=>$user->nickname,
                    "token"=> $auth->getToken($user->id, $user->nickname));
                $response->data = $data;
            }else{
                http_response_code(401);
                $response->error = "Login failed.";
            }
        }else{
            http_response_code(400);
            $response->error = "Data missed. Insert valid params.";
        }
    }
    echo $response->getJson();

    function isParamSet(){
        return (
            !empty($_POST['nickname']) && 
            !empty($_POST['password'])
        );
    }

    function isValidData($nick, $pwd, $user, $auth){
        $hashPwd = $auth->hashPwd($pwd);
        if($user->getByNickname($nick)){
            if($user->password == $hashPwd){
                return true;
            }
        }
        return false;
    }
?>