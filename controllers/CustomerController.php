<?php

require_once('../classes/Customer.php');
require_once('../config.php'); // Assuming you've configured your database connection here

// Debug Helper
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
            global $pdo;

            $stmt = $pdo->prepare("DELETE FROM customer WHERE id = ?");
            $stmt->execute([$customerId]);

            return true;
        } else {
            return false;
        }
    }

    public function registerCustomer($name, $email, $password, $role_id) {
        try {
            $conn = new Connect(); // Create a new database connection

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Invalid email format";
            }

            // Validate password
            if (strlen($password) < 8) {
                return "Password must be at least 8 characters long";
            }

            // Sanitize inputs
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $role_id = filter_var($role_id, FILTER_SANITIZE_NUMBER_INT);

            // Hash the password using bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and bind the statement
            $stmt = $conn->prepare("INSERT INTO Customer (email, password, name, role_id) VALUES (?, ?, ?, ?)");
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $hashedPassword);
            $stmt->bindParam(3, $name);
            $stmt->bindParam(4, $role_id);

            // Execute the statement
            if ($stmt->execute()) {
                return "Registration successful!";
            } else {
                return "Error: " . $stmt->errorInfo()[2]; // Get the error message
            }

            // Close the statement
            $stmt = null;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
?>
