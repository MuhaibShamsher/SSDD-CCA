<?php
session_start();
require_once('db/db_config.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT comment FROM comments ORDER BY id DESC";
$result = $conn->query($sql);

$comments = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row['comment'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Comment</title>
    <link rel="stylesheet" type="text/css" href="css/form.css">
</head>
<body>
    <div class="container">
        <h2>Leave a Comment</h2>
        <form action="submit_form.php" method="POST">
            <label for="comment">Your Comment:</label><br>
            <textarea name="comment" id="comment" rows="5" required></textarea><br><br>
            <input type="submit" value="Submit">
        </form>
        <p><a href="dashboard.php">Back to Dashboard</a></p>

        <h3>All Comments:</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="comment"><?= htmlspecialchars($comment) ?></div>
        <?php endforeach; ?>
    </div>
</body>
</html>
