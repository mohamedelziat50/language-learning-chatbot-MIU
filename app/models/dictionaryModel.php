<?php
class Dictionary {
    private $id;
    private $word;
    private $translation;
    private $language_id;
    private $pronunciation;
    private $example;

    public function __construct($id = null, $word = '', $translation = '', $language_id = null, $pronunciation = '', $example = '') {
        $this->id = $id;
        $this->word = $word;
        $this->translation = $translation;
        $this->language_id = $language_id;
        $this->pronunciation = $pronunciation;
        $this->example = $example;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getWord() {
        return $this->word;
    }

    public function getTranslation() {
        return $this->translation;
    }

    public function getLanguageId() {
        return $this->language_id;
    }

    public function getPronunciation() {
        return $this->pronunciation;
    }

    public function getExample() {
        return $this->example;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setWord($word) {
        $this->word = $word;
    }

    public function setTranslation($translation) {
        $this->translation = $translation;
    }

    public function setLanguageId($language_id) {
        $this->language_id = $language_id;
    }

    public function setPronunciation($pronunciation) {
        $this->pronunciation = $pronunciation;
    }

    public function setExample($example) {
        $this->example = $example;
    }
}
?>
