<?php
    class ImageService{

        private $targetDir; 
        private $jwtService;
        private $dbService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->targetDir = "uploads/";
            $this->dbService = new DbService();
        }


        private function saveToDisk(){
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $imageFileType = strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
            $fileNameToSave =  $userId . '-' . time() . '.' . $imageFileType;
            $targetFile = $this->targetDir . $fileNameToSave;
            if (!move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFile)) {
                echo json_encode(["code" => 400, "message"=>"Error uploading file"]);
                http_response_code(400);
                exit ;
            }
            return $targetFile;
        }

        private function verifyImage(){
            if ($_FILES["imageFile"]["size"] > 500000) {
                throw new ErrorException("File too large");
            }
            try {
                //var_dump ($_FILES["imageFile"]);
                $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
            }
            catch (ErrorException $th){
                echo $th;
            }
            if ($check == false) {
                throw new ErrorException("File isn't a valid image");
            }
        }

        public function postImage(){
            try {
                $this->verifyImage();
                $uploadedFileName = $this->saveToDisk();
            } catch (Exception $th) {
                //echo $th;
                echo json_encode(["code" => 400, "message"=>$th->getMessage()]);
                http_response_code(400);
                exit ;
                
            }
            return $uploadedFileName;
            
        }


        public function getFeed($page, $results_per_page){
            $dbConnection = $this->dbService->getDb();
            $offset = ($page - 1) * $results_per_page;
            $query = "SELECT a.id, url, caption, date, username from images a left join users b on a.user_id = b.id ORDER BY a.id desc LIMIT :limit OFFSET :offset;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':limit', $results_per_page);
            $stmt->bindValue(':offset', $offset);
            $images = $stmt->execute();
            $jsonArray = [];
            while($row = $images->fetchArray(SQLITE3_ASSOC)) {
                array_push($jsonArray, $row);
            }
            return $jsonArray;
        }

        public function getNumOfPages($results_per_page){
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT count(*) as num_images FROM images;";
            $stmt = $dbConnection->prepare($query);
            $result = $stmt->execute();
            $result = $result->fetchArray(SQLITE3_ASSOC);
            return ceil($result["num_images"]/$results_per_page);
        }

        public function getComments($image_id){
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT a.date, a.comment, username FROM comments a left join users b on a.user_id = b.id where a.image_id = :id order by a.id desc;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':id', $image_id);
            $result = $stmt->execute();
            $jsonArray = [];
            while($row = $result->fetchArray(SQLITE3_ASSOC)) {
                array_push($jsonArray, $row);
            }
            return $jsonArray;
        }

        public function getImage($id){
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT a.id, url, caption, date, username FROM images a left join users b on a.user_id = b.id where a.id = :id;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
            $result = $result->fetchArray(SQLITE3_ASSOC);
            if ($result){
                $result["comments"] = $this->getComments($id);
                return $result;
            }
            echo json_encode(["code" => 400, "message"=>"bad image id"]);
            http_response_code(400);
            exit;
        }

        public function comment($comment, $image_id){

            if ($this->getImage($image_id)){
                $token = $this->jwtService->getBearerToken();
                $userId = $this->jwtService->getUserId($token);
                $comm = new Comment($comment, $userId, $image_id, time());
                $comm->create();
                return $this->getComments($image_id);
            }
            else{
                echo json_encode(["code" => 400, "message"=>"bad image id"]);
                http_response_code(400);
                exit;
            }
        }

        public function like($image_id){
            if ($this->getImage($image_id)){
                $dbConnection = $this->dbService->getDb();
                $token = $this->jwtService->getBearerToken();
                $userId = $this->jwtService->getUserId($token);
                $query = "UPDATE images SET 
                likes = 
                    case 
                        when length(likes) = 0 then :userId || ','
                        when instr(likes, :userId) > 0 then replace(likes, :userId || ',', '')
                    else likes || :userId || ','
                    end
                WHERE id = :id";
                $stmt = $dbConnection->prepare($query);
                $stmt->bindValue(':userId', $userId);
                $stmt->bindValue(':id', $image_id);
                $result = $stmt->execute();

                $query = "SELECT likes FROM images a where id = :id;";
                $stmt = $dbConnection->prepare($query);
                $stmt->bindValue(':id', $image_id);
                $result = $stmt->execute();
                $likes = trim($result->fetchArray(SQLITE3_ASSOC)["likes"], ',');
                var_dump($likes);
                

                $query = "SELECT username from users a where a.id in ($likes);";
                echo $query;
                //$stmt->bindValue(':id', $image_id);
                //$query = "SELECT username from users a where a.id in (select trim(likes, ',') as likes from images b where b.id = :id);";
                $stmt = $dbConnection->prepare($query);
                //$stmt->bindValue(':id', $image_id);
                $result = $stmt->execute();
                $likes = $result->fetchArray(SQLITE3_ASSOC)["username"];
                var_dump($likes);

                $jsonArray = [];
                while($row = $result->fetchArray(SQLITE3_ASSOC)["username"]) {
                    array_push($jsonArray, $row);
                }
                var_dump($jsonArray);
                return $jsonArray;
            }
            else{
                echo json_encode(["code" => 400, "message"=>"bad image id"]);
                http_response_code(400);
                exit;
            }
            
        }
    }
?>