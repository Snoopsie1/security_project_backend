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

    public static function createCustomer($name, $email, $password, $role_id) {
        global $pdo;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO customer (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role_id]);

        return true;
    }
}
?>
