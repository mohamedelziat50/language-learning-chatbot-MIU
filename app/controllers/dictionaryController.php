<?php

require_once __DIR__ . "/../models/dictionaryModel.php";

class DictionaryController
{
    private DictionaryModel $model;

    public function __construct()
    {
        $filePath = __DIR__ . "/../data/dictionary.php";
        $this->model = new DictionaryModel($filePath);
    }

    // ---------------------------------------------------
    // List all languages
    // ---------------------------------------------------
    public function languages(): void
    {
        $languages = $this->model->getLanguages();
        echo json_encode($languages);
    }

    // ---------------------------------------------------
    // List categories inside a language
    // ---------------------------------------------------
    public function categories(string $language): void
    {
        $categories = $this->model->getCategories($language);
        echo json_encode($categories);
    }

    // ---------------------------------------------------
    // List words inside a category
    // ---------------------------------------------------
    public function words(string $language, string $category): void
    {
        $words = $this->model->getWords($language, $category);
        echo json_encode($words);
    }

    // ---------------------------------------------------
    // Search for a word in all languages
    // ---------------------------------------------------
    public function search(string $term): void
    {
        $results = $this->model->search($term);
        echo json_encode($results);
    }

    // ---------------------------------------------------
    // Return the whole dictionary
    // ---------------------------------------------------
    public function full(): void
    {
        echo json_encode($this->model->getAll());
    }
}
