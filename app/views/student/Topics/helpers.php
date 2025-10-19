<?php
function getTopicDescription($topicName) {
    $descriptions = [
        'Greetings' => 'Learn how to greet people formally and informally',
        'Food & Dining' => 'Master restaurant vocabulary and ordering phrases',
        'Travel' => 'Essential phrases for airports, hotels, and transportation',
        'Family' => 'Talk about family members and relationships',
        'Shopping' => 'Navigate stores, prices, and purchases confidently',
        'Numbers' => 'Learn counting, prices, and basic mathematics',
        'Weather' => 'Discuss weather conditions and forecasts',
        'Daily Routine' => 'Describe your daily activities and schedule',
        'Basics' => 'Essential words and phrases for beginners',
        'Colors' => 'Learn colors and descriptive vocabulary',
        'Food & Drinks' => 'Food items, drinks, and meal-related vocabulary',
        'Transportation' => 'Public transport, directions, and travel',
        'Hobbies' => 'Talk about interests and free time activities',
        'Introductions' => 'Introduce yourself and others properly'
    ];
    
    return $descriptions[$topicName] ?? 'Learn essential vocabulary and phrases for this topic';
}

function getTopicIcon($topicName) {
    $icons = [
        'Greetings' => 'fas fa-handshake',
        'Food & Dining' => 'fas fa-utensils',
        'Travel' => 'fas fa-plane',
        'Family' => 'fas fa-users',
        'Shopping' => 'fas fa-shopping-cart',
        'Numbers' => 'fas fa-sort-numeric-up',
        'Weather' => 'fas fa-cloud-sun',
        'Daily Routine' => 'fas fa-calendar-day',
        'Basics' => 'fas fa-star',
        'Colors' => 'fas fa-palette',
        'Food & Drinks' => 'fas fa-coffee',
        'Transportation' => 'fas fa-bus',
        'Hobbies' => 'fas fa-gamepad',
        'Introductions' => 'fas fa-user-plus'
    ];
    
    return $icons[$topicName] ?? 'fas fa-book';
}