<?php
// config/config.php - FIXED VERSION
define('DB_HOST', 'localhost');
define('DB_NAME', 'smartstudy');  // ← Your database name
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for XAMPP/WAMP
define('URLROOT', 'http://localhost/smartstudy');
define('APPROOT', dirname(__DIR__));

// OpenAI configuration
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-3.5-turbo');

// Debug mode
define('DEBUG', true);