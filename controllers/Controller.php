<?php
abstract class Controller {

    protected $routes = [];
    protected $query;

    protected function addRoute($method, $path, $action) {
        $this->routes[$method][$path] = $action;
    }

    abstract protected function initRoutes();

    private function setQuery(){
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $query_exploded = array();
        if ($query){
            parse_str($query, $query_exploded);
        }
        $this->query = $query_exploded;

    }

    protected function responseError($code, $message){
        http_response_code($code);
        echo json_encode(["code" => $code, "message" => $message]);
        exit;
    }

    public function __call($method, $args) {
        $this->setQuery();

        $request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if (isset($this->routes[$method][$request])) {
            $action = $this->routes[$method][$request];
            if (method_exists($this, $action)) {
                $this->$action();
                return ;
            }
        }
        $this->notFound();
    }

    protected function notFound() {
        http_response_code(404);
        exit;
    }
}
?>