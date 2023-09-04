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
    public static function deletePurchase($purchaseId, $customerRole) {
        if ($customerRole == 1) {
            $pdo = new Connect();

            $deletePurchaseProductSQL = "DELETE FROM purchase_product WHERE purchase_id = :purchaseID";
            $stmtPurchaseProduct = $pdo->prepare($deletePurchaseProductSQL);
            $stmtPurchaseProduct->bindParam(":purchaseID", $purchaseId, PDO::PARAM_INT);
            
            if (!$stmtPurchaseProduct->execute()) {
                $pdo->rollBack();
                return false; // Deletion of related purchase_product records failed
            }

            $deletePurchaseSQL = "DELETE FROM purchase WHERE id = :purchaseID";
            $stmtPurchase = $pdo->prepare($deletePurchaseSQL);
            $stmtPurchase->bindParam(":purchaseID", $purchaseId, PDO::PARAM_INT);

            if ($stmtPurchase->execute()) {
                $pdo->commit(); // Deletion was successful
                return true;
            } else {
                $pdo->rollBack(); // Deletion of purchase record failed
                return false;
            }
        } else {
            return false;
        }
    }

    public static function createPurchase($productIDs) {
        if ($customerRole == 1) {
            global $pdo;

            $insertPurchaseSQL = "INSERT INTO purchase () VALUES ()";
            $stmtPurchase = $pdo->prepare($insertPurchaseSQL);

            if (!$stmtPurchase->execute()) {
                $pdo->rollBack();
                return false; //Insertion of purchase record failed
            }

            //get the auto-generated purchaseID
            $purchaseID = $pdo->lastInsertId();

            //Insert the purchased products into the pruchase_product table
            foreach ($productIDs as $productID) {
                $insertPurchaseProductSQL = "INSERT INTO purchase_product (product_id, purchase_id) VALUES (:productID, :purchaseID)";
                $stmtPurchseProduct = $pdo->prepare($insertPurchaseProductSQL);
                $stmtPurchseProduct->bindParam(":productID", $productID);
                $stmtPurchseProduct->bindParam(":purchaseID", $purchseID);

                if (!$stmtPurchaseProduct->execute()) {
                    $pdo->rollBack();
                    return false; // Insertion of purchase_product record failed
                }
            }

            // Commit the transaction since everything was succesful
            $pdo->commit();
            return true; // Purchase creation was succesful
        } else {
            return false;
        }
    }
}
?>
