<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../DbHelper.php';
include_once '../controller/SemesterController.php';

error_reporting(E_ALL ^ E_WARNING);

$db = new DbHelper();
$semesterController = new SemesterController($db);
$data = json_decode(file_get_contents("php://input"));

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET['id'])) {
            echo $semesterController->getSemesterById($_GET['id']);
        } elseif (isset($_GET['name'])) {
            echo $semesterController->getSemesterByName($_GET['name']);
        } else {
            echo $semesterController->getSemesters();
        }
        break;
    case "POST":
        if (isset($data)) {
            echo $semesterController->createSemester($data);
        } else {
            echo error("Data is empty");
        }
        break;
    case "DELETE":
        if (isset($_GET['id'])) {
            echo $semesterController->deleteSemester($_GET['id']);
        } else {
            echo error("No id provided");
        }

}
