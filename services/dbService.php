<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db/db.db');
    }

    public function __destruct(){
        $this->db->close();
    }
    
    public function lastInsertRowID(){
        $this->db->lastInsertRowID();
    }

    public function query($query){
        return $this->db->prepare($query)->execute();
    }

    public function insert($tableName, $object) {
        /*echo var_dump($object);*/
        $query = $this->objectToQuery($tableName, $object);
        return $this->query($query);
    }

    private function objectToQuery($tableName, $vars){
        //$vars = $obj->getObjectVars();
        $insert_into = "INSERT INTO " . $tableName . " (";
        $values = "VALUES (";
        foreach ($vars as $key => $value) {
            $insert_into .= $key . ',';
            $values .= '"' . strval($value) . '",';
        }
        $insert_into = rtrim($insert_into, ',') . ')';
        $values = rtrim($values, ',') . ')';
        echo $insert_into . ' ' . $values;
        return $insert_into . ' ' . $values;
    }

    public function findById($tableName, $id) {
        $statement = $this->db->prepare("SELECT * FROM {$tableName} WHERE id = :id;");
        $statement->bindValue(':id', $id);
        $result = $statement->execute();
        return $result;
    }
}
?>