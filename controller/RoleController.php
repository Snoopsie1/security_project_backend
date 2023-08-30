<?php
require_once('../classes/Role.php');
require_once('../config.php'); // Assuming you've configured your database connection here

ini_set('display_errors', 1);
error_reporting(E_ALL);

class RoleController {
    public static function getRoleById($roleId) {
        $pdo = new Connect(); // Assuming $pdo is your configured database connection

        $stmt = $pdo->prepare("SELECT * FROM role WHERE id = ?");
        if (!$stmt) {
            echo "Query preparation error: " . $pdo->errorInfo()[2];
            die(); // Stop script execution
        }
        
        $stmt->execute([$roleId]);
        if (!$stmt->execute([$roleId])) {
          echo "Query execution error: " . $stmt->errorInfo()[2];
          die(); // Stop script execution
      }
      
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Role($row['id'], $row['type']);
        } else {
            return null;
        }
    }
}
?>
