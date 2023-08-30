<?php

require_once('../controller/RoleController.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $roleId = isset($_GET['id']) ? $_GET['id'] : null;

    if ($roleId !== null) {
      $role = RoleController::getRoleById($roleId);
        
        if ($role !== null) {
            $response = [
                'id' => $role->id,
                'type' => $role->type,
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo "Role not found";
        }
    } else {
        header('Content-Type: application/json');
        // Return a list of all roles from the database here
    }
}
?>
