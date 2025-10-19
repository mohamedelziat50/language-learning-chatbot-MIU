# Language Learning Chatbot Platform

This platform is designed to facilitate language learning through interactive chatbot sessions, discussion forums, progress tracking, and gamification features. It supports multiple user roles: students, tutors, and admins.

## Features
- **User Authentication**: Secure login for students, tutors, and admins.
- **Chatbot Interaction**: AI-powered chatbot for language practice.
- **Discussion Forum**: Engage in topic-based discussions.
- **Progress Tracking**: Monitor learning progress.
- **Gamification**: Earn badges and rewards for achievements.

## File Structure
- `app/`: Contains the MVC components (controllers, models, views).
- `config/`: Configuration files and database setup script.
- `database/`: Database scripts and migrations.
- `public/`: Publicly accessible assets (CSS, JS, images).

## Setup
1. Clone the repository
2. Start XAMPP (Apache + MySQL)
3. Create a database named `language_learning` in phpMyAdmin
4. Run `config/setup_database.php` to create the users table
5. Access the application via `index.php`

**Note:** `config/db_connect.php` is already configured for default XAMPP settings. Only modify if you have custom database credentials.

## License
This project is licensed under the MIT License.