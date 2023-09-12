<?php

include_once('../classes/Product.php');
include_once('../config.php'); // Assuming you've configured your database connection here

// Debug Helper
ini_set('display_errors', 1);
error_reporting(E_ALL);

class ProductController {

  //localhost/api/routes/product.php
  public static function getAllProducts() {
        $pdo = new Connect();

        $stmt = $pdo->prepare("SELECT * FROM product");
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['id'], $row['name'], $row['price']);
        }

        return $products;
    }

    public function checkProductName($pdo, $name) {
        $product_query_statement = $pdo->prepare("SELECT * FROM product WHERE name = :name");
        $product_query_statement->bindValue(':name', $name);

        if ($product_query_statement->execute()) {
            $result = $product_query_statement->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        return array();
    }

    public function createProduct($productName, $productPrice, $customerRole) {
        if ($customerRole == 1) {
            $pdo = new Connect();

            $stmt = $pdo->prepare("INSERT INTO product (name, price) VALUES (?, ?)");
            $stmt->bindParam(1, $productName);
            $stmt->bindParam(2, $productPrice);

            $product_data = $this->checkProductName($pdo, $productName);

            if (!empty($product_data)) {
                http_response_code(500);
                return json_encode(array(
                    "status" => 0,
                    "message" => "Product already exists. Try another Product name!",
                ));
            } else {
                if ($stmt->execute()) {
                    http_response_code(200);
                    return json_encode(array(
                        "status" => 1,
                        "message" => "Product has been created",
                    ));
                } else {
                    return "Error: " . $stmt->errorInfo()[2]; // Get the error message
                }
            }
        } else {
            return false;
        }
    }

    public static function deleteProduct($productId, $customerRole) {
        error_log("Customer Role: " . $customerRole);
        if ($customerRole === 1) {
            $pdo = new Connect();
    
            // Use a prepared statement to safely delete the customer by ID
            $stmt = $pdo->prepare("DELETE FROM product WHERE id = :id");
            $stmt->bindValue(':id', $productId);
    
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
}
?>
