<?php
// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'database.php';
require_once 'response.php';


require_once 'routes/user/userMethods.php';
require_once 'routes/user/loginMethod.php';
require_once 'routes/user/getProfile.php';
require_once 'routes/food/getMeal.php';




//error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
//error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']); 

// Handle CORS
cors();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Get the request URI and method
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI if present
$request_uri = strtok($request_uri, '?');

// Remove the base path from the request URI
$request_uri = substr($request_uri, strlen($BASE_PATH));

// Split the URI into segments
$uri_segments = explode('/', trim($request_uri, '/'));

// Get the resource (first segment)
$resource = $uri_segments[0] ?? '';

// API Key validation function
function validateApiKey($apiKey)
{
    // ในตัวอย่างนี้เราใช้ API key แบบ hardcode
    // ในการใช้งานจริง คุณควรเก็บ API key ในฐานข้อมูลหรือไฟล์ config
    $validApiKeys = [
        'BfE8yHcqvc8L2H2mnw3A'
        //'your_api_key_2'
    ];

    return in_array($apiKey, $validApiKeys);
}

// Check API Key for all requests except OPTIONS and test
if ($method !== 'OPTIONS' && $resource !== 'test') {
    $headers = getallheaders();
    //error_log("ALL HEADERS: " . print_r($headers, true));
    
    // ลองตรวจสอบ header แบบ case-insensitive
    $apiKey = null;
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-api-key') {
            $apiKey = $value;
            break;
        }
    }
    
    error_log("FOUND API KEY: " . ($apiKey ?? 'null'));

    if (!$apiKey || !validateApiKey($apiKey)) {
        sendResponse(401, [
            "error" => "Unauthorized. Invalid API key.",
        ]);
        exit;
    }
}

if ($resource === 'test') {
    sendResponse(200, ["message" => "API is working", "method" => $method, "resource" => $resource, "uri" => $request_uri]);
}

// Connect to database
$conn = db_connect();

// Handle different endpoints
switch ($resource) {
    case 'getMeal':
        getMealMethod($method, $conn);
        break;
    case 'getProfile':
        getProfileMethod($method, $conn);
        break;
    case 'userMethods':
        userMethods($method, $conn);
        break;
    case 'login':
        loginMethod($method, $conn);
    case '':
        sendResponse(200, ["message" => "Welcome to the QueueCar API"]);
        break;
    default:
        sendResponse(404, ["error" => "Endpoint not found"]);
        break;
}





// Close database connection
$conn->close();
