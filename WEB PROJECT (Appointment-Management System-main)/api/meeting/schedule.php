<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../controller/ScheduleController.php';

error_reporting(E_ALL ^ E_WARNING);

$controller = new ScheduleController();
$data = json_decode(file_get_contents("php://input"));

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo $controller->getSchedule($_GET['id']);
        } else if (isset($_GET['supervisorId'])) {
            echo $controller->getScheduleBySupervisor($_GET['supervisorId'], $_GET['semesterId']);
        } else if (isset($_GET['semesterId'])) {
            echo $controller->getSchedulesBySemester($_GET['semesterId']);
        } else {
            echo $controller->getSchedules();
        }
        break;
    case 'POST':
        if (isset($data)) {
            echo $controller->createSchedule($data);
        } else {
            error("Data can not be null");
        }
        break;
    case 'PUT':
        if (isset($data)) {
            echo $controller->updateSchedule($data);
        } else {
            error("Data can not be null");
        }
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $supervisorId = $_GET['supervisorId'];
        echo $controller->deleteSchedule($id, $supervisorId);
        break;
    default:
        echo error($_SERVER["REQUEST_METHOD"] . " request method not supported.");
}