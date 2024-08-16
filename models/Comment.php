<?php

    class Comment extends Model{
        
        protected $comment;
        protected $user_id;
        protected $image_id;
        protected $date;

        public function __construct($comment, $user_id, $image_id, $date){
            parent::__construct("comments");
            $this->comment= $comment;
            $this->user_id = $user_id;
            $this->image_id = $image_id;
            $this->date = $date;
        }

        public function create(){
            return parent::create();
        }

        public function getObjectVars($safe = true){
            return parent::getObjectVars($safe);
        }

    }
?>