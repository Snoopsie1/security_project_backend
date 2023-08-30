<?php

require_once('../controller/CustomerController.php');

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
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
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];

        if (CustomerController::createCustomer($name, $email, $password, $role_id)) {
            echo "Customer created successfully.";
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
