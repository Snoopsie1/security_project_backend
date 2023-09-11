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

    //localhost/api/routes/product.php?id=productId&customerRole=customerRole
    public static function deleteProduct($productId, $customerRole) {
        if ($customerRole == 1) {
            global $pdo;

            $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
            $stmt->execute([$productId]);

            http_response_code(200);
            return json_encode(array(
                "status" => 1,
                "message" => "Added product, successfully!",
            ));
        } else {
            http_response_code(500);
            return json_encode(array(
                "status" => 0,
                "message" => "Whoops something went wrong!",
            ));
        }
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
}
?>
