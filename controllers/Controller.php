<?php

    abstract class Controller {
        public function handleRequest($method, $action, $params) {
            switch ($method) {
                case 'GET':
                    $this->handleGet($action, $params);
                    break;
                case 'POST':
                    $this->handlePost($action, $params);
                    break;
                default:
                    header("HTTP/1.0 405 Method Not Allowed");
                    echo "405 Method Not Allowed";
                    break;
            }
        }

        abstract protected function handleGet($action, $params);
        abstract protected function handlePost($action, $params);
    }
?>