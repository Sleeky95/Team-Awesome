<?php

function error($message) {
    http_response_code(400);
    $response = array("message" => $message);
    return json_encode($response);
}

function unauthorized($message) {
    http_response_code(401);
    $response = array("message" => $message);
    return json_encode($response);
}

function success($message, $data) {
    http_response_code(200);
    $response = array("message" => $message, "data" => $data);
    return json_encode($response);
}
