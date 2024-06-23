<?php

require_once "services/dbService.php";
require_once "services/userService.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$db = new DbService();
$userService = new UserService();
//$user = $userService->signUp("l","l","l");
//$user->print();
$foundUser = $userService->getUserById(19);
$foundUser->setUsername("asfsafads");
$userService->changePassword($foundUser->getId(), "spass");
$foundUser->update();
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