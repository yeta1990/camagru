<?php

    class ImageController extends Controller{

        private $imageService;
        private $userService;
        private $jwtService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->imageService = new ImageService();
            $this->userService = new UserService();
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/image/feed', 'getFeed');
            $this->addRoute('GET', 'api/image', 'getImage');
            $this->addRoute('GET', 'api/image/pages', 'getNumOfPages');
            $this->addRoute('POST', 'api/image', 'postImage');
            $this->addRoute('POST', 'api/image/merge', 'mergeImages');
            $this->addRoute('POST', 'api/image/like', 'like');
            $this->addRoute('POST', 'api/image/comment', 'comment');
            $this->addRoute('DELETE', 'api/image', 'deleteImage');
        }


        protected function postImage(){
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $imageName = $this->imageService->postImage();
            $captionSanitized = htmlspecialchars($_POST["caption"]);
            $image = new Image($imageName, $captionSanitized, $userId, "", time());

            $image->create();

            echo json_encode($this->imageService->getImageByUserId($userId));
        }

        protected function deleteImage(){

            $input_parsed = json_decode(file_get_contents('php://input'), true);
            if (isset($input_parsed['image_id'])) {
                $token = $this->jwtService->getBearerToken();
                $userId = $this->jwtService->getUserId($token);
                $image = $this->imageService->getImage($input_parsed["image_id"]);
                if ($image["user_id"] != $userId){
                    echo json_encode(["code" => 403, "message"=>"forbidden"]);
                    http_response_code(403);
                    exit ;
                }
                $this->imageService->deleteImage($input_parsed["image_id"]);
                echo json_encode($this->imageService->getImageByUserId($userId));
                exit;
            }
        }

        protected function getFeed(){
            $queries = array();
            parse_str($_SERVER['QUERY_STRING'], $queries);
            $page = array_key_exists("page", $queries) ? $queries["page"] : 1;
            $results_per_page= array_key_exists("limit", $queries) ? $queries["limit"] : 10;

            if ($results_per_page < 1 || $results_per_page > 20){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"what are you trying to do?"]);
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
            else {
                $token = $this->jwtService->getBearerToken();
                $userId = $this->jwtService->getUserId($token);
                if ($userId){
                    echo json_encode($this->imageService->getImageByUserId($userId));
                }
                else {
                    echo json_encode(["code" => 403, "message"=>"forbidden"]);
                    http_response_code(403);
                    exit ;
                }
            }
        }

        protected function comment(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            if (isset($input_parsed['image_id'], $input_parsed["comment"]) && strlen($input_parsed["comment"]) > 0 && strlen($input_parsed["comment"]) < 257) {
                $token = $this->jwtService->getBearerToken();
                $userId = $this->jwtService->getUserId($token);
                $commentSanitized = htmlspecialchars($input_parsed['comment']);
                $comments = $this->imageService->comment($userId, $commentSanitized, $input_parsed["image_id"]);
                $commenterUser = $this->userService->getUserById($userId)->getObjectVars();
                $image = $this->imageService->getImage($input_parsed["image_id"]);
                $imageOwnerUser = $this->userService->getUserById($image["user_id"])->getObjectVars();
                if ($imageOwnerUser["notifications"]){
                    MailService::send(
                        $imageOwnerUser['email'],
                        $imageOwnerUser['username'],
                        'camagru-albgarci: You have a new comment',
                        'You have a new comment from '. $commenterUser["username"] .' : <a href="http://localhost:8080/image?id=' . $input_parsed["image_id"]. '">View comment</a>');
                }
                echo json_encode(["code" => 200, "comments"=>$comments]);
            }
            else {
                echo json_encode(["code" => 400, "message"=>"error creating comment"]);
                http_response_code(400);
            }
        }

        protected function like(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            if (isset($input_parsed['image_id'])) {
                $likes = $this->imageService->like($input_parsed['image_id']);
                echo json_encode(["code" => 200, "likes"=>$likes]);
            }
            else {
                echo json_encode(["code" => 400, "message"=>"error sending like"]);
                http_response_code(400);
            }

        }

        protected function mergeImages(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            $data = $input_parsed["imageFile"];
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents('uploads/image.png', $data);

            $imageName = $this->imageService->mergeImages('uploads/image.png', $input_parsed["watermark"]);
            if (isset($input_parsed["caption"])){
                $captionSanitized = htmlspecialchars($input_parsed["caption"]);
            }
            else {
                $captionSanitized = "";
            }

            $captionSanitized = htmlspecialchars($input_parsed["caption"]);
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $image = new Image($imageName, $captionSanitized, $userId, "", time());
            $image->create();
            echo json_encode($this->imageService->getImageByUserId($userId));
            exit;

        }
    }
?>