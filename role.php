<?php 

class Role {
  public $id;
  public $type;

  public function __construct($id, $type) {
    $this->id = $id;
    $this->type = $type;
  }
}

// To fetch admin url/api/role.php=id=1
// To fetch user url/api/role.php=id=2
$roles = [
  new Role(1, 'admin'),
  new Role(2, 'user'),
];

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $roleId = isset($_GET['id']) ? $_GET['id'] : null;

    if($roleId !== null) {
      $foundRole = null;
      foreach($roles as $role) {
        if ($role->id == $roleId) {
          $foundRole = $role;
          break;
        }
      }
    }

    if ($foundRole !== null) {
      $response = [
        'id' => $foundRole->id,
        'type' => $foundRole->type,
      ];
      header('Content-Type: application/json');
      echo json_encode($response);
    } else {
      header('HTTP/1.1 404 Not Found');
      echo "Role not found";
    }
} else {
  header('Content-Type: application/json');
  echo json_encode($roles);
}

?>