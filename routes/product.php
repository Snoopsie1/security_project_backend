<?php

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    // Respond to preflight requests (OPTIONS) with a 200 status code
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("HTTP/1.1 200 OK");
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');

require_once('../controllers/ProductController.php');
require_once('../controllers/CustomerController.php');

switch ($requestMethod) {
    case 'GET':
        // Handle GET request to retrieve all products
        $products = ProductController::getAllProducts();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($products);
        break;

    case 'DELETE':
        $productData = json_decode(file_get_contents("php://input"), true);
        $produdctId = $productData['id']; 
        $customerId = $productData['customer_id'];

        echo $produdctId;
        echo $customerId;
        
        if ($customerId !== null) {
            // Retrieve the user's role using the getCustomerRole method from your CustomerController
            $customerRole = CustomerController::getCustomerRole($customerId);
            
            if ($customerRole !== null) {
                if ($customerRole === 1) {
                    // User is an admin, they have permission to delete
                    if (ProductController::deleteProduct($produdctId, $customerRole)) {
                        echo "Product deleted successfully.";
                    } else {
                        echo "Failed to delete product.";
                    }
                } else {
                    echo "Unauthorized action.";
                }
            } else {
                echo "Failed to retrieve customer role.";
            }
        } else {
            echo "Customer ID is required.";
        }
        break;

    case 'POST':
        // Handle POST request to create a product
        $data = json_decode(file_get_contents("php://input"), true); // Get JSON data
        $productName = $data['name'];
        $productPrice = $data['price'];
        $customerRole = $data['role'];

        $productController = new ProductController();
        $result = $productController->createProduct($productName, $productPrice, $customerRole);
        echo ($result);

        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Method not allowed.";
        break;
}
?>
