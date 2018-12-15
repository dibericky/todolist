<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    include_once '../config/database.php';
    include_once '../model/task.php';
    include_once '../model/response.php';

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $task = new Task($db);

    if(empty($_GET['id'])){
        http_response_code(400);
        $response->error = "set an id";
        echo $response->getJson();
    }else{
        $id = $_GET['id'];
        $taskFound = $task->deleteById($id);
        if($taskFound["status"] == true){
            http_response_code(200);
            $task_row = $taskFound["data"]->fetch(PDO::FETCH_ASSOC);
            $arr = extractData($task_row);
            $response->data = $arr;
        }else{
            http_response_code(404);
            $response->data = array();
            $response->error = "Task not found";
        }
        echo json_encode($response);
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
?>