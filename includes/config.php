<?php
// MealConnect — Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mealconnect');
define('SITE_NAME', 'MealConnect');
define('SITE_URL', 'http://localhost/MealConnect');
define('CURRENCY', 'PKR');

// Groq API - replace with your Groq API key and model/endpoint as needed
define('GROQ_API_KEY', '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');
define('GROQ_MODEL', 'groq-1');
define('GROQ_API_URL', 'https://api.groq.ai/v1/models/' . GROQ_MODEL . '/outputs');

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
