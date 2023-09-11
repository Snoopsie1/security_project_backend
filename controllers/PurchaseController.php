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
            $productStmt->bindValue(':purchaseId', $row['id'], PDO::PARAM_INT);
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

    public static function getPurchaseByCustomerId($customerId) {
        try {
            $pdo = new Connect();
             // SQL query to fetch purchase IDs for a specific customer by ID
            $purchaseSql = "SELECT p.id
            FROM purchase p
            JOIN customer_purchase cp ON p.id = cp.purchase_id
            WHERE cp.customer_id = :customer_id";

            // Prepare and execute the purchase statement
            $purchaseStmt = $pdo->prepare($purchaseSql);
            $purchaseStmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
            $purchaseStmt->execute();

            $purchases = [];
            while ($row = $purchaseStmt->fetch(PDO::FETCH_ASSOC)) {
                $purchase = new Purchase($row['id']);

                // Fetch product names associated with this order
                $productStmt = $pdo->prepare("SELECT p.id, p.name, p.price FROM product p INNER JOIN purchase_product op ON p.id = op.product_id WHERE op.purchase_id = :purchaseId");
                $productStmt->bindValue(':purchaseId', $row['id'], PDO::PARAM_INT);
                $productStmt->execute();

                $products = [];
                while ($productRow = $productStmt->fetch(PDO::FETCH_ASSOC)) {
                    $products[] = $productRow;
                }

                $purchase->setProducts($products);

                $purchases[] = $purchase;

            }
            return $purchases;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
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

            $deleteCustomerPurchaseSQL = "DELETE FROM customer_purchase WHERE purchase_id = :purchaseID";
            $stmtCustomerPurchase = $pdo->prepare($deleteCustomerPurchaseSQL);
            $stmtCustomerPurchase->bindParam(":purchaseID", $purchaseId, PDO::PARAM_INT);

            if (!$stmtCustomerPurchase->execute()) {
                $pdo->rollBack();
                return false;
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

    public static function createPurchase($productIDs, $customerID, $customerRole) {
        if ($customerRole == 1) {
            $pdo = new Connect();

            // CREATE NEW PURCHASE WITH AUTOINCREMENT ID
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
                $stmtPurchaseProduct = $pdo->prepare($insertPurchaseProductSQL);
                $stmtPurchaseProduct->bindParam(":productID", $productID);
                $stmtPurchaseProduct->bindParam(":purchaseID", $purchaseID);

                if (!$stmtPurchaseProduct->execute()) {
                    $pdo->rollBack();
                    return false; // Insertion of purchase_product record failed
                }
            }

            //CREATE RELATION BETWEEN CUSTOMER AND PURCHASE
            $insertCustomerPurchase = "INSERT INTO customer_purchase (customer_id, purchase_id) VALUES (:customerID, :purchaseID)";
            $stmtCustomerPurchase = $pdo->prepare($insertCustomerPurchase);
            $stmtCustomerPurchase->bindParam(":customerID", $customerID);
            $stmtCustomerPurchase->bindParam(":purchaseID", $purchaseID);

            if (!$stmtCustomerPurchase->execute()) {
                $pdo->rollBack();
                return false;
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
