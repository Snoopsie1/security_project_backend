<?php
$router->addRoute('GET', '|^/customers$|', [$customerController, 'getCustomers']);
$router->addRoute('POST', '|^/customers$|', [$customerController, 'createCustomer']);
