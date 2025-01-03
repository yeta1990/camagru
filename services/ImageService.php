<?php
    class ImageService{

        private $targetDir; 
        private $jwtService;
        private $dbService;
        private $userService;

        public function __construct(){
            $this->jwtService = new JwtService(getenv("JWT_PASS"));
            $this->targetDir = "uploads/";
            $this->dbService = new DbService();
            $this->userService = new UserService();
        }

        public function checkImageFiletype(){

            if (!isset($_FILES["imageFile"]["tmp_name"]) || $_FILES["imageFile"]["tmp_name"] == ""){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Image too large"]);
                exit;
            }

            //extension
            $imageFileType = strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
            $validExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageFileType, $validExtensions)) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid image extension"]);
                exit;
            }
            //mimetype
            $mimeType = mime_content_type($_FILES["imageFile"]["tmp_name"]);
            $validMimeTypes = ['image/jpeg', 'image/png'];
            if (!in_array($mimeType, $validMimeTypes)) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid MIME type"]);
                exit;
            }

            //magic number
            $file = fopen($_FILES["imageFile"]["tmp_name"], 'rb');
            $magicNumber = fread($file, 4);
            fclose($file);

            $jpegMagicNumbers = ["\xFF\xD8\xFF\xE0", "\xFF\xD8\xFF\xE1", "\xFF\xD8\xFF\xE2"];
            $pngMagicNumber = "\x89\x50\x4E\x47";

            if (!in_array($magicNumber, $jpegMagicNumbers) && $magicNumber !== $pngMagicNumber) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid file content"]);
                exit;
            }

        }

        private function saveToDisk(){
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $imageFileType = strtolower(pathinfo($_FILES["imageFile"]["name"], PATHINFO_EXTENSION));
            

            $fileNameToSave =  $userId . '-' . time() . '.' . $imageFileType;
            $targetFile = $this->targetDir . $fileNameToSave;
            if (!move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetFile)) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Error uploading file"]);
                exit ;
            }


            return $targetFile;
        }

        private function checkImageSize() {

            $maxSize = 5 * 1024 * 1024; //5mb in bytes

            if ($_FILES["imageFile"]["size"] > $maxSize) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message" => "File too large"]);
                exit;
            }
        }
 
        public function checkUploadedImageSize() {

            $maxSize = 5 * 1024 * 1024; //5mb in bytes
            $minWidth = 800; 
            $maxAspectRatio = 16 / 9;
        
            if ($_FILES["imageFile"]["size"] > $maxSize) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message" => "File too large"]);
                exit;
            }
        
            list($width, $height) = getimagesize($_FILES["imageFile"]["tmp_name"]);
        
            if ($width < $minWidth) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message" => "Image width must be at least {$minWidth}px."]);
                exit;
            }
        
            $imageAspectRatio = $width >= $height ? $width / $height : $height / $width;
        
            if ($imageAspectRatio < 1 || $imageAspectRatio > $maxAspectRatio) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message" => "Image must have a minimum aspect ratio of 1:1 and a maximum of 16:9."]);
                exit;
            }
        
        }

        public function checkImageValidity() {
            $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
    
            if ($check === false) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message" => "File isn't a valid image"]);
                exit;
            }
        }

        private function verifyImage(){
            $this->checkImageFiletype();
            $this->checkImageSize(); 
            $this->checkImageValidity();
        }

        public function postImage(){
            try {
                $this->verifyImage();
                $uploadedFileName = $this->saveToDisk();
            } catch (Exception $th) {
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>$th->getMessage()]);
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
                $row["likes"] = $this->getLikesUsernames($row["id"]);
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

        public function deleteImage($id){
            $image = $this->getImage($id);
            $realpath = realpath($image['url']);
            if (is_writable($realpath)){
                unlink($realpath);
            }
            $dbConnection = $this->dbService->getDb();
            $query = "DELETE FROM images where id = :id";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
        }

        public function getImage($id){
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT a.id, url, caption, date, username, a.user_id FROM images a left join users b on a.user_id = b.id where a.id = :id;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':id', $id);
            $result = $stmt->execute();
            $result = $result->fetchArray(SQLITE3_ASSOC);
            if ($result){
                $result["comments"] = $this->getComments($id);
                $result["likes"] = $this->getLikesUsernames($id);
                return $result;
            }
            http_response_code(400);
            echo json_encode(["code" => 400, "message"=>"bad image id"]);
            exit;
        }

        public function getImageByUserId($user_id){
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT a.id, url, caption, date FROM images a where a.user_id = :user_id order by id desc;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':user_id', $user_id);
            $result = $stmt->execute();
            $jsonArray = [];
            while($row = $result->fetchArray(SQLITE3_ASSOC)) {
                array_push($jsonArray, $row);
            }
            return $jsonArray;
        }

        public function comment($userId, $comment, $image_id){

            if ($this->getImage($image_id)){
                $comm = new Comment($comment, $userId, $image_id, time());
                $comm->create();
                return $this->getComments($image_id);
            }
            else{
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"bad image id"]);
                exit;
            }
        }

        private function getLikesUsernames($image_id){

            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $dbConnection = $this->dbService->getDb();
            $query = "SELECT likes FROM images a where id = :id;";
            $stmt = $dbConnection->prepare($query);
            $stmt->bindValue(':id', $image_id);
            $result = $stmt->execute();
            $likes = trim($result->fetchArray(SQLITE3_ASSOC)["likes"], ',');

            $query = "SELECT username from users a where a.id in ($likes);";
            $stmt = $dbConnection->prepare($query);

            $result = $stmt->execute();

            if ($userId){
                $userName = $this->userService->getUserById($userId)->getObjectVars()["username"];
                $jsonArray = [];
                while($res = $result->fetchArray(SQLITE3_ASSOC)){
                    if ($res["username"] == $userName) {
                        array_push($jsonArray, "you");
                    }else {
                        array_push($jsonArray, $res["username"]);
                    }
                }
                return $jsonArray;
            }
            return [""];

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


                $likesUsernames = $this->getLikesUsernames($image_id);
                return $likesUsernames;
            }
            else{
                echo json_encode(["code" => 400, "message"=>"bad image id"]);
                http_response_code(400);
                exit;
            }
            
        }


        private function resizeBackground($back_image, $maxWidth, $maxHeight) {
            $imageInfo = getimagesize($back_image);
            $mimeType = $imageInfo['mime'];

            //if the image is a jpg, convert it to png
            if ($mimeType === 'image/jpeg') {
                $image = imagecreatefromjpeg($back_image);
                $pngPath = preg_replace('/\.(jpg|jpeg)$/i', '.png', $back_image);
                imagepng($image, $pngPath);
                imagedestroy($image); //free memory
                unlink($back_image); //remove original jpg
                $back_image = $pngPath;
            } 

            list($width, $height) = getimagesize($back_image);
        
            if ($width > $maxWidth && $height > $maxHeight) {
                $ratio = max($maxWidth / $width, $maxHeight / $height);
                $newWidth = intval($width * $ratio);
                $newHeight = intval($height * $ratio);
        
                $image = imagecreatefrompng($back_image);
                $imageResized = imagescale($image, $newWidth);
        
                imagealphablending($imageResized, false);
                imagesavealpha($imageResized, true);
        
                return $imageResized;
            } else {
                $image = imagecreatefrompng($back_image);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                return $image;
            }
        }

        public function mergeImages($back_image, $watermark_paths, $maxWidth = 800, $maxHeight = 500) {
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $imageFileType = 'png';
            $fileNameToSave = $userId . '-' . time() . '.' . $imageFileType;
            $targetFile = $this->targetDir . $fileNameToSave;
            $backImagePath = $this->targetDir . $userId . '-' . time() . '-back.' . $imageFileType;

            $background = $this->resizeBackground($back_image, $maxWidth, $maxHeight);
            imagepng($background, $backImagePath);

            if (count($watermark_paths) > 3) { //there are only 3 available watermarks
                echo json_encode(["code" => 400, "message"=>"What are you trying to do?"]);
                http_response_code(400);
                unlink($backImagePath);
                unlink($targetFile);
                exit;
            }
            if (count($watermark_paths) > 0 && strlen($watermark_paths[0] > 0)) {
                foreach ($watermark_paths as $watermark_path) {
                    $watermark = trim(parse_url($watermark_path, PHP_URL_PATH), "/");
                    if (!file_exists($watermark)) {
                        echo json_encode(["code" => 400, "message"=>"Watermark image path provided not exist"]);
                        http_response_code(400);
                        unlink($backImagePath);
                        unlink($targetFile);
                        exit;
                    }
                    
                    $background = $this->imageMergeAlpha($backImagePath, $watermark);
                    imagepng($background, $backImagePath);
                }
            }

            imagepng($background, $targetFile);
            unlink($backImagePath);
            return $targetFile;
        }

        private function imageMergeAlpha($back_image, $watermark_img){
            $image_1 = imagecreatefrompng($back_image);
            $image_2 = imagecreatefrompng($watermark_img);
            imagealphablending($image_1, true);
            imagesavealpha($image_1, true);
            $final_w = imagesx($image_2);
            $final_h = imagesy($image_2);
            imagecopy($image_1, $image_2, 0, 0, 0, 0, $final_w, $final_h);
            return $image_1;
        }

    }
?>