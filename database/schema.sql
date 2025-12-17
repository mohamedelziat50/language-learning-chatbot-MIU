-- Database Schema for Language Learning Chatbot
-- Run this via setup_database.php or import directly into phpMyAdmin

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','tutor','admin') DEFAULT 'student',
    selected_language_id INT UNSIGNED DEFAULT '1', -- Foreign key to languages.language_id
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (selected_language_id) REFERENCES languages(language_id) ON DELETE SET NULL
);

-- Documents table for user-created documents
CREATE TABLE IF NOT EXISTS documents (
    document_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL, -- FOREIGN KEY TO users.user_id
    title VARCHAR(255) NOT NULL DEFAULT 'Untitled Document',
    content TEXT, -- NO LIMIT ON CONTENT
    preview_text VARCHAR(120), -- FIRST 120 CHARS OF CONTENT
    language VARCHAR(50) DEFAULT 'English', -- DEFAULT LANGUAGE IS ENGLISH, Later should reference languages table & it's id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- WHEN DOCUMENT IS CREATED
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- WHEN DOCUMENT IS UPDATED
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE, -- WHEN USER IS DELETED, ALL DOCUMENTS ARE DELETED
    INDEX idx_owner_id (owner_id), -- INDEX ON owner_id, SPEED UP SEARCH BY OWNER_ID
    INDEX idx_updated_at (updated_at) -- INDEX ON updated_at, SPEED UP SEARCH BY UPDATED_AT
);

-- Languages table (for your Language model)
CREATE TABLE IF NOT EXISTS languages (
    language_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    flag VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some common languages
INSERT INTO languages (name, code, flag) VALUES
('English', 'en', '🇺🇸'),
('Spanish', 'es', '🇪🇸'),
('French', 'fr', '🇫🇷'),
('German', 'de', '🇩🇪'),
('Italian', 'it', '🇮🇹'),
('Japanese', 'ja', '🇯🇵'),
('Chinese', 'zh', '🇨🇳'),
('Arabic', 'ar', '🇸🇦'),
('Russian', 'ru', '🇷🇺'),
('Portuguese', 'pt', '🇵🇹');