<?php

interface AIQuizGeneratorInterface {
    
    public function generate(int $mcqCount, int $shortCount, int $difficulty, string $language): array;
}
