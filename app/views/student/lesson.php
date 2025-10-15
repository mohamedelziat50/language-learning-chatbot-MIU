<?php
session_start();
$lang = $_GET['lang'] ?? 'English';
$topic = $_GET['topic'] ?? 'Greetings';

// Initialize progress tracking
if (!isset($_SESSION['user_progress'])) {
    $_SESSION['user_progress'] = [];
}

if (!isset($_SESSION['user_progress'][$lang])) {
    $_SESSION['user_progress'][$lang] = [];
}

if (!isset($_SESSION['user_progress'][$lang][$topic])) {
    $_SESSION['user_progress'][$lang][$topic] = [
        'completed_lessons' => [],
        'score' => 0,
        'words_learned' => 0,
        'last_accessed' => date('Y-m-d H:i:s')
    ];
}

// Real content database for all topics
function getRealLessonContent($language, $topicName) {
    $contentDatabase = [
        "Greetings" => [
            "vocabulary" => [
                ["word" => "Hello", "translation" => getTranslation($language, "Hello"), "pronunciation" => getPronunciation($language, "Hello")],
                ["word" => "Goodbye", "translation" => getTranslation($language, "Goodbye"), "pronunciation" => getPronunciation($language, "Goodbye")],
                ["word" => "Good morning", "translation" => getTranslation($language, "Good morning"), "pronunciation" => getPronunciation($language, "Good morning")],
                ["word" => "Good evening", "translation" => getTranslation($language, "Good evening"), "pronunciation" => getPronunciation($language, "Good evening")],
                ["word" => "How are you?", "translation" => getTranslation($language, "How are you?"), "pronunciation" => getPronunciation($language, "How are you?")],
                ["word" => "I'm fine", "translation" => getTranslation($language, "I'm fine"), "pronunciation" => getPronunciation($language, "I'm fine")],
                ["word" => "Thank you", "translation" => getTranslation($language, "Thank you"), "pronunciation" => getPronunciation($language, "Thank you")],
                ["word" => "You're welcome", "translation" => getTranslation($language, "You're welcome"), "pronunciation" => getPronunciation($language, "You're welcome")]
            ],
            "phrases" => [
                "formal_greeting" => getTranslation($language, "Good morning, how are you today?"),
                "informal_greeting" => getTranslation($language, "Hey, what's up?"),
                "introducing_yourself" => getTranslation($language, "My name is [Name], nice to meet you"),
                "asking_about_someone" => getTranslation($language, "How have you been?"),
                "evening_greeting" => getTranslation($language, "Good evening, how was your day?"),
                "polite_response" => getTranslation($language, "I'm doing well, thank you for asking")
            ],
            "grammar" => [
                "point" => "Formal vs Informal Greetings",
                "explanation" => getGrammarExplanation($language, "greetings_formality"),
                "examples" => [
                    getTranslation($language, "Hello") . " → " . getTranslation($language, "Hi") . " (formal → informal)",
                    getTranslation($language, "Goodbye") . " → " . getTranslation($language, "Bye") . " (formal → informal)",
                    getTranslation($language, "How are you?") . " → " . getTranslation($language, "What's up?") . " (formal → informal)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Alex",
                    "text" => getTranslation($language, "Good morning, Sarah! How are you?"),
                    "translation" => "Good morning, Sarah! How are you?"
                ],
                [
                    "speaker" => "Sarah",
                    "text" => getTranslation($language, "Good morning, Alex! I'm doing well, thank you. How about you?"),
                    "translation" => "Good morning, Alex! I'm doing well, thank you. How about you?"
                ],
                [
                    "speaker" => "Alex",
                    "text" => getTranslation($language, "I'm great, thanks! Have a good day!"),
                    "translation" => "I'm great, thanks! Have a good day!"
                ],
                [
                    "speaker" => "Sarah", 
                    "text" => getTranslation($language, "You too, see you later!"),
                    "translation" => "You too, see you later!"
                ]
            ]
        ],

        "Food" => [
            "vocabulary" => [
                ["word" => "Restaurant", "translation" => getTranslation($language, "Restaurant"), "pronunciation" => getPronunciation($language, "Restaurant")],
                ["word" => "Menu", "translation" => getTranslation($language, "Menu"), "pronunciation" => getPronunciation($language, "Menu")],
                ["word" => "Water", "translation" => getTranslation($language, "Water"), "pronunciation" => getPronunciation($language, "Water")],
                ["word" => "Coffee", "translation" => getTranslation($language, "Coffee"), "pronunciation" => getPronunciation($language, "Coffee")],
                ["word" => "Bread", "translation" => getTranslation($language, "Bread"), "pronunciation" => getPronunciation($language, "Bread")],
                ["word" => "Cheese", "translation" => getTranslation($language, "Cheese"), "pronunciation" => getPronunciation($language, "Cheese")],
                ["word" => "Bill", "translation" => getTranslation($language, "Bill"), "pronunciation" => getPronunciation($language, "Bill")],
                ["word" => "Delicious", "translation" => getTranslation($language, "Delicious"), "pronunciation" => getPronunciation($language, "Delicious")]
            ],
            "phrases" => [
                "ordering" => getTranslation($language, "I would like to order the pasta, please"),
                "asking_menu" => getTranslation($language, "Could I see the menu, please?"),
                "recommendation" => getTranslation($language, "What do you recommend?"),
                "compliment" => getTranslation($language, "This food is delicious!"),
                "asking_bill" => getTranslation($language, "Could we have the bill, please?"),
                "dietary" => getTranslation($language, "I'm vegetarian, what options do you have?")
            ],
            "grammar" => [
                "point" => "Ordering Food Politely",
                "explanation" => getGrammarExplanation($language, "ordering_food"),
                "examples" => [
                    getTranslation($language, "I would like...") . " (polite request)",
                    getTranslation($language, "Could I have...") . " (asking permission)", 
                    getTranslation($language, "I'll have...") . " (direct but polite)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Good evening. Could we have a table for two?"),
                    "translation" => "Good evening. Could we have a table for two?"
                ],
                [
                    "speaker" => "Waiter",
                    "text" => getTranslation($language, "Of course, right this way. Here are your menus."),
                    "translation" => "Of course, right this way. Here are your menus."
                ],
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Thank you. I'll have the grilled chicken and my friend will have the pasta."),
                    "translation" => "Thank you. I'll have the grilled chicken and my friend will have the pasta."
                ],
                [
                    "speaker" => "Waiter",
                    "text" => getTranslation($language, "Excellent choices. Anything to drink?"),
                    "translation" => "Excellent choices. Anything to drink?"
                ]
            ]
        ],

        "Travel" => [
            "vocabulary" => [
                ["word" => "Airport", "translation" => getTranslation($language, "Airport"), "pronunciation" => getPronunciation($language, "Airport")],
                ["word" => "Hotel", "translation" => getTranslation($language, "Hotel"), "pronunciation" => getPronunciation($language, "Hotel")],
                ["word" => "Ticket", "translation" => getTranslation($language, "Ticket"), "pronunciation" => getPronunciation($language, "Ticket")],
                ["word" => "Passport", "translation" => getTranslation($language, "Passport"), "pronunciation" => getPronunciation($language, "Passport")],
                ["word" => "Luggage", "translation" => getTranslation($language, "Luggage"), "pronunciation" => getPronunciation($language, "Luggage")],
                ["word" => "Directions", "translation" => getTranslation($language, "Directions"), "pronunciation" => getPronunciation($language, "Directions")],
                ["word" => "Map", "translation" => getTranslation($language, "Map"), "pronunciation" => getPronunciation($language, "Map")],
                ["word" => "Taxi", "translation" => getTranslation($language, "Taxi"), "pronunciation" => getPronunciation($language, "Taxi")]
            ],
            "phrases" => [
                "asking_directions" => getTranslation($language, "Excuse me, where is the train station?"),
                "booking_hotel" => getTranslation($language, "I have a reservation under the name Smith"),
                "at_airport" => getTranslation($language, "Which gate for the flight to Paris?"),
                "transportation" => getTranslation($language, "How much is a ticket to the city center?"),
                "emergency" => getTranslation($language, "I need help, I lost my passport"),
                "shopping" => getTranslation($language, "How much does this cost?")
            ],
            "grammar" => [
                "point" => "Asking Questions for Directions",
                "explanation" => getGrammarExplanation($language, "asking_directions"),
                "examples" => [
                    getTranslation($language, "Where is...?") . " (location)",
                    getTranslation($language, "How do I get to...?") . " (directions)",
                    getTranslation($language, "Is there... near here?") . " (existence)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Tourist",
                    "text" => getTranslation($language, "Excuse me, could you help me? I'm looking for the Louvre Museum."),
                    "translation" => "Excuse me, could you help me? I'm looking for the Louvre Museum."
                ],
                [
                    "speaker" => "Local",
                    "text" => getTranslation($language, "Of course! It's about 15 minutes from here. Go straight, then turn left at the bank."),
                    "translation" => "Of course! It's about 15 minutes from here. Go straight, then turn left at the bank."
                ],
                [
                    "speaker" => "Tourist",
                    "text" => getTranslation($language, "Thank you so much! Should I walk or take the metro?"),
                    "translation" => "Thank you so much! Should I walk or take the metro?"
                ],
                [
                    "speaker" => "Local",
                    "text" => getTranslation($language, "Walking is nice, it's a beautiful day. You'll see many shops along the way."),
                    "translation" => "Walking is nice, it's a beautiful day. You'll see many shops along the way."
                ]
            ]
        ],

        "Family" => [
            "vocabulary" => [
                ["word" => "Mother", "translation" => getTranslation($language, "Mother"), "pronunciation" => getPronunciation($language, "Mother")],
                ["word" => "Father", "translation" => getTranslation($language, "Father"), "pronunciation" => getPronunciation($language, "Father")],
                ["word" => "Brother", "translation" => getTranslation($language, "Brother"), "pronunciation" => getPronunciation($language, "Brother")],
                ["word" => "Sister", "translation" => getTranslation($language, "Sister"), "pronunciation" => getPronunciation($language, "Sister")],
                ["word" => "Parents", "translation" => getTranslation($language, "Parents"), "pronunciation" => getPronunciation($language, "Parents")],
                ["word" => "Children", "translation" => getTranslation($language, "Children"), "pronunciation" => getPronunciation($language, "Children")],
                ["word" => "Grandmother", "translation" => getTranslation($language, "Grandmother"), "pronunciation" => getPronunciation($language, "Grandmother")],
                ["word" => "Grandfather", "translation" => getTranslation($language, "Grandfather"), "pronunciation" => getPronunciation($language, "Grandfather")]
            ],
            "phrases" => [
                "introducing_family" => getTranslation($language, "This is my mother, her name is Maria"),
                "asking_about_family" => getTranslation($language, "How many brothers and sisters do you have?"),
                "describing_family" => getTranslation($language, "I have two brothers and one sister"),
                "family_activities" => getTranslation($language, "We like to have dinner together every Sunday"),
                "family_relations" => getTranslation($language, "My grandmother lives with us"),
                "family_events" => getTranslation($language, "We're visiting my cousins this weekend")
            ],
            "grammar" => [
                "point" => "Possessive Forms for Family",
                "explanation" => getGrammarExplanation($language, "family_possessive"),
                "examples" => [
                    getTranslation($language, "My mother") . " (possession)",
                    getTranslation($language, "Our parents") . " (plural possession)", 
                    getTranslation($language, "His brother") . " (gender-specific)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Maria",
                    "text" => getTranslation($language, "This is a photo of my family. These are my parents."),
                    "translation" => "This is a photo of my family. These are my parents."
                ],
                [
                    "speaker" => "John",
                    "text" => getTranslation($language, "They look lovely! How many siblings do you have?"),
                    "translation" => "They look lovely! How many siblings do you have?"
                ],
                [
                    "speaker" => "Maria", 
                    "text" => getTranslation($language, "I have one older brother and one younger sister. My brother lives in London."),
                    "translation" => "I have one older brother and one younger sister. My brother lives in London."
                ],
                [
                    "speaker" => "John",
                    "text" => getTranslation($language, "That's nice. Do you visit him often?"),
                    "translation" => "That's nice. Do you visit him often?"
                ]
            ]
        ]
    ];

    return $contentDatabase[$topicName] ?? $contentDatabase["Greetings"]; // Fallback to Greetings
}

