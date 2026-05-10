<?php
session_start();
require_once('db/db_config.php');
$max_attempts = 5;
$cooldown_time = 60; // seconds

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        $attempts = 0;
        $last_attempt = 0;
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $attempts = (int)$row['attempts'];
            $last_attempt = (int)$row['last_attempt'];
        }
        $stmt->close();

        if ($attempts >= $max_attempts && (time() - $last_attempt) < $cooldown_time) {
            die("Too many login attempts. Please wait $cooldown_time seconds and try again.");
        }

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $del = $conn->prepare("DELETE FROM login_attempts WHERE username = ?");
                $del->bind_param("s", $username);
                $del->execute();
                $del->close();

                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
                header('Location: dashboard.php');
                exit();
            } else {
                if ($attempts > 0) {
                    $attempts++;
                    $up = $conn->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = ? WHERE username = ?");
                    $now = time();
                    $up->bind_param("iis", $attempts, $now, $username);
                    $up->execute();
                    $up->close();
                } else {
                    $now = time();
                    $ins = $conn->prepare("INSERT INTO login_attempts (username, attempts, last_attempt) VALUES (?, 1, ?)");
                    $ins->bind_param("si", $username, $now);
                    $ins->execute();
                    $ins->close();
                }

                $error_message = "Invalid username or password.";
            }
        } else {
            if ($attempts > 0) {
                $attempts++;
                $up = $conn->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = ? WHERE username = ?");
                $now = time();
                $up->bind_param("iis", $attempts, $now, $username);
                $up->execute();
                $up->close();
            } else {
                $now = time();
                $ins = $conn->prepare("INSERT INTO login_attempts (username, attempts, last_attempt) VALUES (?, 1, ?)");
                $ins->bind_param("si", $username, $now);
                $ins->execute();
                $ins->close();
            }
            $error_message = "Invalid username or password.";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login" class="button">
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
