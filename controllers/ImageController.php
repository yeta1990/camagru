<?php

    class ImageController extends Controller{

        private $imageService;

        public function __construct(){
            $this->imageService = new ImageService();
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/image', 'getFeed');
            $this->addRoute('GET', 'api/image/pages', 'getNumOfPages');
            $this->addRoute('POST', 'api/image', 'postImage');
            $this->addRoute('POST', 'api/image/like', 'like');
            $this->addRoute('POST', 'api/image/comment', 'comment');
            $this->addRoute('DELETE', 'api/image', 'deleteImage');
        }

        protected function postImage(){
            $imageName = $this->imageService->postImage();
            $image = new Image($imageName, "caption", 1, "", time());

            $image->create();
            echo json_encode(["code" => 200, "message"=>"ok"]);
        }

        protected function getFeed(){
            $queries = array();
            parse_str($_SERVER['QUERY_STRING'], $queries);
            $page = array_key_exists("page", $queries) ? $queries["page"] : 1;
            $results_per_page= array_key_exists("limit", $queries) ? $queries["limit"] : 10;

            if ($results_per_page < 1 || $results_per_page > 20){
                echo json_encode(["code" => 400, "message"=>"what are you trying to do?"]);
                http_response_code(400);
                exit;
            }
            echo json_encode($this->imageService->getFeed($page,$results_per_page));
        }

        protected function getNumOfPages(){
            $queries = array();
            parse_str($_SERVER['QUERY_STRING'], $queries);
            $results_per_page= array_key_exists("limit", $queries) ? $queries["limit"] : 10;
            if ($results_per_page < 1 || $results_per_page > 20){
                echo json_encode(["code" => 400, "message"=>"what are you trying to do?"]);
                http_response_code(400);
                exit;
            }
            echo json_encode($this->imageService->getNumOfPages($results_per_page));
        }
    }
?>