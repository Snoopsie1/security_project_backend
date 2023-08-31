<?php

require_once('../classes/Product.php');
require_once('../config.php'); // Assuming you've configured your database connection here

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

            return true;
        } else {
            return false;
        }
    }

    public static function createProduct($productName, $productPrice, $customerRole) {
        if ($customerRole == 1) {
            global $pdo;

            $stmt = $pdo->prepare("INSERT INTO product (name, price) VALUES (?, ?)");
            $stmt->execute([$productName, $productPrice]);

            return true;
        } else {
            return false;
        }
    }
}
?>
