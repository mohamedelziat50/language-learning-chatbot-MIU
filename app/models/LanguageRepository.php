<?php
require_once 'Language.php';

class LanguageRepository {
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

    public function create(Language $language) {
        $name = $this->conn->real_escape_string($language->getName());
        $code = $this->conn->real_escape_string($language->getCode());
        $query = "INSERT INTO $this->table (name, code) VALUES ('$name', '$code')";
        return $this->conn->query($query);
    }

    public function update(Language $language) {
        $id = intval($language->getId());
        $name = $this->conn->real_escape_string($language->getName());
        $code = $this->conn->real_escape_string($language->getCode());
        $query = "UPDATE $this->table SET name = '$name', code = '$code' WHERE id = $id";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = intval($id);
        $query = "DELETE FROM $this->table WHERE id = $id";
        return $this->conn->query($query);
    }
}
?>
