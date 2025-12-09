<?php
class Lesson {
    private $id;
    private $title;
    private $content;
    private $topic_id;
    private $difficulty;

    public function __construct($id = null, $title = '', $content = '', $topic_id = null, $difficulty = 'beginner') {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->topic_id = $topic_id;
        $this->difficulty = $difficulty;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getTopicId() {
        return $this->topic_id;
    }

    public function getDifficulty() {
        return $this->difficulty;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setTopicId($topic_id) {
        $this->topic_id = $topic_id;
    }

    public function setDifficulty($difficulty) {
        $this->difficulty = $difficulty;
    }
}
?>
