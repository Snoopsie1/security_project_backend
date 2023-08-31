<?php

require_once('../controllers/CustomerController.php');

header("Access-Control-Allow-Origin: http://localhost:3000"); // Allow requests from your React app's domain
header("Access-Control-Allow-Methods: GET, POST, DELETE"); // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type"); // Specify the allowed request headers

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    // Respond to preflight requests (OPTIONS) with a 200 status code
    header("HTTP/1.1 200 OK");
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        $customerId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($customerId !== null) {
            $customer = CustomerController::getCustomerById($customerId);
    
            if ($customer !== null) {
                header('Content-Type: application/json');
                echo json_encode($customer);
            } else {
                echo "Customer not found.";
            }
        } else {
            echo "Customer ID is required.";
        }
        break;

    case 'GET':
        // Handle GET request to retrieve all customers
        $customers = CustomerController::getAllCustomers();

        header('Content-Type: application/json');
        echo json_encode($customers);
        break;

    case 'DELETE':
        // Handle DELETE request to delete a customer
        $customerId = isset($_GET['id']) ? $_GET['id'] : null;
        $customerRole = isset($_GET['role']) ? $_GET['role'] : 0;

        if ($customerId !== null) {
            if (CustomerController::deleteCustomer($customerId, $customerRole)) {
                echo "Customer deleted successfully.";
            } else {
                echo "Unauthorized action.";
            }
        } else {
            echo "Customer ID is required.";
        }
        break;

    case 'POST':
        // Handle POST request to create a customer
        $data = json_decode(file_get_contents("php://input"), true); // Get JSON data

        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $role_id = $data['role_id'];

        $customerController = new CustomerController();
        $result = $customerController->registerCustomer($email, $password, $name, $role_id);

        $response = array(
            "message" => $result
        );

        echo json_encode($response);
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Method not allowed.";
        break;
}
?>
