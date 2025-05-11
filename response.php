<?php
function sendResponse($status, $data) {
    if (!headers_sent()) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=utf-8");
        http_response_code($status);
    }
    echo json_encode($data);
    exit;
}

function cors() {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, PATCH");
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers,Authorization");
        
        exit(0);
    }
}