<?php
class Customer {
    private $db;
    private $table = 'customer';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCustomers() {
        $query = "SELECT * FROM $this->table";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $customers;
    }

    public function createCustomer($name, $email, $password, $role_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO $this->table (name, email, password, role_id) VALUES (:name, :email, :password, :role_id)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role_id', $role_id);
        
        if ($stmt->execute()) {
            return true; // Customer creation successful
        } else {
            return false; // Customer creation failed
        }
    }
}