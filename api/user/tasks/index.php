<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    include_once '../../config/database.php';
    include_once '../../model/task.php';
    include_once '../../model/user.php';
    include_once '../../model/response.php';
    include_once '../../config/core.php';
    include_once '../../auth/support.php';

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $user = new User($db);
    $auth = new AuthManager($user);

    if(!$auth->checkToken()){
        http_response_code(401);
        $response->error = "Invalid Token";
    }else{
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "POST"){
        
        }else{
            $userId = $auth->getUserConnectedId();
            if($method == "GET"){
                if(isset($_GET['state']) && !empty($_GET['state'])){
                    getUserTasks($userId, $user, $response, $_GET['state']); //get tasks of user connected (id got from token) with state = param
                }else{
                    getUserTasks($userId, $user, $response, 0); //get tasks of user connected (id got from token)       
                }
            }
        }
    }
    echo $response->getJson();

    function getUserTasks($userId, $user, $response, $state){
        http_response_code(200);
        $response->data = $user->getTasks($userId, $state);
    }
    
?>