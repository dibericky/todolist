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
    include_once '../auth/support.php';

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $user = new User($db);
    $auth = new AuthManager($user);
    
    $method = $_SERVER['REQUEST_METHOD'];

    if($method == "POST"){
        if(isParamsSet()){
            createUser($user, $response, $auth);
        }else{
            http_response_code(400);
            $response->error = "Data missed. Insert valid params.";
        }
    }else{
        if(!$auth->checkToken()){
            http_response_code(401);
            $response->error = "Invalid Token";
        }else{
            if(!isset($_GET['id']) || empty($_GET['id'])){
                http_response_code(400);
                $response->error = "set an id";
            }else{
                $id = $_GET['id'];
                if($auth->isUserConnectedById($id)){
                    if($method == "PUT"){
                    //  updateTask($id, $task, $response);
                    }else if($method == "GET"){
                        getUser($id, $user, $response);
                    }
                }else{
                    http_response_code(401);
                    $response->error = "Permission denied.";
                }
            }
        }
    }

    echo $response->getJson();

    function getUser($id, $user, $response){
        if(!($user->getById($id))){
            http_response_code(404);
            $response->error = "User not found";
            $response->data = array();
        }else{
            http_response_code(200);
            $response->data = $user->getRepresentation();
        }
    }


    function isParamsSet(){
        return (
            !empty($_POST['nickname']) && 
            !empty($_POST['password'])
        );
    }

    function createUser($user, $response, $auth){
            if($user->getByNickname($_POST['nickname'])){
                http_response_code(503);
                $response->error = "User already exists";
            }else{
                $user->nickname = $_POST['nickname'];
                $user->password = $auth->hashPwd($_POST['password']);
                if($user->create()){
                        http_response_code(200);
                        $arr = array(
                            "id"=> $user->id,
                            "nickname"=> $user->nickname,
                            "token"=> $auth->getToken($user->id, $user->nickname)
                        );
                        $response->data = $arr;
                }else{
                    http_response_code(503);
                    $response->error = "Unable to create user";
                }
            }
    }
?>