<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../controller/AppointmentController.php';

error_reporting(E_ALL ^ E_WARNING);

$controller = new AppointmentController();
$data = json_decode(file_get_contents("php://input"));

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        echo $controller->getAppointmentByUserHistory($_GET['userId'], $_GET['semesterId']);
        break;
}
