<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM freelancers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if ($password == $hashed_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_email'] = $email;
            header("Location: freelancerdashboard.php");
        } else {
            echo "<script>alert('Incorrect password');</script>";
            echo "<script>window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found');</script>";
        echo "<script>window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
