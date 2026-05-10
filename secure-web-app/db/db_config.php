<?php
$env_file = dirname(__DIR__, 2) . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        putenv($key . '=' . $value);
    }
}

$servername = getenv('DB_HOST_SECURE') ?: 'localhost';
$username = getenv('DB_USER_SECURE') ?: 'root';
$password = getenv('DB_PASSWORD_SECURE') ?: '';
$dbname = getenv('DB_NAME_SECURE') ?: 'secure_web_app';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
