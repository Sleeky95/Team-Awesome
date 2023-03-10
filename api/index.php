<?php

    include 'userController.php';
    include 'db.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', $uri);

    if ($uri[1] != 'student' and $uri[1] != 'examiner' and $uri[1] != 'admin') {
        header("HTTP/1.1 404 Not Found");
        exit();
    }


    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $authkey="";
    if (isset($_SERVER["HTTP_AUTHORIZATION"])) {

        $authkey = $_SERVER["HTTP_AUTHORIZATION"];
    }

    (new UserController(DatabaseConnector::getConnection(), $requestMethod, $uri, $authkey))->processRequest();
?>
