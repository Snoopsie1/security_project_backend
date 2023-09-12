<?php

class Logging {
    public $customer_id;
    public $message;
    public $timestamp;

    public function __construct($customer_id, $message, $timestamp) {
        $this->customer_id = $customer_id;
        $this->message = $message;
        $this->timestamp = $timestamp;
    }
}
?>
