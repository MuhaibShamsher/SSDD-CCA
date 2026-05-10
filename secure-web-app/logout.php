<?php
session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"],     // Set to true if using HTTPS
        $params["httponly"]
    );
}

session_destroy();

header("Location: login.php");
exit();
?>
