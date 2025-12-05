<?php
class Language {
    private $conn;
    private $table = 'languages';

    public function __construct($database) {
        $this->conn = $database;
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM $this->table");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $id = intval($id);
        $result = $this->conn->query("SELECT * FROM $this->table WHERE id = $id");
        return $result->fetch_assoc();
    }

    public function create($name, $code) {
        $name = $this->conn->real_escape_string($name);
        $code = $this->conn->real_escape_string($code);
        $query = "INSERT INTO $this->table (name, code) VALUES ('$name', '$code')";
        return $this->conn->query($query);
    }

    public function update($id, $name, $code) {
        $id = intval($id);
        $name = $this->conn->real_escape_string($name);
        $code = $this->conn->real_escape_string($code);
        $query = "UPDATE $this->table SET name = '$name', code = '$code' WHERE id = $id";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = intval($id);
        $query = "DELETE FROM $this->table WHERE id = $id";
        return $this->conn->query($query);
    }
}
// ASSOC is used to fetch results as associative arrays and that means each row is represented as an array where the keys are the column names.
?>
