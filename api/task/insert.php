<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../model/user.php';
include_once '../model/response.php';
include_once '../model/task.php';

$database = new Database();
$db = $database->getConnection();
$response = new Response();
$task = new Task($db);

if(!empty($_POST['state']) && 
    !empty($_POST['title']) &&
    !empty($_POST['description']) &&
    !empty($_POST['userId'])){
    
    $userId = $_POST['userId'];
    $user = new User($db);
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
            $arr = array(
                "id"=> $task->id,
                "state"=> $task->state,
                "title"=> $task->title,
                "description"=>$task->description
            );
            $response->data = $arr;
        }else{
            http_response_code(503);
            $response->error = "Unable to create task";
        }
    }
}else{
    http_response_code(400);
    $response->error = $v;
}
echo $response->getJson();
?>