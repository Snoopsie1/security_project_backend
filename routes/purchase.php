<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Set CORS headers for preflight OPTIONS request
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("HTTP/1.1 200 OK");
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
        switch (!$action) {
            case true: {   // if their is no query params then run getAllPurchases
                $purchases = PurchaseController::getAllPurchases();
                echo json_encode($purchases);
                break;
            }
            case false: {   // if their is query params then run getPurchaseById
                $purchaseId = array_pop($segments);
                $purchase = PurchaseController::getPurchaseById($purchaseId);
                
                if($purchase !== null) {
                    header('Content-Type: application/json');
                    header('Access-Control-Allow-Origin: *');
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
        // Handle POST request to create a product  THIS IS NOT DONE THIS IS NOT DONE THIS IS NOT DONE THIS IS NOT DONE
        $productName = $_POST['name'];
        $productPrice = $_POST['price'];
        $customerRole = $_POST['role'];

        if (ProductController::createProduct($productName, $productPrice, $customerRole)) {
            echo "Product created successfully.";
        } else {
            echo "Unauthorized action.";
        }
        break;
    }
    default: {
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Method not allowed.";
        break;
    }
}
?>
