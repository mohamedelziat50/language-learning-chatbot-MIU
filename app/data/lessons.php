<?php
return [
    "French" => [
        "Greetings" => [
            "vocabulary" => [
                ["word" => "Hello", "translation" => "Bonjour", "pronunciation" => "bon-zhoor"],
                ["word" => "Goodbye", "translation" => "Au revoir", "pronunciation" => "oh ruh-vwahr"],
                ["word" => "Good morning", "translation" => "Bonjour", "pronunciation" => "bon-zhoor"],
                ["word" => "Good evening", "translation" => "Bonsoir", "pronunciation" => "bon-swahr"],
                ["word" => "How are you?", "translation" => "Comment ça va?", "pronunciation" => "kom-on sa va"],
                ["word" => "I'm fine", "translation" => "Je vais bien", "pronunciation" => "zhuh vay byan"],
                ["word" => "Thank you", "translation" => "Merci", "pronunciation" => "mair-see"],
                ["word" => "You're welcome", "translation" => "De rien", "pronunciation" => "duh ryan"]
            ],
            "phrases" => [
                "formal_greeting" => "Bonjour, comment allez-vous aujourd'hui?",
                "informal_greeting" => "Salut, quoi de neuf?",
                "introducing_yourself" => "Je m'appelle [Name], enchanté(e)",
                "asking_about_someone" => "Comment allez-vous?",
                "evening_greeting" => "Bonsoir, comment s'est passée votre journée?",
                "polite_response" => "Je vais bien, merci de demander"
            ],
            "grammar" => [
                "point" => "Formal vs Informal Greetings",
                "explanation" => "In French, use 'Bonjour' for formal situations and 'Salut' for informal ones. 'Vous' is formal 'you', 'tu' is informal.",
                "examples" => [
                    "Bonjour → Salut (formal → informal)",
                    "Au revoir → Bye (formal → informal)",
                    "Comment allez-vous? → Quoi de neuf? (formal → informal)"
                ]
            ],
            "conversation" => [
                ["speaker" => "Alex", "text" => "Bonjour Sarah ! Comment ça va?", "translation" => "Good morning Sarah! How are you?"],
                ["speaker" => "Sarah", "text" => "Bonjour Alex ! Je vais bien, merci. Et toi?", "translation" => "Good morning Alex! I'm doing well, thank you. How about you?"],
                ["speaker" => "Alex", "text" => "Je vais très bien, merci ! Bonne journée!", "translation" => "I'm great, thanks! Have a good day!"],
                ["speaker" => "Sarah", "text" => "Toi aussi, à plus tard!", "translation" => "You too, see you later!"]
            ]
        ],
        "Food & Dining" => [
            "vocabulary" => [
                ["word" => "Restaurant", "translation" => "Restaurant", "pronunciation" => "res-toh-rahn"],
                ["word" => "Menu", "translation" => "Menu", "pronunciation" => "muh-noo"],
                ["word" => "Waiter", "translation" => "Serveur", "pronunciation" => "ser-vuhr"],
                ["word" => "Table", "translation" => "Table", "pronunciation" => "tabl"],
                ["word" => "Order", "translation" => "Commander", "pronunciation" => "ko-mon-day"],
                ["word" => "Bill", "translation" => "Addition", "pronunciation" => "a-dee-syon"],
                ["word" => "Tip", "translation" => "Pourboire", "pronunciation" => "poor-bwar"],
                ["word" => "Delicious", "translation" => "Délicieux", "pronunciation" => "day-lee-syuh"]
            ],
            "phrases" => [
                "reserving_table" => "Je voudrais réserver une table pour deux, s'il vous plaît",
                "asking_menu" => "Pourrions-nous voir le menu, s'il vous plaît?",
                "ordering_food" => "Je prendrai le saumon grillé avec des légumes",
                "asking_recommendation" => "Que recommandez-vous aujourd'hui?",
                "dietary_restriction" => "Je suis allergique aux noix",
                "asking_bill" => "Pourrions-nous avoir l'addition, s'il vous plaît?"
            ],
            "grammar" => [
                "point" => "Ordering Food with Modal Verbs",
                "explanation" => "Use 'Je voudrais' (I would like) for polite requests. Add 's'il vous plaît' (please) for extra politeness.",
                "examples" => [
                    "Je voudrais... (polite request)",
                    "Pourrais-je avoir... (asking permission)",
                    "Je prendrai... (decision)"
                ]
            ],
            "conversation" => [
                ["speaker" => "Client", "text" => "Bonsoir. Nous avons une réservation au nom de Johnson.", "translation" => "Good evening. We have a reservation under Johnson."],
                ["speaker" => "Hôte", "text" => "Bienvenue! Votre table est prête. Par ici, s'il vous plaît.", "translation" => "Welcome! Your table is ready. Right this way, please."],
                ["speaker" => "Serveur", "text" => "Voici vos menus. Puis-je vous apporter quelque chose à boire?", "translation" => "Here are your menus. Can I start you with something to drink?"],
                ["speaker" => "Client", "text" => "Oui, nous prendrons de l'eau pour l'instant. Nous avons besoin de quelques minutes pour décider.", "translation" => "Yes, we'll both have water for now. We need a few minutes to decide on food."]
            ]
        ]
    ],
    "Spanish" => [
        "Basics" => [
            "vocabulary" => [
                ["word" => "Yes", "translation" => "Sí", "pronunciation" => "see"],
                ["word" => "No", "translation" => "No", "pronunciation" => "noh"],
                ["word" => "Please", "translation" => "Por favor", "pronunciation" => "por fah-vor"],
                ["word" => "Sorry", "translation" => "Lo siento", "pronunciation" => "loh syen-toh"],
                ["word" => "Help", "translation" => "Ayuda", "pronunciation" => "ah-yoo-dah"],
                ["word" => "Water", "translation" => "Agua", "pronunciation" => "ah-gwah"],
                ["word" => "Food", "translation" => "Comida", "pronunciation" => "koh-mee-dah"],
                ["word" => "Bathroom", "translation" => "Baño", "pronunciation" => "bah-nyoh"]
            ],
            "phrases" => [
                "basic_questions" => "¿Dónde está el baño?",
                "polite_requests" => "¿Podría ayudarme, por favor?",
                "emergency_phrases" => "Necesito ayuda",
                "simple_responses" => "Sí, entiendo",
                "asking_repetition" => "¿Podría repetir eso, por favor?",
                "basic_needs" => "Tengo hambre y sed"
            ],
            "grammar" => [
                "point" => "Basic Sentence Structure",
                "explanation" => "Spanish sentences typically follow Subject-Verb-Object order. Questions are formed by inversion or adding question marks.",
                "examples" => [
                    "Yo soy... (I am...)",
                    "¿Dónde está...? (Where is...?)",
                    "Necesito... (I need...)"
                ]
            ],
            "conversation" => [
                ["speaker" => "Turista", "text" => "Disculpe, ¿dónde está la estación de tren?", "translation" => "Excuse me, where is the train station?"],
                ["speaker" => "Local", "text" => "Está recto, como a cinco minutos caminando.", "translation" => "It's straight ahead, about five minutes walk."],
                ["speaker" => "Turista", "text" => "¡Muchas gracias!", "translation" => "Thank you very much!"],
                ["speaker" => "Local", "text" => "De nada. ¡Que tenga un buen día!", "translation" => "You're welcome. Have a good day!"]
            ]
        ]
    ]
];
?>
