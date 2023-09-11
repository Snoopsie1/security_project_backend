<?php

include_once('../classes/Customer.php');
include_once('../config.php'); // Assuming you've configured your database connection here
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

// require_once __DIR__ . '../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

class CustomerController {
    //localhost/api/routes/customer.php?id=
    public static function getCustomerById($customerId) {
        $pdo = new Connect();
    
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE id = ?");
        $stmt->execute([$customerId]);
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            $customer = new Customer($row['id'], $row['name'], $row['email'], $row['password'], $row['role_id']);
            return $customer;
        } else {
            return null; // Customer not found
        }
    }

    // Function to get the customer's role by their ID
    public static function getCustomerRole($customerId) {
    $pdo = new Connect(); // Assuming $pdo is your configured database connection

    $stmt = $pdo->prepare("SELECT role_id FROM customer WHERE id = ?");
    $stmt->execute([$customerId]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row['role_id'];
    } else {
        return null; // Customer not found
    }
    }
    

    //localhost/api/routes/customer.php
    public static function getAllCustomers() {
        $pdo = new Connect();

        $stmt = $pdo->prepare("SELECT * FROM customer");
        $stmt->execute();

        $customers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customers[] = new Customer($row['id'], $row['name'], $row['email'], $row['password'], $row['role_id']);
        }

        return $customers;
    }

    public static function editCustomer($customerId, $updatedName, $updatedEmail) {
        $pdo = new Connect();
        $statement = $pdo->prepare("UPDATE customer SET name = :name, email = :email WHERE id = :id");
        $statement->bindValue(':name', $updatedName);
        $statement->bindValue(':email', $updatedEmail);
        $statement->bindValue(':id', $customerId);
        $statement->execute();
    
        if (!$success) {
            error_log("Database update error: " . implode(" ", $stmt->errorInfo()));
        }
    
        return $stmt->rowCount() > 0; // Return true if any rows were updated
    }
     

   // localhost/api/routes/customer.php?id=customerId&customerRole=customerRole
   public static function deleteCustomer($customerId, $customerRole) {
    error_log("Customer Role: " . $customerRole);
    if ($customerRole === 1) {
        $pdo = new Connect();

        // Use a prepared statement to safely delete the customer by ID
        $stmt = $pdo->prepare("DELETE FROM customer WHERE id = :id");
        $stmt->bindValue(':id', $customerId);

        // Execute the statement
        $success = $stmt->execute();

        if (!$success) {
            // Handle the database update error (e.g., log the error message)
            error_log("Database delete error: " . implode(" ", $stmt->errorInfo()));
        }

        return $stmt->rowCount() > 0; // Return true if any rows were deleted
    } else {
        // User doesn't have the necessary role to delete customers
        return false;
    }
}




    public function checkEmail($pdo, $email) {
        $email_query_statement = $pdo->prepare("SELECT * FROM customer WHERE email = :email");
        $email_query_statement->bindValue(':email', $email);

        if ($email_query_statement->execute()) {
            $result = $email_query_statement->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        return array();
    }

    public function registerCustomer($name, $email, $password, $role_id) {
        try {
            $pdo = new Connect(); // Create a new database connection

            // Validate email
            // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //     return "Invalid email format";
            // }

            // Validate password
            // if (strlen($password) < 8) {
            //     return "Password must be at least 8 characters long";
            // }

            // Sanitize inputs
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);
            $role_id = filter_var($role_id, FILTER_SANITIZE_NUMBER_INT);
    
            // Hash the password using bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $customerId = uniqid();

            $customer = new Customer($customerId, $name, $email, $password, $role_id);

            // Prepare and bind the statement
            $stmt = $pdo->prepare("INSERT INTO customer (name, email, password, role_id, purchases) VALUES (?, ?, ?, ?, NULL)");
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $hashedPassword);
            $stmt->bindParam(4, $role_id);
    
            // Execute the statement
            // $executeResult = $stmt->execute();

            // error_log($executeResult);

            $email_data = $this->checkEmail($pdo, $email);
    
            if (!empty($email_data)) {
                http_response_code(500);
                return json_encode(array(
                    "status" => 0,
                    "message" => "Customer already exists. Try another email!",
                ));
            } else {
                if ($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(array(
                        "status" => 1,
                        "message" => "Customer has been created",
                    ));
                } else {
                    return "Error: " . $stmt->errorInfo()[2]; // Get the error message
                }
            } 
    
            // Close the statement
            $stmt = null;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public static function getHashedPasswordAndUserIdByEmail($email) {
        // Create a PDO instance and connect to the database
        $pdo = new Connect();

        // Prepare and execute a query to fetch the hashed password based on the email
        $statement = $pdo->prepare("SELECT id, password FROM customer WHERE email = :email");
        $statement->bindValue(':email', $email);
        $statement->execute();

        // Fetch the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Close the connection
        $pdo = null;

        // If a result is found, return the hashed password, otherwise return null
        return $result ?: null;
    }

    public static function login($email, $password) {
        // Fetch hashed password from the database based on the provided email
        $userData = self::getHashedPasswordAndUserIdByEmail($email);

        if ($userData === null) {
            // User not found
            http_response_code(500);
            return "User not found";
        }

        if (password_verify($password, $userData['password'])) {
            // Passwords match, user can be logged in

            // Generate a JWT
            $userId = $userData['id'];
            //TODO: fix this
            // $secretKey = $_ENV['JWT_SECRET_KEY']; ENV metode virker ikke
            $secretKey = 'e79694081b3d2287e288708062bed5662ce15ea38202c89007dbed8a3d608396';

            $jwtPayload = array(
                "user_id" => $userId,
                "exp" => time() + 3600 // Token expiration time (1 hour from now)
            );

            try {
                $jwt = JWT::encode($jwtPayload, $secretKey, 'HS256');
                // Return the JWT in the response
                http_response_code(200);
                return json_encode(array(
                    "status" => 1,
                    "jwt" => $jwt,
                    "message" => "Customer logged in successfully"
                ));
            } catch (Exception $e) {
                // Handle JWT encoding error
                return $e;
            }
        }
        }
    }
?>
