<?php
require_once 'config.php';
require_once 'models/Customer.php';
require_once 'controllers/CustomerController.php';
require_once 'routing/Router.php';

// Create a database connection instance using the Connect class from config.php
$db = new Connect();

$customerModel = new CustomerModel($db);
$orderModel = new OrderModel($db);

$customerController = new CustomerController($customerModel);
$orderController = new OrderController($orderModel);

$router = new Router();

// Include route files
require_once 'routing/CustomerRoute.php';
require_once 'routing/OrderRoute.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$routeInfo = $router->matchRoute($requestMethod, $requestUri);

if ($routeInfo === false) {
    http_response_code(404);
    echo json_encode(array("message" => "Endpoint not found."));
} else {
    list($handler, $params) = $routeInfo;
    call_user_func_array($handler, $params);
}
