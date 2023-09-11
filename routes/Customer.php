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

require_once('../controllers/CustomerController.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

switch ($requestMethod) {
        case 'GET':
            // Check if 'id' parameter is provided
            $customerId = isset($_GET['id']) ? $_GET['id'] : null;
    
            if ($customerId !== null) {
                // Handle GET request to retrieve a single customer by ID
                $customer = CustomerController::getCustomerById($customerId);
    
                if ($customer !== null) {
                    header('Content-Type: application/json');
                    echo json_encode($customer);
                } else {
                    echo "Customer not found.";
                }
            } else {
                // Handle GET request to retrieve all customers
                $customers = CustomerController::getAllCustomers();
    
                header('Content-Type: application/json');
                echo json_encode($customers);
            }
            break;
    
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

        case 'PUT':
           $putData = json_decode(file_get_contents("php://input"), true); // Parse the request body
            // Assuming that 'id', 'name', and 'email' exist in the request body
            $customerId = $putData['id'];
            $updatedName = $putData['name'];
            $updatedEmail = $putData['email'];
        
            // You can add additional validation here if needed
        
            // Handle the PUT request to edit the customer
            $result = CustomerController::editCustomer($customerId, $updatedName, $updatedEmail);
        
            if ($result) {
                echo "Customer updated successfully.";
            } else {
                echo "Failed to update customer.";
            }
            break;
        
    // case 'GET':
    //     // Handle GET request to retrieve all customers
    //     $customers = CustomerController::getAllCustomers();

    //     header('Content-Type: application/json');
    //     echo json_encode($customers);
    //     break;

    case 'DELETE':
        $customerId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($customerId !== null) {
            // Retrieve the user's role using the getCustomerRole method from your CustomerController
            $customerRole = CustomerController::getCustomerRole($customerId);
            
            if ($customerRole !== null) {
                if ($customerRole === 1) {
                    // User is an admin, they have permission to delete
                    if (CustomerController::deleteCustomer($customerId, $customerRole)) {
                        echo "Customer deleted successfully.";
                    } else {
                        echo "Failed to delete customer.";
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
        // Handle POST request to create a customer
        $data = json_decode(file_get_contents("php://input"), true); // Get JSON data
        if (isset($data['action'])) {
            if ($data['action'] === 'login') {
                // Handle POST request for customer login
                $email = $data['email'];
                $password = $data['password'];

                $result = CustomerController::login($email, $password);

                if ($result) {
                    // Successful login
                    http_response_code(200);
                    echo $result;
                } else {
                    // Failed login
                    http_response_code(500);
                    echo json_encode(array(
                        "status" => 0,
                        "message" => "Login Failed"
                    ));
                }
        } elseif($data['action'] === 'register') {
            $name = $data['name'];
            $email = $data['email'];
            $password = $data['password'];
            $role_id = $data['role_id'];

            $customerController = new CustomerController();
            $result = $customerController->registerCustomer($name, $email, $password, $role_id);

            echo ($result);
        }
    }
    break;

    default:
        http_response_code(503);
        echo json_encode(array(
            "status" => 0,
            "message" => "Access Denied"
        ));
        break;
}
?>
