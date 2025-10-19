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
2. Copy `.env.example` to `.env` (default XAMPP settings already configured)
3. Start XAMPP (Apache + MySQL)
4. Create a database named `language_learning` in phpMyAdmin
5. Run `config/setup_database.php` to create tables and admin user
6. Login with credentials defined in `.env` file (default: admin@gmail.com / admin123)

**Note:** All configuration (database, admin user) is in `.env` file. Schema is in `database/schema.sql`.

## License
This project is licensed under the MIT License.