// Enhanced translation database with more languages
function getTranslation($language, $english) {
    $translations = [
        "French" => [
            "Hello" => "Bonjour",
            "Goodbye" => "Au revoir",
            "Good morning" => "Bonjour", 
            "Good evening" => "Bonsoir",
            "How are you?" => "Comment ça va?",
            "I'm fine" => "Je vais bien",
            "Thank you" => "Merci",
            "You're welcome" => "De rien",
            "Good morning, how are you today?" => "Bonjour, comment allez-vous aujourd'hui?",
            "Hey, what's up?" => "Salut, quoi de neuf?",
            "My name is [Name], nice to meet you" => "Je m'appelle [Name], enchanté(e)",
            "How have you been?" => "Comment allez-vous?",
            "Good evening, how was your day?" => "Bonsoir, comment s'est passée votre journée?",
            "I'm doing well, thank you for asking" => "Je vais bien, merci de demander",
            "Good morning, Sarah! How are you?" => "Bonjour Sarah ! Comment ça va?",
            "Good morning, Alex! I'm doing well, thank you. How about you?" => "Bonjour Alex ! Je vais bien, merci. Et toi?",
            "I'm great, thanks! Have a good day!" => "Je vais très bien, merci ! Bonne journée!",
            "You too, see you later!" => "Toi aussi, à plus tard!",
            "Restaurant" => "Restaurant",
            "Menu" => "Menu",
            "Water" => "Eau",
            "Coffee" => "Café",
            "Bread" => "Pain",
            "Cheese" => "Fromage",
            "Bill" => "Addition",
            "Delicious" => "Délicieux",
            "I would like to order the pasta, please" => "Je voudrais commander les pâtes, s'il vous plaît",
            "Could I see the menu, please?" => "Pourrais-je voir le menu, s'il vous plaît?",
            "What do you recommend?" => "Que recommandez-vous?",
            "This food is delicious!" => "Cette nourriture est délicieuse!",
            "Could we have the bill, please?" => "Pourrions-nous avoir l'addition, s'il vous plaît?",
            "I'm vegetarian, what options do you have?" => "Je suis végétarien(ne), quelles options avez-vous?",
            "Good evening. Could we have a table for two?" => "Bonsoir. Pourrions-nous avoir une table pour deux?",
            "Of course, right this way. Here are your menus." => "Bien sûr, par ici. Voici vos menus.",
            "Thank you. I'll have the grilled chicken and my friend will have the pasta." => "Merci. Je prendrai le poulet grillé et mon ami prendra les pâtes.",
            "Excellent choices. Anything to drink?" => "D'excellents choix. Quelque chose à boire?",
            "Airport" => "Aéroport",
            "Hotel" => "Hôtel", 
            "Ticket" => "Billet",
            "Passport" => "Passeport",
            "Luggage" => "Bagages",
            "Directions" => "Itinéraire",
            "Map" => "Carte",
            "Taxi" => "Taxi",
            "Excuse me, where is the train station?" => "Excusez-moi, où est la gare?",
            "I have a reservation under the name Smith" => "J'ai une réservation au nom de Smith",
            "Which gate for the flight to Paris?" => "Quelle porte pour le vol vers Paris?",
            "How much is a ticket to the city center?" => "Combien coûte un billet pour le centre-ville?",
            "I need help, I lost my passport" => "J'ai besoin d'aide, j'ai perdu mon passeport",
            "How much does this cost?" => "Combien ça coûte?",
            "Excuse me, could you help me? I'm looking for the Louvre Museum." => "Excusez-moi, pourriez-vous m'aider? Je cherche le musée du Louvre.",
            "Of course! It's about 15 minutes from here. Go straight, then turn left at the bank." => "Bien sûr! C'est à environ 15 minutes d'ici. Allez tout droit, puis tournez à gauche à la banque.",
            "Thank you so much! Should I walk or take the metro?" => "Merci beaucoup! Dois-je marcher ou prendre le métro?",
            "Walking is nice, it's a beautiful day. You'll see many shops along the way." => "Marcher est agréable, c'est une belle journée. Vous verrez beaucoup de magasins en chemin.",
            "Mother" => "Mère",
            "Father" => "Père",
            "Brother" => "Frère", 
            "Sister" => "Sœur",
            "Parents" => "Parents",
            "Children" => "Enfants",
            "Grandmother" => "Grand-mère",
            "Grandfather" => "Grand-père",
            "This is my mother, her name is Maria" => "Voici ma mère, elle s'appelle Maria",
            "How many brothers and sisters do you have?" => "Combien de frères et sœurs as-tu?",
            "I have two brothers and one sister" => "J'ai deux frères et une sœur",
            "We like to have dinner together every Sunday" => "Nous aimons dîner ensemble tous les dimanches",
            "My grandmother lives with us" => "Ma grand-mère vit avec nous",
            "We're visiting my cousins this weekend" => "Nous visitons mes cousins ce week-end",
            "This is a photo of my family. These are my parents." => "Voici une photo de ma famille. Ce sont mes parents.",
            "They look lovely! How many siblings do you have?" => "Ils ont l'air charmants! Combien de frères et sœurs as-tu?",
            "I have one older brother and one younger sister. My brother lives in London." => "J'ai un grand frère et une petite sœur. Mon frère vit à Londres.",
            "That's nice. Do you visit him often?" => "C'est sympa. Tu lui rends souvent visite?"
        ],
        
        "Spanish" => [
            "Hello" => "Hola",
            "Goodbye" => "Adiós",
            "Good morning" => "Buenos días",
            "Good evening" => "Buenas tardes", 
            "How are you?" => "¿Cómo estás?",
            "I'm fine" => "Estoy bien",
            "Thank you" => "Gracias",
            "You're welcome" => "De nada",
            "Good morning, how are you today?" => "Buenos días, ¿cómo estás hoy?",
            "Hey, what's up?" => "¿Oye, qué tal?",
            "My name is [Name], nice to meet you" => "Me llamo [Name], mucho gusto",
            "How have you been?" => "¿Cómo has estado?",
            "Good evening, how was your day?" => "Buenas tardes, ¿cómo te fue hoy?",
            "I'm doing well, thank you for asking" => "Estoy bien, gracias por preguntar",
            // Add more Spanish translations following the same pattern...
        ],
        
        "German" => [
            "Hello" => "Hallo",
            "Goodbye" => "Auf Wiedersehen", 
            "Good morning" => "Guten Morgen",
            "Good evening" => "Guten Abend",
            "How are you?" => "Wie geht es dir?",
            "I'm fine" => "Mir geht es gut",
            "Thank you" => "Danke",
            "You're welcome" => "Bitte",
            // Add more German translations...
        ]
    ];
    
    return $translations[$language][$english] ?? "[Translation not available]";
}

