<?php

    class ImageController extends Controller{

        private $userService;
        private $jwtService;
        private $authService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->userService = new UserService();
            $this->authService = new AuthService([]);
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/feed', 'getFeed');
            $this->addRoute('POST', 'api/image', 'postImage');
            $this->addRoute('POST', 'api/image/like', 'like');
            $this->addRoute('POST', 'api/image/comment', 'comment');
            $this->addRoute('DELETE', 'api/image', 'deleteImage');
        }

        protected function postImage(){
            $request_body = json_decode(file_get_contents('php://input'), true);
            $image = new Image("url", "caption", 1, "[]", "date");
            $image->create();
        }
    }

?>