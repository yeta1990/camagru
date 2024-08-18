<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db/db.db');
    }

    public function __destruct(){
        $this->db->close();
    }

    public function getDb(){
        return $this->db;
    }
    public function query($query){
        return $this->db->prepare($query)->execute();
    }

    public function insert($tableName, $object) {
        $vars = $object->getObjectVars(false);
        $query = $this->objectToInsertQuery($tableName, $vars);
        $result = $this->query($query);
        if ($result){
            return $this->db->lastInsertRowID();
        }
        return -1;
    }

    private function objectToInsertQuery($tableName, $vars){
        $insert_into = "INSERT INTO " . $tableName . " (";
        $values = "VALUES (";
        foreach ($vars as $key => $value) {
            $insert_into .= $key . ',';
            $values .= '"' . strval($value) . '",';
        }
        $insert_into = rtrim($insert_into, ',') . ')';
        $values = rtrim($values, ',') . ')';
        return $insert_into . ' ' . $values;
    }

    public function update($tableName, $object){
        $vars = $object->getObjectVars();
        $query = $this->objectToUpdateQuery($tableName, $vars);
        $result = $this->query($query);
        if ($result){
            return 1;
        }
        return -1;

    }

    private function objectToUpdateQuery($tableName, $vars){

        $update = "UPDATE {$tableName} SET ";

        foreach ($vars as $key => $value) {
            $update .= $key . '="' . strval($value) . '",';
        }
        $update = rtrim($update, ',') . ' ';
        $id = $vars["id"];
        $update .= "WHERE id={$id};";
        return $update;
    }

    public function findByCustomField($tableName, $field, $value) {
        $statement = $this->db->prepare("SELECT * FROM {$tableName} WHERE {$field} = :value;");
        $statement->bindValue(':value', $value);
        $result = $statement->execute();
        return $result;
    }


    public function findById($tableName, $id) {
        return $this->findByCustomField($tableName, "id", $id);
    }
}
?>