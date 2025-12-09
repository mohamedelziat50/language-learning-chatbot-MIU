<?php
require_once 'Topic.php';

class TopicRepository {
    private $conn;
    private $table = 'topics';

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

    public function getByLanguage($language_id) {
        $language_id = intval($language_id);
        $result = $this->conn->query("SELECT * FROM $this->table WHERE language_id = $language_id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(Topic $topic) {
        $title = $this->conn->real_escape_string($topic->getTitle());
        $description = $this->conn->real_escape_string($topic->getDescription());
        $language_id = intval($topic->getLanguageId());
        $query = "INSERT INTO $this->table (title, language_id, description)
                  VALUES ('$title', $language_id, '$description')";
        return $this->conn->query($query);
    }

    public function update(Topic $topic) {
        $id = intval($topic->getId());
        $title = $this->conn->real_escape_string($topic->getTitle());
        $description = $this->conn->real_escape_string($topic->getDescription());
        $query = "UPDATE $this->table SET title = '$title', description = '$description' WHERE id = $id";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = intval($id);
        $query = "DELETE FROM $this->table WHERE id = $id";
        return $this->conn->query($query);
    }
}
?>
