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

// REAL CONTENT for each topic
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

        "Food & Dining" => [
            "vocabulary" => [
                ["word" => "Restaurant", "translation" => getTranslation($language, "Restaurant"), "pronunciation" => getPronunciation($language, "Restaurant")],
                ["word" => "Menu", "translation" => getTranslation($language, "Menu"), "pronunciation" => getPronunciation($language, "Menu")],
                ["word" => "Waiter/Waitress", "translation" => getTranslation($language, "Waiter"), "pronunciation" => getPronunciation($language, "Waiter")],
                ["word" => "Table", "translation" => getTranslation($language, "Table"), "pronunciation" => getPronunciation($language, "Table")],
                ["word" => "Order", "translation" => getTranslation($language, "Order"), "pronunciation" => getPronunciation($language, "Order")],
                ["word" => "Bill/Check", "translation" => getTranslation($language, "Bill"), "pronunciation" => getPronunciation($language, "Bill")],
                ["word" => "Tip", "translation" => getTranslation($language, "Tip"), "pronunciation" => getPronunciation($language, "Tip")],
                ["word" => "Delicious", "translation" => getTranslation($language, "Delicious"), "pronunciation" => getPronunciation($language, "Delicious")]
            ],
            "phrases" => [
                "reserving_table" => getTranslation($language, "I'd like to reserve a table for two, please"),
                "asking_menu" => getTranslation($language, "Could we see the menu, please?"),
                "ordering_food" => getTranslation($language, "I'll have the grilled salmon with vegetables"),
                "asking_recommendation" => getTranslation($language, "What do you recommend today?"),
                "dietary_restriction" => getTranslation($language, "I'm allergic to nuts"),
                "asking_bill" => getTranslation($language, "Could we get the bill, please?")
            ],
            "grammar" => [
                "point" => "Ordering Food with Modal Verbs",
                "explanation" => getGrammarExplanation($language, "ordering_food"),
                "examples" => [
                    getTranslation($language, "I would like...") . " (polite request)",
                    getTranslation($language, "Could I have...") . " (asking permission)", 
                    getTranslation($language, "I'll have...") . " (decision)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Good evening. We have a reservation under Johnson."),
                    "translation" => "Good evening. We have a reservation under Johnson."
                ],
                [
                    "speaker" => "Host",
                    "text" => getTranslation($language, "Welcome! Your table is ready. Right this way, please."),
                    "translation" => "Welcome! Your table is ready. Right this way, please."
                ],
                [
                    "speaker" => "Waiter",
                    "text" => getTranslation($language, "Here are your menus. Can I start you with something to drink?"),
                    "translation" => "Here are your menus. Can I start you with something to drink?"
                ],
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Yes, we'll both have water for now. We need a few minutes to decide on food."),
                    "translation" => "Yes, we'll both have water for now. We need a few minutes to decide on food."
                ]
            ]
        ],

        "Travel & Transportation" => [
            "vocabulary" => [
                ["word" => "Airport", "translation" => getTranslation($language, "Airport"), "pronunciation" => getPronunciation($language, "Airport")],
                ["word" => "Train station", "translation" => getTranslation($language, "Train station"), "pronunciation" => getPronunciation($language, "Train station")],
                ["word" => "Ticket", "translation" => getTranslation($language, "Ticket"), "pronunciation" => getPronunciation($language, "Ticket")],
                ["word" => "Passport", "translation" => getTranslation($language, "Passport"), "pronunciation" => getPronunciation($language, "Passport")],
                ["word" => "Luggage", "translation" => getTranslation($language, "Luggage"), "pronunciation" => getPronunciation($language, "Luggage")],
                ["word" => "Boarding pass", "translation" => getTranslation($language, "Boarding pass"), "pronunciation" => getPronunciation($language, "Boarding pass")],
                ["word" => "Departure", "translation" => getTranslation($language, "Departure"), "pronunciation" => getPronunciation($language, "Departure")],
                ["word" => "Arrival", "translation" => getTranslation($language, "Arrival"), "pronunciation" => getPronunciation($language, "Arrival")]
            ],
            "phrases" => [
                "asking_directions" => getTranslation($language, "Excuse me, where is the nearest metro station?"),
                "buying_ticket" => getTranslation($language, "I'd like a one-way ticket to the city center, please"),
                "checking_in" => getTranslation($language, "Here is my passport and boarding pass"),
                "flight_information" => getTranslation($language, "What time does the flight to London depart?"),
                "hotel_checkin" => getTranslation($language, "I have a reservation for two nights under the name Smith"),
                "asking_help" => getTranslation($language, "Could you help me with my luggage, please?")
            ],
            "grammar" => [
                "point" => "Asking for Directions and Information",
                "explanation" => getGrammarExplanation($language, "asking_directions"),
                "examples" => [
                    getTranslation($language, "Where is...?") . " (location questions)",
                    getTranslation($language, "How do I get to...?") . " (direction questions)",
                    getTranslation($language, "What time does...?") . " (time questions)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Traveler",
                    "text" => getTranslation($language, "Excuse me, could you tell me where I can find the check-in counter for flight 245?"),
                    "translation" => "Excuse me, could you tell me where I can find the check-in counter for flight 245?"
                ],
                [
                    "speaker" => "Airport Staff",
                    "text" => getTranslation($language, "Certainly. Go straight ahead and turn left at the information desk. It will be on your right."),
                    "translation" => "Certainly. Go straight ahead and turn left at the information desk. It will be on your right."
                ],
                [
                    "speaker" => "Traveler",
                    "text" => getTranslation($language, "Thank you. And where is the baggage drop-off area?"),
                    "translation" => "Thank you. And where is the baggage drop-off area?"
                ],
                [
                    "speaker" => "Airport Staff",
                    "text" => getTranslation($language, "It's right next to the check-in counters. You can't miss it."),
                    "translation" => "It's right next to the check-in counters. You can't miss it."
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
                ["word" => "Grandparents", "translation" => getTranslation($language, "Grandparents"), "pronunciation" => getPronunciation($language, "Grandparents")],
                ["word" => "Siblings", "translation" => getTranslation($language, "Siblings"), "pronunciation" => getPronunciation($language, "Siblings")]
            ],
            "phrases" => [
                "introducing_family" => getTranslation($language, "This is my wife, Maria, and these are our children"),
                "asking_about_family" => getTranslation($language, "Do you have any brothers or sisters?"),
                "describing_family" => getTranslation($language, "I come from a large family with three brothers"),
                "family_activities" => getTranslation($language, "We usually have family dinner together on Sundays"),
                "talking_relationships" => getTranslation($language, "How long have you been married?"),
                "family_events" => getTranslation($language, "We're celebrating my parents' anniversary this weekend")
            ],
            "grammar" => [
                "point" => "Possessive Forms and Family Relationships",
                "explanation" => getGrammarExplanation($language, "family_possessive"),
                "examples" => [
                    getTranslation($language, "My mother's house") . " (possessive 's)",
                    getTranslation($language, "Our parents' car") . " (plural possessive)",
                    getTranslation($language, "His brother's wedding") . " (specific possession)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Anna",
                    "text" => getTranslation($language, "So, tell me about your family. Do you have any siblings?"),
                    "translation" => "So, tell me about your family. Do you have any siblings?"
                ],
                [
                    "speaker" => "Mark",
                    "text" => getTranslation($language, "Yes, I have an older sister and a younger brother. My sister lives in Paris now."),
                    "translation" => "Yes, I have an older sister and a younger brother. My sister lives in Paris now."
                ],
                [
                    "speaker" => "Anna", 
                    "text" => getTranslation($language, "That's nice! Do you visit her often?"),
                    "translation" => "That's nice! Do you visit her often?"
                ],
                [
                    "speaker" => "Mark",
                    "text" => getTranslation($language, "Usually twice a year. We're actually planning a family reunion next month."),
                    "translation" => "Usually twice a year. We're actually planning a family reunion next month."
                ]
            ]
        ],

        "Shopping" => [
            "vocabulary" => [
                ["word" => "Store/Shop", "translation" => getTranslation($language, "Store"), "pronunciation" => getPronunciation($language, "Store")],
                ["word" => "Price", "translation" => getTranslation($language, "Price"), "pronunciation" => getPronunciation($language, "Price")],
                ["word" => "Discount", "translation" => getTranslation($language, "Discount"), "pronunciation" => getPronunciation($language, "Discount")],
                ["word" => "Size", "translation" => getTranslation($language, "Size"), "pronunciation" => getPronunciation($language, "Size")],
                ["word" => "Fitting room", "translation" => getTranslation($language, "Fitting room"), "pronunciation" => getPronunciation($language, "Fitting room")],
                ["word" => "Cashier", "translation" => getTranslation($language, "Cashier"), "pronunciation" => getPronunciation($language, "Cashier")],
                ["word" => "Receipt", "translation" => getTranslation($language, "Receipt"), "pronunciation" => getPronunciation($language, "Receipt")],
                ["word" => "Exchange", "translation" => getTranslation($language, "Exchange"), "pronunciation" => getPronunciation($language, "Exchange")]
            ],
            "phrases" => [
                "asking_price" => getTranslation($language, "How much does this cost?"),
                "asking_size" => getTranslation($language, "Do you have this in a larger size?"),
                "trying_clothes" => getTranslation($language, "Where are the fitting rooms?"),
                "asking_discount" => getTranslation($language, "Is this item on sale?"),
                "making_payment" => getTranslation($language, "I'd like to pay by credit card, please"),
                "returning_item" => getTranslation($language, "I'd like to exchange this, it doesn't fit")
            ],
            "grammar" => [
                "point" => "Making Requests and Asking Questions",
                "explanation" => getGrammarExplanation($language, "shopping_requests"),
                "examples" => [
                    getTranslation($language, "Do you have...?") . " (availability)",
                    getTranslation($language, "Could I try...?") . " (permission)",
                    getTranslation($language, "I'm looking for...") . " (searching)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Excuse me, I'm looking for this sweater in a medium size."),
                    "translation" => "Excuse me, I'm looking for this sweater in a medium size."
                ],
                [
                    "speaker" => "Shop Assistant",
                    "text" => getTranslation($language, "Let me check our stock. Yes, we have medium. Would you like to try it on?"),
                    "translation" => "Let me check our stock. Yes, we have medium. Would you like to try it on?"
                ],
                [
                    "speaker" => "Customer",
                    "text" => getTranslation($language, "Yes, please. Where are the fitting rooms?"),
                    "translation" => "Yes, please. Where are the fitting rooms?"
                ],
                [
                    "speaker" => "Shop Assistant",
                    "text" => getTranslation($language, "Just around the corner to your left. Let me know if you need a different size."),
                    "translation" => "Just around the corner to your left. Let me know if you need a different size."
                ]
            ]
        ],

        "Work & Business" => [
            "vocabulary" => [
                ["word" => "Meeting", "translation" => getTranslation($language, "Meeting"), "pronunciation" => getPronunciation($language, "Meeting")],
                ["word" => "Office", "translation" => getTranslation($language, "Office"), "pronunciation" => getPronunciation($language, "Office")],
                ["word" => "Colleague", "translation" => getTranslation($language, "Colleague"), "pronunciation" => getPronunciation($language, "Colleague")],
                ["word" => "Schedule", "translation" => getTranslation($language, "Schedule"), "pronunciation" => getPronunciation($language, "Schedule")],
                ["word" => "Deadline", "translation" => getTranslation($language, "Deadline"), "pronunciation" => getPronunciation($language, "Deadline")],
                ["word" => "Presentation", "translation" => getTranslation($language, "Presentation"), "pronunciation" => getPronunciation($language, "Presentation")],
                ["word" => "Client", "translation" => getTranslation($language, "Client"), "pronunciation" => getPronunciation($language, "Client")],
                ["word" => "Project", "translation" => getTranslation($language, "Project"), "pronunciation" => getPronunciation($language, "Project")]
            ],
            "phrases" => [
                "scheduling_meeting" => getTranslation($language, "Could we schedule a meeting for next Tuesday?"),
                "asking_availability" => getTranslation($language, "What time works best for you?"),
                "business_introduction" => getTranslation($language, "I'm the project manager for this account"),
                "discussing_deadlines" => getTranslation($language, "When is the deadline for this project?"),
                "requesting_information" => getTranslation($language, "Could you send me the report by email?"),
                "closing_meeting" => getTranslation($language, "Thank you for your time. Let's follow up next week")
            ],
            "grammar" => [
                "point" => "Professional Communication and Formal Requests",
                "explanation" => getGrammarExplanation($language, "business_communication"),
                "examples" => [
                    getTranslation($language, "Could we...?") . " (polite suggestions)",
                    getTranslation($language, "I would appreciate if...") . " (formal requests)",
                    getTranslation($language, "Let's discuss...") . " (collaborative language)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Manager",
                    "text" => getTranslation($language, "Good morning team. Let's start with the project updates."),
                    "translation" => "Good morning team. Let's start with the project updates."
                ],
                [
                    "speaker" => "Employee",
                    "text" => getTranslation($language, "We're on track for the Friday deadline. The design phase is complete."),
                    "translation" => "We're on track for the Friday deadline. The design phase is complete."
                ],
                [
                    "speaker" => "Manager", 
                    "text" => getTranslation($language, "Excellent. Sarah, could you prepare a presentation for the client meeting next week?"),
                    "translation" => "Excellent. Sarah, could you prepare a presentation for the client meeting next week?"
                ],
                [
                    "speaker" => "Sarah",
                    "text" => getTranslation($language, "Certainly. I'll have it ready by Wednesday for your review."),
                    "translation" => "Certainly. I'll have it ready by Wednesday for your review."
                ]
            ]
        ],

        "Health & Emergency" => [
            "vocabulary" => [
                ["word" => "Hospital", "translation" => getTranslation($language, "Hospital"), "pronunciation" => getPronunciation($language, "Hospital")],
                ["word" => "Doctor", "translation" => getTranslation($language, "Doctor"), "pronunciation" => getPronunciation($language, "Doctor")],
                ["word" => "Medicine", "translation" => getTranslation($language, "Medicine"), "pronunciation" => getPronunciation($language, "Medicine")],
                ["word" => "Pain", "translation" => getTranslation($language, "Pain"), "pronunciation" => getPronunciation($language, "Pain")],
                ["word" => "Emergency", "translation" => getTranslation($language, "Emergency"), "pronunciation" => getPronunciation($language, "Emergency")],
                ["word" => "Appointment", "translation" => getTranslation($language, "Appointment"), "pronunciation" => getPronunciation($language, "Appointment")],
                ["word" => "Pharmacy", "translation" => getTranslation($language, "Pharmacy"), "pronunciation" => getPronunciation($language, "Pharmacy")],
                ["word" => "Insurance", "translation" => getTranslation($language, "Insurance"), "pronunciation" => getPronunciation($language, "Insurance")]
            ],
            "phrases" => [
                "describing_symptoms" => getTranslation($language, "I have a fever and headache"),
                "making_appointment" => getTranslation($language, "I need to make an appointment with the doctor"),
                "emergency_help" => getTranslation($language, "Help! I need a doctor!"),
                "asking_pharmacy" => getTranslation($language, "Where is the nearest pharmacy?"),
                "medical_history" => getTranslation($language, "I'm allergic to penicillin"),
                "insurance_questions" => getTranslation($language, "Do you accept my insurance?")
            ],
            "grammar" => [
                "point" => "Describing Symptoms and Medical Needs",
                "explanation" => getGrammarExplanation($language, "health_descriptions"),
                "examples" => [
                    getTranslation($language, "I have...") . " (symptoms)",
                    getTranslation($language, "I feel...") . " (sensations)",
                    getTranslation($language, "My... hurts") . " (body parts)"
                ]
            ],
            "conversation" => [
                [
                    "speaker" => "Patient",
                    "text" => getTranslation($language, "Hello, I have an appointment with Dr. Smith at 2 PM."),
                    "translation" => "Hello, I have an appointment with Dr. Smith at 2 PM."
                ],
                [
                    "speaker" => "Receptionist",
                    "text" => getTranslation($language, "Welcome. Please have a seat. The doctor will see you shortly."),
                    "translation" => "Welcome. Please have a seat. The doctor will see you shortly."
                ],
                [
                    "speaker" => "Doctor", 
                    "text" => getTranslation($language, "Good afternoon. What seems to be the problem today?"),
                    "translation" => "Good afternoon. What seems to be the problem today?"
                ],
                [
                    "speaker" => "Patient",
                    "text" => getTranslation($language, "I've had a sore throat and fever since yesterday."),
                    "translation" => "I've had a sore throat and fever since yesterday."
                ]
            ]
        ]
    ];

    // Return the correct topic content or empty content if topic doesn't exist
    if (isset($contentDatabase[$topicName])) {
        return $contentDatabase[$topicName];
    } else {
        // Return empty content structure instead of falling back to Greetings
        return [
            "vocabulary" => [],
            "phrases" => [],
            "grammar" => [
                "point" => "Content Not Available",
                "explanation" => "Lesson content for '{$topicName}' is being developed.",
                "examples" => []
            ],
            "conversation" => []
        ];
    }
}

