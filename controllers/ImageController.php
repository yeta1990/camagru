<?php

    class ImageController extends Controller{

        private $imageService;

        public function __construct(){
            $this->imageService = new ImageService();
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/image/feed', 'getFeed');
            $this->addRoute('GET', 'api/image', 'getImage');
            $this->addRoute('GET', 'api/image/pages', 'getNumOfPages');
            $this->addRoute('POST', 'api/image', 'postImage');
            $this->addRoute('POST', 'api/image/like', 'like');
            $this->addRoute('POST', 'api/image/comment', 'comment');
            $this->addRoute('DELETE', 'api/image', 'deleteImage');
        }

        protected function postImage(){
            $imageName = $this->imageService->postImage();
            $image = new Image($imageName, $_POST["caption"], 1, "", time());

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
            $results_per_page= array_key_exists("limit", $queries) ? $queries["limit"] : 5;
            if ($results_per_page < 5 || $results_per_page > 20){
                echo json_encode(["code" => 400, "message"=>"what are you trying to do?"]);
                http_response_code(400);
                exit;
            }
            echo json_encode($this->imageService->getNumOfPages($results_per_page));
        }

        protected function getImage(){
            $queries = array();
            parse_str($_SERVER['QUERY_STRING'], $queries);
            if (array_key_exists("id", $queries)){
                echo json_encode($this->imageService->getImage($queries["id"]));
            }
        }

        protected function comment(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            if (isset($input_parsed['image_id'], $input_parsed["comment"]) && strlen($input_parsed["comment"]) > 0 && strlen($input_parsed["comment"]) < 257) {
                $comments = $this->imageService->comment($input_parsed['comment'], $input_parsed["image_id"]);
                echo json_encode(["code" => 200, "comments"=>$comments]);
            }
            else {
                echo json_encode(["code" => 400, "message"=>"error creating comment"]);
                http_response_code(400);
            }
        }

        protected function like(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            var_dump ($input_parsed);
            if (isset($input_parsed['image_id'])) {
                $likes = $this->imageService->like($input_parsed['image_id']);
                echo json_encode(["code" => 200, "likes"=>$likes]);
            }
            else {
                echo json_encode(["code" => 400, "message"=>"error creating comment"]);
                http_response_code(400);
            }

        }
    }
?>