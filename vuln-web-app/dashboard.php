<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>This is a fake protected page (no real security).</p>

        <!-- Link to vulnerable profile page (IDOR) -->
        <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="button">My Profile</a>
        
        <br><br>
        <a href="form.php" class="button">Go to Form</a>

        <br><br>
        <a href="logout.php" class="button logout-button">Logout</a>
    </div>
</body>
</html>
