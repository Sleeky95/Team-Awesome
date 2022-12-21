<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods:POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../controller/AppointmentController.php';
error_reporting(E_ALL ^ E_WARNING);

$controller = new AppointmentController();
$data = json_decode(file_get_contents("php://input"));

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':
        if (isset($data)) {
            echo $controller->markAttendance($data->appointmentId, $data->status);
        } else {
            echo error("Data can not be null");
        }
        break;
    default:
        echo error($_SERVER["REQUEST_METHOD"] . " request method not supported.");
}
