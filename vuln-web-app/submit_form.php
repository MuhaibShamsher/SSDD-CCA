<?php

$conn = new mysqli("localhost", "root", "", "vuln_web_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$comment = $_POST['comment'];
$comment = addslashes($comment);

$sql = "INSERT INTO comments (comment) VALUES ('$comment')";

if ($conn->query($sql) === TRUE) {
    header("Location: form.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
