<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, password FROM employers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $stored_password);
        $stmt->fetch();

        // Since you are not hashing the password, direct comparison is used
        if ($password == $stored_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['employer_user_email'] = $email;

            // Output alert before the redirect
            echo "<script>alert('Login successful. User ID: ".$_SESSION['user_id']."');</script>";

            // Ensure alert is processed before redirecting
            echo "<script>window.location.href='employerdashboard.php';</script>";
            exit();

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
