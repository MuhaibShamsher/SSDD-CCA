<?php
session_start();
require_once('db/db_config.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['comment']) || empty(trim($_POST['comment']))) {
        die("Comment cannot be empty.");
    }

    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO comments (comment) VALUES (?)");
    $stmt->bind_param("s", $comment);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: form.php");
        exit();
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
