<?php

require_once "services/dbService.php";
require_once "services/userService.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$db = new DbService();
$userService = new UserService();
$user = $userService->createUser("d","d","d");
$user->print();
$foundUser = $userService->getUserById(6);
$foundUser->print();


if ($uri === '/demo' && $method === 'GET') {
    require_once 'controllers/demoController.php';
}
elseif ($uri === '/register' && $method === 'POST') {
    echo "register";
} elseif ($uri === '/login' && $method === 'POST') {
    echo "login";
} else {
    http_response_code(404);
    echo "Hello";
}


?>