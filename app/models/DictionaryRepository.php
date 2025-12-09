<?php
require_once 'Dictionary.php';

class DictionaryRepository {
    private $conn;
    private $table = 'dictionary';

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

    public function search($keyword, $language_id = null) {
        $keyword = $this->conn->real_escape_string($keyword);
        $query = "SELECT * FROM $this->table WHERE (word LIKE '%$keyword%' OR translation LIKE '%$keyword%')";

        if ($language_id) {
            $language_id = intval($language_id);
            $query .= " AND language_id = $language_id";
        }

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(Dictionary $dictionary) {
        $word = $this->conn->real_escape_string($dictionary->getWord());
        $translation = $this->conn->real_escape_string($dictionary->getTranslation());
        $pronunciation = $this->conn->real_escape_string($dictionary->getPronunciation());
        $example = $this->conn->real_escape_string($dictionary->getExample());
        $language_id = intval($dictionary->getLanguageId());

        $query = "INSERT INTO $this->table (word, translation, language_id, pronunciation, example)
                  VALUES ('$word', '$translation', $language_id, '$pronunciation', '$example')";
        return $this->conn->query($query);
    }

    public function update(Dictionary $dictionary) {
        $id = intval($dictionary->getId());
        $word = $this->conn->real_escape_string($dictionary->getWord());
        $translation = $this->conn->real_escape_string($dictionary->getTranslation());
        $pronunciation = $this->conn->real_escape_string($dictionary->getPronunciation());
        $example = $this->conn->real_escape_string($dictionary->getExample());

        $query = "UPDATE $this->table SET word = '$word', translation = '$translation',
                  pronunciation = '$pronunciation', example = '$example' WHERE id = $id";
        return $this->conn->query($query);
    }

    public function delete($id) {
        $id = intval($id);
        $query = "DELETE FROM $this->table WHERE id = $id";
        return $this->conn->query($query);
    }
}
?>
