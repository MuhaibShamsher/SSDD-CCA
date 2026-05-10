<?php
$conn = new mysqli("localhost", "root", "", "vuln_web_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // No validation or sanitization
    $password = $_POST['password']; // Stored as plain text

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insecure Registration</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <h2>Register (Insecure)</h2>
    <form action="register.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Register">

         <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</body>
</html>
