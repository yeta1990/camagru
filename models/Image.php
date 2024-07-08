<?php

    class Image extends Model{
        
        protected $url;
        protected $caption;
        protected $user_id;
        protected $likes;
        protected $date;

        public function __construct($url, $caption, $user_id, $likes, $date){
            parent::__construct("images");
            $this->url= $url;
            $this->caption = $caption;
            $this->user_id = $user_id;
            $this->likes = $likes;
            $this->date = $date;
        }

        public function create(){
            return parent::create();
        }

        public function getObjectVars($safe = true){
            return parent::getObjectVars($safe);
        }

        public function getLikes(){
            return explode(',', $this->likes);
        }

        //to do: function to convert string of array [] likes in a real array

    }
?>