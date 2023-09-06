<?php

class Customer {
    public $id;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $purchases;

    public function __construct($id, $name, $email, $password, $role_id, $purchases = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->purchases = $purchases;
    }

    public function setPurchases($purchases) {
        $this->purchases = $purchases;
    }
}
?>