function getPronunciation($language, $word) {
    $pronunciations = [
        "French" => [
            "Bonjour" => "bon-zhoor",
            "Au revoir" => "oh ruh-vwahr", 
            "Bonsoir" => "bon-swahr",
            "Comment ça va?" => "kom-on sa va",
            "Je vais bien" => "zhuh vay byan",
            "Merci" => "mair-see",
            "De rien" => "duh ryan",
            // Add more French pronunciations...
        ],
        "Spanish" => [
            "Hola" => "oh-lah",
            "Adiós" => "ah-dee-ohs",
            "Buenos días" => "bway-nos dee-ahs",
            "Gracias" => "grah-see-ahs",
            // Add more Spanish pronunciations...
        ]
    ];
    
    $translation = getTranslation($language, $word);
    return $pronunciations[$language][$translation] ?? $translation;
}

function getGrammarExplanation($language, $topic) {
    $explanations = [
        "greetings_formality" => [
            "French" => "In French, use 'Bonjour' for formal situations and 'Salut' for informal ones. 'Vous' is formal 'you', 'tu' is informal.",
            "Spanish" => "In Spanish, use 'Buenos días' formally and 'Hola' informally. 'Usted' is formal 'you', 'tú' is informal.",
            "German" => "In German, use 'Guten Tag' formally and 'Hallo' informally. 'Sie' is formal 'you', 'du' is informal.",
            "default" => "Use formal greetings with strangers, elders, and in professional settings. Use informal greetings with friends and peers."
        ],
        "ordering_food" => [
            "French" => "Use 'Je voudrais' (I would like) for polite requests. Add 's'il vous plaît' (please) for extra politeness.",
            "Spanish" => "Use 'Me gustaría' (I would like) or 'Quisiera' (I would want) for polite ordering.",
            "German" => "Use 'Ich hätte gern' (I would like to have) for polite food orders.",
            "default" => "Use polite phrases like 'I would like' or 'Could I have' when ordering food."
        ],
        "asking_directions" => [
            "French" => "Start with 'Excusez-moi' (Excuse me) and use 'Où est' (Where is) for locations.",
            "Spanish" => "Use 'Disculpe' (Excuse me) and '¿Dónde está?' (Where is) for asking directions.",
            "German" => "Use 'Entschuldigung' (Excuse me) and 'Wo ist' (Where is) for finding places.",
            "default" => "Start with 'Excuse me' and use question words like 'Where', 'How', 'Which' for directions."
        ],
        "family_possessive" => [
            "French" => "Use 'mon/ma/mes' for 'my' depending on gender and number: mon père (my father), ma mère (my mother), mes parents (my parents).",
            "Spanish" => "Use 'mi' for singular and 'mis' for plural: mi madre (my mother), mis hermanos (my brothers).",
            "German" => "Use 'mein/meine/mein' depending on gender: mein Vater (my father), meine Mutter (my mother).",
            "default" => "Use possessive adjectives that match the gender and number of the family member."
        ]
    ];
    
    return $explanations[$topic][$language] ?? $explanations[$topic]["default"] ?? "Grammar explanation";
}

