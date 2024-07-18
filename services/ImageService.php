<?php
    class ImageService{

        private $targetDir; 
        private $jwtService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->targetDir = "uploads/";
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
            
        }
    }
?>