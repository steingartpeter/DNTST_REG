<?php
//<M>
//×-
//@-FILENAME   : C:\Users\ax07057\PROGRAMOK\XAMPP\xampp\htdocs\DNTST_REG\backend - index.php -@
//@-AUTHOR     : AX07057+G.Gemini-@
//@-CREATED ON : 2025-06-10  -@
//@-DEPENDECIES:
//×-
// @-- /core/autoload.php -@
//-×
//-@
//@-DESCRIPTION :
// backedn endpoint, handle any requests with htaccess.
// -@
//@-CHANGES     :
//×-
// @-- CHANGE_DATE :<br>
// Short description of the applied change...
//-@
//-×
//-×
//</M>

// Start session if using session-based authentication
// session_start(); // Uncomment if using sessions

// Include autoloader
require_once __DIR__ . '/core/autoload.php'; // Or Composer's autoload: require_once __DIR__ . '/vendor/autoload.php';

//<nn>
// Set erro handler class to send standard message from PHP errors to frontend
//</nn>
set_error_handler([ErrorHandler::class, 'handleError']);
set_exception_handler([ErrorHandler::class, 'handleException']);


function sendJsonResponse($data, $statusCode = 200)
{
    //<nn>
    // A helper functin to send back standarad JSON responses
    //</nn>
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// --- Simple Router ---
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

//<nn>
// Remove base path if your app is in a subdirectory and .htaccess handles it
// Example: if your API is at /DNTST_REG/backend/
//</nn>
$basePath = '/DNTST_REG/backend';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
//<nn>
// Remove query string from URI for routing
//</nn>
$requestUri = strtok($requestUri, '?');
$uriSegments = explode('/', trim($requestUri, '/'));

$controllerName = isset($uriSegments[0]) && !empty($uriSegments[0]) ? ucfirst($uriSegments[0]) . 'ApiController' : null;
$actionName = isset($uriSegments[1]) && !empty($uriSegments[1]) ? $uriSegments[1] : 'index'; // Default action


if ($controllerName && class_exists($controllerName)) {
    //<nn>
    // Instantiate the correct controller
    // Assumes Database connection is handled in controller constructor or passed
    //</nn>
    $controller = new $controllerName();

    if (method_exists($controller, $actionName)) {
        //<nn>
        // Here you might pass URI parameters or request body to the action
        // For POST/PUT, you'd typically get data from php://input
        //</nn>
        $requestData = [];
        if ($requestMethod === 'POST' || $requestMethod === 'PUT') {
            $jsonInput = file_get_contents('php://input');
            $requestData = json_decode($jsonInput, true);
            if (json_last_error() !== JSON_ERROR_NONE && !empty($jsonInput)) {
                sendJsonResponse(['error' => 'Invalid JSON input'], 400);
            }
        }

        //<nn>
        // Handling the request itself
        // Example: Call action with request data and other URI segments if needed
        // $controller->$actionName($requestData, $uriSegments[2] ?? null, ...);
        // For simplicity, actions can directly access $_GET, $_POST or $requestData
        //</nn>
        try {
            $controller->$actionName($requestData);
        } catch (Exception $e) {
            // Log the error $e->getMessage()
            //sendJsonResponse(['error' => 'An internal server error occurred.'], 500);
            ErrorHandler::handleException($e);
        }
    } else {
        sendJsonResponse(['error' => "Action {$actionName} not found in controller {$controllerName}."], 404);
    }
} else {
    sendJsonResponse(['error' => "Controller {$controllerName} not found or route not defined."], 404);
}
