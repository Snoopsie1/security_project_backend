<?php

require_once('../classes/Purchase.php');
require_once('../config.php'); // Assuming you've configured your database connection here

// Debug Helper
ini_set('display_errors', 1);
error_reporting(E_ALL);

class PurchaseController {

    //localhost/api/routes/product.php
    public static function getAllPurchases() {
        $pdo = new Connect();

        $stmt = $pdo->prepare("SELECT * FROM purchase");
        $stmt->execute();

        $purchases = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $purchase = new Purchase($row['id']);

            // Fetch product names associated with this order
            $productStmt = $pdo->prepare("SELECT p.id, p.name, p.price FROM product p INNER JOIN purchase_product op ON p.id = op.product_id WHERE op.purchase_id = :purchaseId");
            $productStmt->bindValue(':purchaseId', $row['products'], PDO::PARAM_INT);
            $productStmt->execute();

            $products = [];
            while ($productRow = $productStmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = $productRow;
            }

            $purchase->setProducts($products);

            $purchases[] = $purchase;

        }

        return $purchases;
    }


    public static function getPurchaseById($purchaseId) {
        $pdo = new Connect(); // Assuming $pdo is your configured database connection

        $stmt = $pdo->prepare("SELECT * FROM purchase WHERE id = ?");
        $stmt->execute([$purchaseId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $purchase = new Purchase($row['id']);

        $productStmt = $pdo->prepare("SELECT p.name FROM product p INNER JOIN purchase_product op ON p.id = op.product_id WHERE op.purchase_id = :purchaseId");
        $productStmt->bindValue(':purchaseId', $row['products'], PDO::PARAM_INT);
        $productStmt->execute();

        $productNames = [];
        while ($productRow = $productStmt->fetch(PDO::FETCH_ASSOC)) {
            $productNames[] = $productRow["name"];
        }

        $purchase->setProducts($productNames);

        return $purchase;
    }

    //localhost/api/routes/product.php?id=productId&customerRole=customerRole
    public static function deletePurchase($orderId, $customerRole) {
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