// Progress tracking functions
function updateProgress($language, $topic, $lessonType, $score = 0) {
    if (!isset($_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'] = [];
    }
    
    if (!in_array($lessonType, $_SESSION['user_progress'][$language][$topic]['completed_lessons'])) {
        $_SESSION['user_progress'][$language][$topic]['completed_lessons'][] = $lessonType;
        $_SESSION['user_progress'][$language][$topic]['score'] += $score;
        $_SESSION['user_progress'][$language][$topic]['words_learned'] = count(getRealLessonContent($language, $topic)['vocabulary']);
        $_SESSION['user_progress'][$language][$topic]['last_accessed'] = date('Y-m-d H:i:s');
    }
}

function getProgressStats($language, $topic) {
    if (!isset($_SESSION['user_progress'][$language][$topic])) {
        return [
            'completed' => 0,
            'total' => 5,
            'percentage' => 0,
            'score' => 0,
            'words_learned' => 0
        ];
    }
    
    $progress = $_SESSION['user_progress'][$language][$topic];
    $completed = count($progress['completed_lessons'] ?? []);
    $percentage = ($completed / 5) * 100;
    
    return [
        'completed' => $completed,
        'total' => 5,
        'percentage' => $percentage,
        'score' => $progress['score'] ?? 0,
        'words_learned' => $progress['words_learned'] ?? 0
    ];
}

// Handle lesson completion
if (isset($_GET['complete']) && $_GET['complete'] === 'true') {
    $lessonType = $_GET['type'] ?? '';
    $score = $_GET['score'] ?? 10;
    updateProgress($lang, $topic, $lessonType, $score);
}

// Get lesson structure
function getLessonStructure($language, $topicName) {
    $baseStructure = [
        "vocabulary" => [
            "title" => "Essential Vocabulary",
            "icon" => "fas fa-book",
            "description" => "Learn key words and phrases for " . $topicName
        ],
        "phrases" => [
            "title" => "Common Phrases", 
            "icon" => "fas fa-comment",
            "description" => "Useful expressions and sentences for " . $topicName
        ],
        "grammar" => [
            "title" => "Grammar Basics",
            "icon" => "fas fa-language",
            "description" => "Important grammar rules for " . $topicName
        ],
        "conversation" => [
            "title" => "Conversation Practice", 
            "icon" => "fas fa-users",
            "description" => "Real-life dialogue examples for " . $topicName
        ],
        "practice" => [
            "title" => "Practice Exercise",
            "icon" => "fas fa-pencil-alt", 
            "description" => "Test your knowledge of " . $topicName
        ]
    ];
    
    $content = getRealLessonContent($language, $topicName);
    $lessons = [];
    $lessonTypes = array_keys($baseStructure);
    
    foreach ($lessonTypes as $index => $type) {
        $lessons[] = [
            "id" => $index + 1,
            "type" => $type,
            "title" => $baseStructure[$type]["title"],
            "icon" => $baseStructure[$type]["icon"],
            "description" => $baseStructure[$type]["description"],
            "content" => $content[$type] ?? []
        ];
    }
    
    return $lessons;
}

$lessons = getLessonStructure($lang, $topic);
$totalLessons = count($lessons);
$currentLessonIndex = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;
$currentLesson = $lessons[$currentLessonIndex] ?? $lessons[0];

// Get progress stats
$progressStats = getProgressStats($lang, $topic);

if (!$currentLesson) {
    header("Location: ../Topics/Topics.php?lang=" . urlencode($lang));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learn <?php echo htmlspecialchars($topic); ?> - <?php echo htmlspecialchars($lang); ?> | LinguaLearn</title>
    <link rel="stylesheet" href="lesson.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../Languages/language.php" class="logo">
                <i class="fas fa-globe-americas"></i>
                LinguaLearn
            </a>
            <ul class="nav-menu">
                <li><a href="../dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="../Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-link"><i class="fas fa-arrow-left"></i> Back to Topics</a></li>
            </ul>
        </div>
    </nav>

    <div class="lesson-container">
        <div class="lesson-header">
            <h1>Learn <?php echo htmlspecialchars($topic); ?></h1>
            <p class="language-badge"><?php echo htmlspecialchars($lang); ?></p>
        </div>

        <!-- Progress Bar -->
        <div class="lesson-progress">
            <span>Lesson <?php echo $currentLessonIndex + 1; ?> of <?php echo $totalLessons; ?></span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo (($currentLessonIndex + 1) / $totalLessons) * 100; ?>%"></div>
            </div>
            <span><?php echo $currentLesson['title']; ?></span>
        </div>

        <!-- Lesson Content -->
        <div class="lesson-content">
            <div class="lesson-card <?php echo $currentLesson['type']; ?>">
                <h2><i class="<?php echo $currentLesson['icon']; ?>"></i> <?php echo $currentLesson['title']; ?></h2>
                <p class="lesson-description"><?php echo $currentLesson['description']; ?></p>
                
                <?php if ($currentLesson['type'] === 'vocabulary' && !empty($currentLesson['content'])): ?>
                    <div class="vocabulary-grid">
                        <?php foreach ($currentLesson['content'] as $item): ?>
                            <div class="vocabulary-item">
                                <div class="word"><?php echo $item['word']; ?></div>
                                <div class="translation"><?php echo $item['translation']; ?></div>
                                <div class="pronunciation"><?php echo $item['pronunciation']; ?></div>
                                <button class="audio-btn" onclick="playAudio('<?php echo $item['translation']; ?>')">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'phrases' && !empty($currentLesson['content'])): ?>
                    <div class="phrases-list">
                        <?php foreach ($currentLesson['content'] as $key => $phrase): ?>
                            <div class="phrase-item">
                                <div class="phrase-key"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</div>
                                <div class="phrase-text"><?php echo $phrase; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'grammar' && !empty($currentLesson['content'])): ?>
                    <div class="grammar-content">
                        <div class="grammar-point">
                            <h3><?php echo $currentLesson['content']['point']; ?></h3>
                            <p><?php echo $currentLesson['content']['explanation']; ?></p>
                        </div>
                        <?php if (!empty($currentLesson['content']['examples'])): ?>
                            <div class="examples">
                                <h4>Examples:</h4>
                                <?php foreach ($currentLesson['content']['examples'] as $example): ?>
                                    <div class="example"><?php echo $example; ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'conversation' && !empty($currentLesson['content'])): ?>
                    <div class="conversation-dialogue">
                        <?php foreach ($currentLesson['content'] as $line): ?>
                            <div class="dialogue-line">
                                <span class="speaker"><?php echo $line['speaker']; ?>:</span>
                                <span class="text"><?php echo $line['text']; ?></span>
                                <span class="translation">(<?php echo $line['translation']; ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'practice'): ?>
                    <div class="practice-intro">
                        <p>Test your knowledge with interactive exercises!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Practice Section -->
            <div class="practice-section" id="practiceSection">
                <h3><i class="fas fa-pencil-alt"></i> Practice Exercise</h3>
                <div id="exerciseContent">
                    <!-- Exercise content loaded by JavaScript -->
                </div>
            </div>

            <!-- Navigation -->
            <div class="lesson-nav">
                <?php if ($currentLessonIndex > 0): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex - 1; ?>" class="nav-btn">
                        <i class="fas fa-arrow-left"></i> Previous
                    </a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($currentLessonIndex < $totalLessons - 1): ?>
                    <a href="?lang=<?php echo urlencode($lang); ?>&topic=<?php echo urlencode($topic); ?>&lesson=<?php echo $currentLessonIndex + 1; ?>" class="nav-btn primary" id="nextBtn">
                        Next Lesson <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="../Topics/Topics.php?lang=<?php echo urlencode($lang); ?>" class="nav-btn success">
                        Complete Topic <i class="fas fa-check"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="lesson.js"></script>
    <script>
        const lessonData = <?php echo json_encode($currentLesson); ?>;
        const language = "<?php echo $lang; ?>";
        const topic = "<?php echo $topic; ?>";
        const currentLessonIndex = <?php echo $currentLessonIndex; ?>;
    </script>
</body>
</html>