<?php


include_once('../classes/Logging.php');
include_once('../config.php'); // Assuming you've configured your database connection here

// Debug Helper
ini_set('display_errors', 1);
error_reporting(E_ALL);


    class LoggingController {
        public static function getLogsById($customerId) {
            $pdo = new Connect();

            $stmt = $pdo->prepare("SELECT * FROM logging WHERE customer_id = :customer_id");
            $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
            $stmt->execute();

            $logs = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $logs[] = new Logging($row['customer_id'], $row['message'], $row['timestamp']);
            }

            return $logs;

        }

        public static function createLog($customerId, $message) {
            $pdo = new Connect();

            $stmt = $pdo->prepare("INSERT INTO logging (customer_id, message) VALUES (:customer_id, :message)");
            $stmt->bindParam(":customer_id", $customerId);
            $stmt->bindParam(":message", $message);
            $stmt->execute();
        }
    }

?>