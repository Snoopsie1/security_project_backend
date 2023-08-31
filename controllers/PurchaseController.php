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
            $Purchase = new Purchase($row['id']);

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

            $Purchase->setProducts($productNames);
            $purchases[] = $Purchase;
        }

        return $purchases;
    }

    public static function getPurchaseById($purchaseId) {
        $pdo = new Connect(); // Assuming $pdo is your configured database connection

        $stmt = $pdo->prepare("SELECT * FROM purchase WHERE id = ?");
        $stmt->execute([$purchaseId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $Purchase = new Purchase($row['id']);

        $productIds = explode(',', $row['products']);

        foreach($productIds as $productId) {
            $productStmt = $pdo->prepare("SELECT name FROM product WHERE id = :productId");
            $productStmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $productStmt->execute();

            $productRow = $productStmt->fetch(PDO::FETCH_ASSOC);
            if ($productRow) {
                $productNames[] = $productRow['name'];
            }

            $Purchase->setProducts($productNames);
            $purchases[] = $Purchase;
        }
        return $purchases;
    }

    //localhost/api/routes/product.php?id=productId&customerRole=customerRole
    public static function deletePurchase($purchaseId, $customerRole) {
        if ($customerRole == 1) {
            global $pdo;

            $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
            $stmt->execute([$purchaseId]);

            return true;
        } else {
            return false;
        }
    }

    public static function createPurchase($products, $customerRole) {
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
