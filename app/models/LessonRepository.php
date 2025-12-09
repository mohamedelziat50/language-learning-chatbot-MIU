<?php
require_once 'Lesson.php';

class LessonRepository {
    private $conn;
    private $table = 'lessons';

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

    public function getByTopic($topic_id) {
        $topic_id = intval($topic_id);
        $result = $this->conn->query("SELECT * FROM $this->table WHERE topic_id = $topic_id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(Lesson $lesson) {
        $title = $this->conn->real_escape_string($lesson->getTitle());
        $content = $this->conn->real_escape_string($lesson->getContent());
        $difficulty = $this->conn->real_escape_string($lesson->getDifficulty());
        $topic_id = intval($lesson->getTopicId());

        $query = "INSERT INTO $this->table (title, content, topic_id, difficulty)
                  VALUES ('$title', '$content', $topic_id, '$difficulty')";
        return $this->conn->query($query);
    }

    public function update(Lesson $lesson) {
        $id = intval($lesson->getId());
        $title = $this->conn->real_escape_string($lesson->getTitle());
        $content = $this->conn->real_escape_string($lesson->getContent());
        $difficulty = $this->conn->real_escape_string($lesson->getDifficulty());

        $query = "UPDATE $this->table SET title = '$title', content = '$content', difficulty = '$difficulty' WHERE id = $id";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = intval($id);
        $query = "DELETE FROM $this->table WHERE id = $id";
        return $this->conn->query($query);
    }

    public function getProgress($lesson_id, $user_id) {
        $lesson_id = intval($lesson_id);
        $user_id = intval($user_id);
        $result = $this->conn->query("SELECT * FROM lesson_progress WHERE lesson_id = $lesson_id AND user_id = $user_id");
        return $result->fetch_assoc();
    }

    public function updateProgress($lesson_id, $user_id, $completed = true) {
        $lesson_id = intval($lesson_id);
        $user_id = intval($user_id);
        $query = "INSERT INTO lesson_progress (lesson_id, user_id, completed)
                  VALUES ($lesson_id, $user_id, " . ($completed ? 1 : 0) . ")
                  ON DUPLICATE KEY UPDATE completed = " . ($completed ? 1 : 0);
        return $this->conn->query($query);
    }
}
?>
