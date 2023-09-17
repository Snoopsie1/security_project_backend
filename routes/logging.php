<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Set CORS headers for preflight OPTIONS request
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("HTTP/1.1 200 OK");
    exit;
}

require_once('../controllers/LoggingController.php');

$requestMethod = $_SERVER['REQUEST_METHOD']; //get request method
$requestUri = $_SERVER['REQUEST_URI']; //get URL

// set CORS headers for all other responses
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

switch ($requestMethod) {
    case 'GET': {
        $urlParts = parse_url($requestUri); //split the URL into parts
        $uriWithoutQuery = $urlParts['path']; //rebuild the URL without the query part
        $segments = explode('/', trim($uriWithoutQuery, '/')); //split URL into segments and select last segment
        //check if URL contains "/purchasephp/" followed by a number
        $action = in_array('logging.php', $segments) && is_numeric(end($segments)) ? true : false;
        
        switch ($action) {  // is id passed or not
            case false: {   // if their is no params then run getAllPurchases
                //get all
                break;
            }
            case true: {   // if their is params then run getPurchaseById
                $customerId = array_pop($segments);
                $logs = LoggingController::getLogsById($customerId);
                
                if($logs !== null) {
                    echo json_encode($logs);
                } else {
                    echo "Order not found";
                }
                break;
            }
            default: {
                echo json_encode(array("error" => "Invalid action."));
            }
        }
        break;
    }
    case 'POST': {
        $urlParts = parse_url($requestUri); //split the URL into parts
        $uriWithoutQuery = $urlParts['path']; //rebuild the URL without the query part
        $segments = explode('/', trim($uriWithoutQuery, '/')); //split URL into segments and select last segment
        //check if URL contains "/purchasephp/" followed by a number
        $action = in_array('logging.php', $segments) && is_numeric(end($segments)) ? true : false;

        $json = file_get_contents('php://input');
        $data = json_decode($json, true); // true to get an associative array
        $customerId = array_pop($segments);

        if ($data !== null) {
            // $data now contains a Purchase object with products
            // Access them like this: $data->id and $data->products
            LoggingController::createLog($customerId, $data["message"], 1);

        } else {
            // Handle JSON decoding error
            http_response_code(400); // Bad Request
            echo "Invalid JSON data";
        }
    }
}
?>
