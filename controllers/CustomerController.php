<?php
class CustomerController {
    private $customerModel;

    public function __construct($customerModel) {
        $this->customerModel = $customerModel;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                $customers = $this->customerModel->getCustomers();
                echo json_encode($customers);
                break;
            // Handle other methods (POST, PUT, DELETE) here
            case 'POST':
                // Handle POST request to create a new customer
                $data = json_decode(file_get_contents("php://input"));
                
                if (!empty($data->name) && !empty($data->role_id)) {
                    $name = $data->name;
                    $role_id = $data->role_id;

                    $result = $this->customerModel->createCustomer($name, $role_id);

                    if ($result) {
                        http_response_code(201); // Created
                        echo json_encode(array("message" => "Customer created."));
                    } else {
                        http_response_code(500); // Internal Server Error
                        echo json_encode(array("message" => "Customer creation failed."));
                    }
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(array("message" => "Missing data."));
                }
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(array("message" => "Method not allowed."));
                break;
        }
    }
}