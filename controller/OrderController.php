<?php

require_once('../classes/Order.php');
require_once('../config.php'); // Assuming you've configured your database connection here

// Debug Helper
ini_set('display_errors', 1);
error_reporting(E_ALL);

class OrderController {

  //localhost/api/routes/product.php
  public static function getAllOrders() {
        $pdo = new Connect();

        $stmt = $pdo->prepare("SELECT * FROM customer_order");
        $stmt->execute();

        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($row['id']);

            $productIds = explode(',', $row['products']);

            $productNames = [];
            foreach($productIds as $productId) {
                $productStmt = $pdo->prepare("SELECT name FROM product WHERE id = :productId");
                $productStmt->bindValue(':productId', $productId, PDO::PARAM_INT);
                $productStmt->execute();

                $productRow = $productStmt->fetch(PDO::FETCH_ASSOC);
                if ($productRow) {
                    $productNames[] = $productRow['name'];
                }
            }

            $order->setProducts($productNames);
            $orders[] = $order;
        }

        return $orders;
    }

    public static function getOrderById($orderId) {
        $pdo = new Connect(); // Assuming $pdo is your configured database connection

        $stmt = $pdo->prepare("SELECT * FROM customer_order WHERE id = ?");
        $stmt->execute([$orderId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $order = new Order($row['id']);

        $productIds = explode(',', $row['products']);

        foreach($productIds as $productId) {
            $productStmt = $pdo->prepare("SELECT name FROM product WHERE id = :productId");
            $productStmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $productStmt->execute();

            $productRow = $productStmt->fetch(PDO::FETCH_ASSOC);
            if ($productRow) {
                $productNames[] = $productRow['name'];
            }

            $order->setProducts($productNames);
            $orders[] = $order;
        }
        return $orders;
    }

    //localhost/api/routes/product.php?id=productId&customerRole=customerRole
    public static function deleteOrder($orderId, $customerRole) {
        if ($customerRole == 1) {
            global $pdo;

            $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
            $stmt->execute([$orderId]);

            return true;
        } else {
            return false;
        }
    }

    public static function createOrder($products) {
        if ($customerRole == 1) {
            global $pdo;

            $stmt = $pdo->prepare("INSERT INTO customer_ordeer (products) VALUES (?)");
            $stmt->execute([$products]);

            return true;
        } else {
            return false;
        }
    }
}
?>
