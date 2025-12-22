-- Add native_language_id field to users table
-- This allows users to specify their native language

ALTER TABLE users 
ADD COLUMN native_language_id INT UNSIGNED DEFAULT 1 AFTER role,
ADD FOREIGN KEY (native_language_id) REFERENCES languages(language_id) ON DELETE SET NULL;

-- Set default native language to English (language_id = 1) for existing users
UPDATE users SET native_language_id = 1 WHERE native_language_id IS NULL;
