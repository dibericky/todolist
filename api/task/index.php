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

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $task = new Task($db);

    $method = $_SERVER['REQUEST_METHOD'];

    if($method == "POST"){
        if(isParamsSet()){
            $user = new User($db);
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
            if($method == "PUT"){
                updateTask($id, $task, $response);
            }else if($method == "GET"){
                getTask($id, $task, $response);
            }else if($method == "DELETE"){
                deleteTask($id, $task, $response);
            }
        }
    }
    echo $response->getJson();

    function getTask($id, $task, $response){
        $taskFound = $task->getById($id);
        if($taskFound){
            http_response_code(200);
            $response->data = $task->getRepresentation();
        }else{
            http_response_code(404);
            $response->data = array();
            $response->error = "Task not found";
        }
    }

    function deleteTask($id, $task, $response){
        $taskFound = $task->deleteById($id);
        if(!$taskFound){
            http_response_code(404);
            $response->data = array();
            $response->error = "Task not found";
            return;
        }
        http_response_code(200);
        $response->data = $task->getRepresentation();
    }

    function updateTask($id, $task, $response){
        $taskFound = $task->getById($id);
        if($taskFound){
            $arrToUpdate = array("state"=>$task->state, "title"=>$task->title, "description"=>$task->description);
            parse_str(file_get_contents("php://input"),$_PUT);
            //first array passed by reference, it's gonna be modified
            addIfPresent($arrToUpdate, "state", $_PUT);
            addIfPresent($arrToUpdate, "title", $_PUT);
            addIfPresent($arrToUpdate, "description", $_PUT);

            $response->data = array("up"=>$arrToUpdate, "put"=>$_PUT);
            if($task->updateById($id, $arrToUpdate)){
                http_response_code(200);
                $response->data = $arrToUpdate;
            }else{
                http_response_code(500);
                $response->data = array();
                $response->error = "Update failed.";
            }
        }else{
            http_response_code(404);
            $response->data = array();
            $response->error = "Task not found";
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
            !empty($_POST['description']) &&
            !empty($_POST['userId'])
        );
    }

    function createTask($task, $response, $user){
        $userId = $_POST['userId'];
        $getU = $user->getById($userId);
        if(empty($getU)){
            http_response_code(400);
            $response->error = "User does not exist";
        }else{
            $task->userId = $userId;
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
    }
?>