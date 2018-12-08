<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    include_once '../config/database.php';
    include_once '../model/task.php';
    include_once '../model/response.php';

    $database = new Database();
    $db = $database->getConnection();
    $response = new Response();
    $task = new Task($db);

    $id = $_GET['id'];
    
    if($id == null){
        http_response_code(400);
        $response->error = "set an id";
        echo $reponse->getJson();
    }else{
        $taskFound = $task->getById($id);
        $num = $taskFound->rowCount();
        if($num == 1){
            $task_row = $stmt->fetch(PDO::FETCH_ASSOC);
            $arr = extractData($task_row);
            $response->data = $arr;
        }
        http_response_code(200);
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
        )
    }
?>