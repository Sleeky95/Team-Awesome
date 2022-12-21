<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../controller/MessageController.php';

error_reporting(E_ALL ^ E_WARNING);

$controller = new MessageController();

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'GET':
        $query = $_GET['q'];
        $userId = $_GET['userId'];
        $scheduleId = $_GET['scheduleId'];
        if ($query == 'sent') {
            echo $controller->getSentMessages($scheduleId, $userId);
        } else {
            echo $controller->getReceivedMessages($scheduleId, $userId);
        }
        break;
    default:
        echo error($_SERVER["REQUEST_METHOD"] . " request method not supported.");
}
