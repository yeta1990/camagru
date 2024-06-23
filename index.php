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


$subpath = $path;
array_shift($subpath); //to remove first level of uri, ex: /user

$query_exploded = array();
if ($query){
    parse_str($query, $query_exploded);
}

switch($path[0]){
    case 'user':
        $userController = new UserController();
        $userController->handleRequest($method, $subpath, $query_exploded);
        break;
    case '':
        require_once "views/home.php";
        break ;
    default:
        require_once "views/notFound.php";
        http_response_code(404);
        break ;

}

?>