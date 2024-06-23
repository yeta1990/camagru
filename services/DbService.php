<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db/db.db');
    }

    public function __destruct(){
        $this->db->close();
    }
    
    public function query($query){
        return $this->db->prepare($query)->execute();
    }

    public function insert($tableName, $object) {
        /*echo var_dump($object);*/
        $vars = $object->getObjectVars();
        $query = $this->objectToInsertQuery($tableName, $vars);
        $result = $this->query($query);
        //to do: catch exceptions like unique constraint failed, empty field, etc.
        if ($result){
            return $this->db->lastInsertRowID();
        }
        return -1;//$this->query($query);
    }

    private function objectToInsertQuery($tableName, $vars){
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

    public function update($tableName, $object){
        $vars = $object->getObjectVars();
        $query = $this->objectToUpdateQuery($tableName, $vars);
        $result = $this->query($query);
        //to do: catch exceptions like unique constraint failed, empty field, etc.
        if ($result){
            echo "Update ok";
        }
        return -1;//$this->query($query);

    }

    private function objectToUpdateQuery($tableName, $vars){

        $update = "UPDATE {$tableName} SET ";

        foreach ($vars as $key => $value) {
            $update .= $key . '="' . strval($value) . '",';
        }
        $update = rtrim($update, ',') . ' ';
        $id = $vars["id"];
        $update .= "WHERE id={$id};";
        echo $update;
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