<?php

require_once('../controllers/PurchaseController.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET': {
        $action =  $purchaseId = isset($_GET['id']) ? true : false;
        switch ($action) {
            case false: {
                $purchases = PurchaseController::getAllPurchases();
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                echo json_encode($purchases);
                break;
            }
            case true: {
                $purchaseId = isset($_GET['id']) ? $_GET['id']:null;
                
                if($purchaseId !== null) {
                    $purchase = PurchaseController::getPurchaseById($purchaseId);
                    if ($purchase !== null) {
                        header('Content-Type: application/json');
                        header('Access-Control-Allow-Origin: *');
                        echo json_encode($purchase);
                    } else {
                        echo "Purchase not found";
                    }
                } else {
                    echo "Purchase ID is Required";
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
        // Handle DELETE request to delete a product
        // TODO: Bønne fiks det her
        $productId = isset($_GET['id']) ? $_GET['id'] : null;
        $customerRole = isset($_GET['role']) ? $_GET['role'] : 0;

        if ($productId !== null) {
            if (ProductController::deleteProduct($productId, $customerRole)) {
                echo "Product deleted successfully.";
            } else {
                echo "Unauthorized action.";
            }
        } else {
            echo "Product ID is required.";
        }
        break;
    }
    case 'POST': {
        // Handle POST request to create a product
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