// Enhanced translation database with more languages
function getTranslation($language, $english) {
    $translations = [
        "French" => [
            // Greetings
            "Hello" => "Bonjour",
            "Goodbye" => "Au revoir",
            "Good morning" => "Bonjour", 
            "Good evening" => "Bonsoir",
            "How are you?" => "Comment ça va?",
            "I'm fine" => "Je vais bien",
            "Thank you" => "Merci",
            "You're welcome" => "De rien",
            
            // Food & Dining
            "Restaurant" => "Restaurant",
            "Menu" => "Menu",
            "Waiter" => "Serveur",
            "Table" => "Table",
            "Order" => "Commander",
            "Bill" => "Addition",
            "Tip" => "Pourboire",
            "Delicious" => "Délicieux",
            
            // Travel & Transportation
            "Airport" => "Aéroport",
            "Train station" => "Gare",
            "Ticket" => "Billet",
            "Passport" => "Passeport",
            "Luggage" => "Bagages",
            "Boarding pass" => "Carte d'embarquement",
            "Departure" => "Départ",
            "Arrival" => "Arrivée",
            
            // Family & Relationships
            "Mother" => "Mère",
            "Father" => "Père",
            "Brother" => "Frère", 
            "Sister" => "Sœur",
            "Parents" => "Parents",
            "Children" => "Enfants",
            "Grandparents" => "Grands-parents",
            "Siblings" => "Frères et sœurs",
            
            // Shopping
            "Store" => "Magasin",
            "Price" => "Prix",
            "Discount" => "Réduction",
            "Size" => "Taille",
            "Fitting room" => "Cabine d'essayage",
            "Cashier" => "Caissier",
            "Receipt" => "Reçu",
            "Exchange" => "Échanger",
            
            // Work & Business
            "Meeting" => "Réunion",
            "Office" => "Bureau",
            "Colleague" => "Collègue",
            "Schedule" => "Emploi du temps",
            "Deadline" => "Date limite",
            "Presentation" => "Présentation",
            "Client" => "Client",
            "Project" => "Projet",
            
            // Health & Emergency
            "Hospital" => "Hôpital",
            "Doctor" => "Docteur",
            "Medicine" => "Médicament",
            "Pain" => "Douleur",
            "Emergency" => "Urgence",
            "Appointment" => "Rendez-vous",
            "Pharmacy" => "Pharmacie",
            "Insurance" => "Assurance"
        ],
        
        "Spanish" => [
            // Greetings
            "Hello" => "Hola",
            "Goodbye" => "Adiós",
            "Good morning" => "Buenos días",
            "Good evening" => "Buenas tardes", 
            "How are you?" => "¿Cómo estás?",
            "I'm fine" => "Estoy bien",
            "Thank you" => "Gracias",
            "You're welcome" => "De nada",
            
            // Food & Dining
            "Restaurant" => "Restaurante",
            "Menu" => "Menú",
            "Waiter" => "Camarero",
            "Table" => "Mesa",
            "Order" => "Pedir",
            "Bill" => "Cuenta",
            "Tip" => "Propina",
            "Delicious" => "Delicioso",
            
            // Travel & Transportation
            "Airport" => "Aeropuerto",
            "Train station" => "Estación de tren",
            "Ticket" => "Billete",
            "Passport" => "Pasaporte",
            "Luggage" => "Equipaje",
            "Boarding pass" => "Tarjeta de embarque",
            "Departure" => "Salida",
            "Arrival" => "Llegada",
            
            // Family & Relationships
            "Mother" => "Madre",
            "Father" => "Padre",
            "Brother" => "Hermano",
            "Sister" => "Hermana",
            "Parents" => "Padres",
            "Children" => "Niños",
            "Grandparents" => "Abuelos",
            "Siblings" => "Hermanos",
            
            // Shopping
            "Store" => "Tienda",
            "Price" => "Precio",
            "Discount" => "Descuento",
            "Size" => "Talla",
            "Fitting room" => "Probador",
            "Cashier" => "Cajero",
            "Receipt" => "Recibo",
            "Exchange" => "Cambiar",
            
            // Work & Business
            "Meeting" => "Reunión",
            "Office" => "Oficina",
            "Colleague" => "Colega",
            "Schedule" => "Horario",
            "Deadline" => "Fecha límite",
            "Presentation" => "Presentación",
            "Client" => "Cliente",
            "Project" => "Proyecto",
            
            // Health & Emergency
            "Hospital" => "Hospital",
            "Doctor" => "Médico",
            "Medicine" => "Medicina",
            "Pain" => "Dolor",
            "Emergency" => "Emergencia",
            "Appointment" => "Cita",
            "Pharmacy" => "Farmacia",
            "Insurance" => "Seguro"
        ],
        
        "German" => [
            // Greetings
            "Hello" => "Hallo",
            "Goodbye" => "Auf Wiedersehen", 
            "Good morning" => "Guten Morgen",
            "Good evening" => "Guten Abend",
            "How are you?" => "Wie geht es dir?",
            "I'm fine" => "Mir geht es gut",
            "Thank you" => "Danke",
            "You're welcome" => "Bitte",
            
            // Food & Dining
            "Restaurant" => "Restaurant",
            "Menu" => "Speisekarte",
            "Waiter" => "Kellner",
            "Table" => "Tisch",
            "Order" => "Bestellen",
            "Bill" => "Rechnung",
            "Tip" => "Trinkgeld",
            "Delicious" => "Köstlich",
            
            // Travel & Transportation
            "Airport" => "Flughafen",
            "Train station" => "Bahnhof",
            "Ticket" => "Fahrkarte",
            "Passport" => "Reisepass",
            "Luggage" => "Gepäck",
            "Boarding pass" => "Bordkarte",
            "Departure" => "Abfahrt",
            "Arrival" => "Ankunft",
            
            // Family & Relationships
            "Mother" => "Mutter",
            "Father" => "Vater",
            "Brother" => "Bruder",
            "Sister" => "Schwester",
            "Parents" => "Eltern",
            "Children" => "Kinder",
            "Grandparents" => "Großeltern",
            "Siblings" => "Geschwister",
            
            // Shopping
            "Store" => "Geschäft",
            "Price" => "Preis",
            "Discount" => "Rabatt",
            "Size" => "Größe",
            "Fitting room" => "Umkleidekabine",
            "Cashier" => "Kassierer",
            "Receipt" => "Quittung",
            "Exchange" => "Umtauschen",
            
            // Work & Business
            "Meeting" => "Besprechung",
            "Office" => "Büro",
            "Colleague" => "Kollege",
            "Schedule" => "Zeitplan",
            "Deadline" => "Frist",
            "Presentation" => "Präsentation",
            "Client" => "Kunde",
            "Project" => "Projekt",
            
            // Health & Emergency
            "Hospital" => "Krankenhaus",
            "Doctor" => "Arzt",
            "Medicine" => "Medizin",
            "Pain" => "Schmerz",
            "Emergency" => "Notfall",
            "Appointment" => "Termin",
            "Pharmacy" => "Apotheke",
            "Insurance" => "Versicherung"
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
            "Restaurant" => "res-toh-rahn",
            "Menu" => "muh-noo",
            "Serveur" => "ser-vuhr",
            "Table" => "tabl",
            "Commander" => "ko-mon-day",
            "Addition" => "a-dee-syon",
            "Pourboire" => "poor-bwar",
            "Délicieux" => "day-lee-syuh",
            "Aéroport" => "ay-eh-roh-por",
            "Gare" => "gar",
            "Billet" => "bee-yay",
            "Passeport" => "pahs-por",
            "Bagages" => "ba-gazh",
            "Carte d'embarquement" => "kart don-bar-kuh-mon",
            "Départ" => "day-par",
            "Arrivée" => "a-ree-vay"
        ],
        "Spanish" => [
            "Hola" => "oh-lah",
            "Adiós" => "ah-dee-ohs",
            "Buenos días" => "bway-nos dee-ahs",
            "Gracias" => "grah-see-ahs",
            "Restaurante" => "res-tow-rahn-te",
            "Menú" => "meh-noo",
            "Camarero" => "kah-mah-reh-ro",
            "Mesa" => "meh-sah",
            "Pedir" => "peh-deer",
            "Cuenta" => "kwen-tah",
            "Propina" => "proh-pee-nah",
            "Delicioso" => "deh-lee-see-oh-so"
        ],
        "German" => [
            "Hallo" => "hah-loh",
            "Auf Wiedersehen" => "owf vee-der-zay-en",
            "Guten Morgen" => "goo-ten mor-gen",
            "Danke" => "dahn-keh",
            "Bitte" => "bit-teh",
            "Restaurant" => "res-toh-rahnt",
            "Speisekarte" => "shpy-zeh-kar-teh",
            "Kellner" => "kel-ner",
            "Tisch" => "tish",
            "Bestellen" => "beh-shtel-len",
            "Rechnung" => "rekh-noong",
            "Trinkgeld" => "trink-gelt"
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
        ],
        "shopping_requests" => [
            "French" => "Use 'Je cherche' (I'm looking for) and 'Avez-vous' (Do you have) when shopping.",
            "Spanish" => "Use 'Estoy buscando' (I'm looking for) and '¿Tiene usted?' (Do you have).",
            "German" => "Use 'Ich suche' (I'm looking for) and 'Haben Sie?' (Do you have).",
            "default" => "Use polite request forms when shopping and asking for assistance."
        ],
        "business_communication" => [
            "French" => "Use formal 'vous' and business vocabulary like 'réunion' (meeting) and 'projet' (project).",
            "Spanish" => "Use formal 'usted' and professional terms like 'reunión' (meeting) and 'proyecto' (project).",
            "German" => "Use formal 'Sie' and business terms like 'Besprechung' (meeting) and 'Projekt' (project).",
            "default" => "Use formal language and professional vocabulary in business settings."
        ],
        "health_descriptions" => [
            "French" => "Use 'J'ai' (I have) for symptoms and 'Ça fait mal' (It hurts) for pain descriptions.",
            "Spanish" => "Use 'Tengo' (I have) for symptoms and 'Me duele' (It hurts me) for pain.",
            "German" => "Use 'Ich habe' (I have) for symptoms and 'Es tut weh' (It hurts) for pain.",
            "default" => "Use clear, descriptive language when discussing health issues and symptoms."
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
    <link rel="stylesheet" href="../../../../public/css/Lessons/lesson.css">
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
                                <div class="word"><?php echo htmlspecialchars($item['word']); ?></div>
                                <div class="translation"><?php echo htmlspecialchars($item['translation']); ?></div>
                                <div class="pronunciation"><?php echo htmlspecialchars($item['pronunciation']); ?></div>
                                <button class="audio-btn" onclick="playAudio('<?php echo htmlspecialchars($item['translation']); ?>')">
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
                                <div class="phrase-text"><?php echo htmlspecialchars($phrase); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'grammar' && !empty($currentLesson['content'])): ?>
                    <div class="grammar-content">
                        <div class="grammar-point">
                            <h3><?php echo htmlspecialchars($currentLesson['content']['point']); ?></h3>
                            <p><?php echo htmlspecialchars($currentLesson['content']['explanation']); ?></p>
                        </div>
                        <?php if (!empty($currentLesson['content']['examples'])): ?>
                            <div class="examples">
                                <h4>Examples:</h4>
                                <?php foreach ($currentLesson['content']['examples'] as $example): ?>
                                    <div class="example"><?php echo htmlspecialchars($example); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($currentLesson['type'] === 'conversation' && !empty($currentLesson['content'])): ?>
                    <div class="conversation-dialogue">
                        <?php foreach ($currentLesson['content'] as $line): ?>
                            <div class="dialogue-line">
                                <span class="speaker"><?php echo htmlspecialchars($line['speaker']); ?>:</span>
                                <span class="text"><?php echo htmlspecialchars($line['text']); ?></span>
                                <span class="translation">(<?php echo htmlspecialchars($line['translation']); ?>)</span>
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
                    <div class="loading-exercise">
                        <i class="fas fa-spinner fa-spin"></i> Loading practice exercise...
                    </div>
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

    <script>
        // Pass PHP data to JavaScript safely
        const lessonData = <?php echo json_encode($currentLesson); ?>;
        const language = "<?php echo addslashes($lang); ?>";
        const topic = "<?php echo addslashes($topic); ?>";
        const currentLessonIndex = <?php echo $currentLessonIndex; ?>;
    </script>
    <script src="../../../../public/js/Lessons/lesson.js"></script>
</body>
</html>