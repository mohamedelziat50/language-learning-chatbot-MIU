<?php

#Liskov Substitution Principle applied here as different AI quiz generators can be used interchangeably.
#as default implementation is GroqQuizGenerator, others like OpenAIQuizGenerator can be added later.
interface AIQuizGeneratorInterface {
    
    public function generate(int $mcqCount, int $shortCount, int $difficulty, string $language): array;
}
