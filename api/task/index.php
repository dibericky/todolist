<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: *");
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
    $task = new Task($db);
    $user = new User($db);
    $auth = new AuthManager($user);

    if(!$auth->checkToken()){
        http_response_code(401);
        $response->error = "Invalid Token";
    }else{
        $method = $_SERVER['REQUEST_METHOD'];

        if($method == "POST"){
            if(isParamsSet()){
                $userId = $auth->getUserConnectedId();
                $user->getById($userId);
                createTask($task, $response, $user);
            }else{
                http_response_code(400);
                $response->error = "Data missed. Insert valid params.";
            }
        }else{
            if(!isset($_GET['id']) || empty($_GET['id'])){
                http_response_code(400);
                $response->error = "set an id";
            }else{
                $id = $_GET['id'];
                if($task->getById($id)){
                    if($auth->isUserConnectedById($task->userId)){
                        if($method == "PUT"){
                            updateTask($id, $task, $response);
                        }else if($method == "GET"){
                            getTask($id, $task, $response);
                        }else if($method == "DELETE"){
                            deleteTask($id, $task, $response);
                        }
                    }else{
                        http_response_code(401);
                        $response->error = "Permission denied.";
                    }
                }else{
                    http_response_code(404);
                    $response->data = array();
                    $response->error = "Task not found";
                }
            }
        }
    }
    echo $response->getJson();

    function getTask($id, $task, $response){
        http_response_code(200);
        $response->data = $task->getRepresentation();
    }

    function deleteTask($id, $task, $response){
        if($task->deleteById($id)){
            http_response_code(200);
            $response->data = $task->getRepresentation();
        }else{
            http_response_code(500);
            $response->error = "Server error...";
        }
    }

    function updateTask($id, $task, $response){
        $arrToUpdate = array("state"=>$task->state, "title"=>$task->title, "description"=>$task->description);
        parse_str(file_get_contents("php://input"),$_PUT);
        //first array passed by reference, it's gonna be modified
        addIfPresent($arrToUpdate, "state", $_PUT);
        addIfPresent($arrToUpdate, "title", $_PUT);
        addIfPresent($arrToUpdate, "description", $_PUT);
        //$response->data = array("up"=>$arrToUpdate, "put"=>$_PUT);
        if($task->updateById($id, $arrToUpdate)){
            http_response_code(200);
            $response->data = $task->getRepresentation();
        }else{
            http_response_code(500);
            $response->data = array();
            $response->error = "Update failed.";
        }
        
    }

    function extractData($row){
        return array(
            "id" => $row['id'],
            "state"=>$row['state'],
            "date"=>$row['date'],
            "title"=>$row['title'],
            "description"=>$row['description'],
            "userId"=>$row['userId']
        );
    }
    //Side effect on first array, it's passed by reference
    function addIfPresent(& $arr, $key, $arr_src){
        if(array_key_exists($key, $arr_src)){
            $arr[$key] = $arr_src[$key];
        }
    }
    function getPutData(){
        parse_str(file_get_contents("php://input"), $_PUT);

        foreach ($_PUT as $key => $value)
        {
            unset($_PUT[$key]);

            $_PUT[str_replace('amp;', '', $key)] = $value;
        }

        $_REQUEST = array_merge($_REQUEST, $_PUT);
    }

    function isParamsSet(){
        return (
            !empty($_POST['state']) && 
            !empty($_POST['title']) &&
            !empty($_POST['description'])
        );
    }

    function createTask($task, $response, $user){
        $task->userId = $user->id;
        $task->state = $_POST['state'];
        $task->title = $_POST['title'];
        $task->description = $_POST['description'];

        if($task->create()){
            http_response_code(201);
            $response->data = $task->getRepresentation();
        }else{
            http_response_code(503);
            $response->error = "Unable to create task";
        }
    }
?>