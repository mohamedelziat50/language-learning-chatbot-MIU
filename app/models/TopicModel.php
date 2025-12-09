<?php
class Topic {
    private $id;
    private $title;
    private $language_id;
    private $description;

    public function __construct($id = null, $title = '', $language_id = null, $description = '') {
        $this->id = $id;
        $this->title = $title;
        $this->language_id = $language_id;
        $this->description = $description;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getLanguageId() {
        return $this->language_id;
    }

    public function getDescription() {
        return $this->description;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setLanguageId($language_id) {
        $this->language_id = $language_id;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
?>
