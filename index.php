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

//basic auth check middleware\\
$whitelistRoutes = [
    [
        "path" => "api/user/login",
        "method" => "POST"
    ],
    [
        "path" => "api/user/signup",
        "method" => "POST"
    ],
    [
        "path" => "api/user/verify",
        "method" => "GET"
    ],
    [
        "path" => "api/user/recover",
        "method" => "POST"
    ],
    [
        "path" => "api/image/feed",
        "method" => "GET"
    ],
    [
        "path" => "api/image/pages",
        "method" => "GET"
    ],
    [
        "path" => "api/image/merge",
        "method" => "GET"
    ]
];

$authService = new AuthService($whitelistRoutes);
$authService->checkPath();

//end of basic auth check middleware\\

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = explode('/', $path);


array_shift($path); //to remove "/api" from uri 


//loading controllers
$controllers = [
    'user' => 'UserController',
    'login' => 'LoginController',
    'image' => 'ImageController'
];


if (isset($controllers[$path[0]])) {
    $controllerName = $controllers[$path[0]];
    $controller = new $controllerName();

    $method = $_SERVER['REQUEST_METHOD'];
    $controller->$method();
} else {
    http_response_code(401);
}


?>