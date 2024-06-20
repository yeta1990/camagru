<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db.db');
        echo "new sql";
    }

    public function __destruct(){
        $this->db->close();
    }
}
?>