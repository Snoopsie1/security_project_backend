<?php

require_once('../controllers/CustomerController.php');

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
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role_id = $_POST['role_id'];

        $customerController = new CustomerController();
        $result = $customerController->registerCustomer($email, $password, $name, $role_id);

        echo $result;
        break;
        
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo "Method not allowed.";
        break;
}
?>
