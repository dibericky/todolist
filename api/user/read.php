<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../config/database.php';
include_once '../model/user.php';
include_once '../model/response.php';

$database = new Database();
$db = $database->getConnection();

$response = new Response();

$user = new User($db);


// make sure data is not empty
if(!empty($_GET['nickname'])){
    $nickname = $_GET['nickname'];
    
    
    $data = $user->getByNickname($nickname);
    if(empty($data)){
        http_response_code(404);
        $response->error = "User not found";
    }else{
        http_response_code(200);
    }
    $response->data = $data;
    echo $response->getJson();
}else{
    http_response_code(400);
    $response->error = "Insert data request";
    echo $response->getJson();
}
?>