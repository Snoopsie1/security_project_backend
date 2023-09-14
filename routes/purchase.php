<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Set CORS headers for preflight OPTIONS request
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("HTTPS/1.1 200 OK");
    exit;
}

require_once('../controllers/PurchaseController.php');

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
        $action = in_array('purchase.php', $segments) && is_numeric(end($segments)) ? true : false;
        
        switch ($action) {  // is id passed or not
            case false: {   // if their is no params then run getAllPurchases
                $purchases = PurchaseController::getAllPurchases();
                echo json_encode($purchases);
                break;
            }
            case true: {   // if their is params then run getPurchaseById
                $customerId = array_pop($segments);
                $purchase = PurchaseController::getPurchaseByCustomerId($customerId);
                
                if($purchase !== null) {
                    echo json_encode($purchase);
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
    case 'DELETE': {
        $urlParts = parse_url($requestUri); //split the URL into parts
        $uriWithoutQuery = $urlParts['path']; //rebuild the URL without the query part

        $segments = explode('/', trim($uriWithoutQuery, '/')); //split URL into segments and select last segment

        $purchaseId = end($segments); //get puchaseId by taking last element in segments
        $customerRole = isset($_GET['role']) ? $_GET['role'] : 0; //get query param
        
        if ($purchaseId !== null) {
            if (PurchaseController::deletePurchase($purchaseId, $customerRole)) {
                echo "purchase deleted successfully.";
            } else {
                echo "Unauthorized action.";
            }
        } else {
            echo "purchase ID is required.";
        }
        break;
    }
    case 'POST': {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true); // true to get an associative array

        if ($data !== null) {
            // $data now contains a Purchase object with products
            // Access them like this: $data->id and $data->products
            PurchaseController::createPurchase($data["productIds"], $data["customerID"], 1);

        } else {
            // Handle JSON decoding error
            http_response_code(400); // Bad Request
            echo "Invalid JSON data";
        }
    }
}
?>
