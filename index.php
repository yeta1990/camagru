<?php

//class autoloader
function myAutoloader($className) {
    $directories = [
        'controllers',
        'services',
        'models'
    ];

    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

spl_autoload_register('myAutoloader');


$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = explode('/', $path);
$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
$method = $_SERVER['REQUEST_METHOD'];


$db = new DbService();
$userService = new UserService();
//$user = $userService->signUp("l","l","l");
//$user->print();
/*
$foundUser = $userService->getUserById(19);
$foundUser->setUsername("asfsafads");
$userService->changePassword($foundUser->getId(), "spass");
$foundUser->update();
$foundUser->print();
*/

switch($path[0]){
    case 'user':
        $userController = new UserController();
        array_shift($path); //to remove /user
        $query_exploded = array();
        if ($query){
            parse_str($query, $query_exploded);
        }
        $userController->handleRequest($method, $path, $query_exploded);
        
        break;
    default:
        echo 'default';
        break ;

}

/*
if ($path[0] === '/demo' && $method === 'GET') {
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
*/

?>