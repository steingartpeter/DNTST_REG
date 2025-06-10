<?php
// backend/index.php

// Start session if using session-based authentication
// session_start(); // Uncomment if using sessions

// Include autoloader
require_once __DIR__ . '/core/autoload.php'; // Or Composer's autoload: require_once __DIR__ . '/vendor/autoload.php';

// Basic Error Handling & JSON Output function
function sendJsonResponse($data, $statusCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// --- Simple Router ---
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove base path if your app is in a subdirectory and .htaccess handles it
// Example: if your API is at /DNTST_REG/backend/
$basePath = '/DNTST_REG/backend'; // Adjust if necessary
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Remove query string from URI for routing
$requestUri = strtok($requestUri, '?');
$uriSegments = explode('/', trim($requestUri, '/'));

$controllerName = isset($uriSegments[0]) && !empty($uriSegments[0]) ? ucfirst($uriSegments[0]) . 'ApiController' : null;
$actionName = isset($uriSegments[1]) && !empty($uriSegments[1]) ? $uriSegments[1] : 'index'; // Default action
// Potentially more segments for IDs, e.g., /appointments/view/123

if ($controllerName && class_exists($controllerName)) {
    $controller = new $controllerName(); // Assumes Database connection is handled in controller constructor or passed

    if (method_exists($controller, $actionName)) {
        // Here you might pass URI parameters or request body to the action
        // For POST/PUT, you'd typically get data from php://input
        $requestData = [];
        if ($requestMethod === 'POST' || $requestMethod === 'PUT') {
            $jsonInput = file_get_contents('php://input');
            $requestData = json_decode($jsonInput, true);
            if (json_last_error() !== JSON_ERROR_NONE && !empty($jsonInput)) {
                sendJsonResponse(['error' => 'Invalid JSON input'], 400);
            }
        }

        // Example: Call action with request data and other URI segments if needed
        // $controller->$actionName($requestData, $uriSegments[2] ?? null, ...);
        // For simplicity, actions can directly access $_GET, $_POST or $requestData
        try {
            $controller->$actionName($requestData); // The action method will call sendJsonResponse
        } catch (Exception $e) {
            // Log the error $e->getMessage()
            sendJsonResponse(['error' => 'An internal server error occurred.'], 500);
        }
    } else {
        sendJsonResponse(['error' => "Action {$actionName} not found in controller {$controllerName}."], 404);
    }
} else {
    sendJsonResponse(['error' => "Controller {$controllerName} not found or route not defined."], 404);
}
