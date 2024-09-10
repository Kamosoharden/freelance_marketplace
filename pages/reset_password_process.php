<?php
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
    $token = $_POST['token'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword != $confirmPassword) {
        echo "<script> alert('Passwords do not match.') </script>";
        echo "<script> window.history.back(); </script>";
        exit;
    }

    // Check if token is valid and not expired
    $freelancerQuery = "SELECT id FROM freelancers WHERE reset_token = '$token' AND token_expiration > NOW()";
    $employerQuery = "SELECT id FROM employers WHERE reset_token = '$token' AND token_expiration > NOW()";
    $freelancerResult = $conn->query($freelancerQuery);
    $employerResult = $conn->query($employerQuery);

    if ($freelancerResult->num_rows > 0) {
        $userType = 'freelancer';
        $userId = $freelancerResult->fetch_assoc()['id'];
    } elseif ($employerResult->num_rows > 0) {
        $userType = 'employer';
        $userId = $employerResult->fetch_assoc()['id'];
    } else {
        echo "<script> alert('Invalid or expired token.') </script>";
        echo "<script> window.history.back(); </script>";
        exit;
    }

    if ($userType == 'freelancer') {
        $updatePasswordQuery = "UPDATE freelancers SET password = '$newPassword', reset_token = NULL, token_expiration = NULL WHERE id = $userId";
    } else {
        $updatePasswordQuery = "UPDATE employers SET password = '$newPassword', reset_token = NULL, token_expiration = NULL WHERE id = $userId";
    }

    if ($conn->query($updatePasswordQuery) === TRUE) {
        echo "<script> alert('Password reset successfully.') </script>";
        echo "<script> window.location.href = './freelancerlogin.html' </script>";
    } else {
        echo "Error: " . $updatePasswordQuery . "<br>" . $conn->error;
    }

    $conn->close();
}
?>