<?php

require_once('../controller/OrderController.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET': {
        $action =  $orderId = isset($_GET['id']) ? true : false;
        switch ($action) {
            case false: {
                $orders = OrderController::getAllOrders();
                header('Content-Type: application/json');
                header('Access-Control-Allow-Origin: *');
                echo json_encode($orders);
                break;
            }
            case true: {
                $orderId = isset($_GET['id']) ? $_GET['id']:null;
                
                if($orderId !== null) {
                    $order = OrderController::getOrderById($orderId);
                    if ($order !== null) {
                        header('Content-Type: application/json');
                        header('Access-Control-Allow-Origin: *');
                        echo json_encode($order);
                    } else {
                        echo "Order not found";
                    }
                } else {
                    echo "Order ID is Required";
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
