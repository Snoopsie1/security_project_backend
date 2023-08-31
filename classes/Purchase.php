<?php

class Purchase {
    public $id;
    public $products = [];

    public function __construct($id) {
        $this->id = $id;
    }

    public function setProducts($products) {
        $this->products = $products;
    }
}
?>
