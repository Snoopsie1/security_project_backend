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

    //localhost/api/routes/customer.php?id=customerId&customerRole=customerRole
    public static function deleteCustomer($customerId, $customerRole) {
        if ($customerRole == 1) {
            $pdo = new Connect();

            $stmt = $pdo->prepare("DELETE FROM customer WHERE id = ?");
            $stmt->execute([$customerId]);

            return true;
        } else {
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

    public static function getAllUserDataByEmail($email) {
        // Create a PDO instance and connect to the database
        $pdo = new Connect();

        // Prepare and execute a query to fetch the hashed password based on the email
        $statement = $pdo->prepare("SELECT * FROM customer WHERE email = :email");
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
        $userData = self::getAllUserDataByEmail($email);

        if ($userData === null) {
            // User not found
            http_response_code(500);
            return "User not found";
        }

        if (password_verify($password, $userData['password'])) {
            // Passwords match, user can be logged in

            // Generate a JWT
            $userId = $userData['id'];
            $userName = $userData['name'];
            $userEmail = $userData['email'];
            $userPassword = $userData['password'];
            $userRole = $userData['role_id'];
            //TODO: fix this
            // $secretKey = $_ENV['JWT_SECRET_KEY']; ENV metode virker ikke
            $secretKey = 'e79694081b3d2287e288708062bed5662ce15ea38202c89007dbed8a3d608396';

            $jwtPayload = array(
                "user_id" => $userId,
                "user_name" => $userName,
                "user_email" => $userEmail,
                "user_password" => $userPassword,
                "user_role" => $userRole,
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
