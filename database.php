<?php
function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($conn->connect_error) {
        sendResponse(500, ["error" => "Database connection failed"]);
    }
    
    return $conn;
}