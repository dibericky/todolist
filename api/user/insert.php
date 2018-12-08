<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
if(!empty($_POST['nickname']) && !empty($_POST['password'])){
   
    $getU = $user->getByNickname($_POST['nickname']);
    if(!empty($getU)){
        http_response_code(503);
        $response->error = "User already exists";
    }else{
        $user->nickname = $_POST['nickname'];
        $user->password = $_POST['password'];
        if($user->create()){
                http_response_code(200);
                $arr = array(
                    "id"=> $user->id,
                    "nickname"=> $user->nickname
                );
                $response->data = $arr;
        }else{
            http_response_code(503);
            $response->error = "Unable to create user";
        }
    }
}else{
    http_response_code(400);
    $response->error = "Insert data request";
}
echo $response->getJson();
?>