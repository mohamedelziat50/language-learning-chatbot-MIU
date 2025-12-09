<?php
class Language {
    private $id;
    private $name;
    private $code;

    public function __construct($id = null, $name = '', $code = '') {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCode() {
        return $this->code;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setCode($code) {
        $this->code = $code;
    }
}
?>
