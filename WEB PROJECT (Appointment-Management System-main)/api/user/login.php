<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../DbHelper.php';
include_once '../controller/UserController.php';
include_once '../model/User.php';

error_reporting(E_ALL ^ E_WARNING);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new DbHelper();
    $userController = new UserController($db);
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data)) {
        echo $userController->login($data->email, $data->password);
    } else {
        echo error("Data can not be empty");
    }
} else {
    echo error("Invalid request method ".$_SERVER["REQUEST_METHOD"]);
}