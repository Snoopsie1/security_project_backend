<?php

require_once('../controller/ProductController.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        // Handle GET request to retrieve all products
        $products = ProductController::getAllProducts();

        header('Content-Type: application/json');
        echo json_encode($products);
        break;

    case 'DELETE':
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

    case 'POST':
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

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Method not allowed.";
        break;
}
?>
