<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $freelancerQuery = "SELECT id FROM freelancers WHERE email = '$email'";
    $employerQuery = "SELECT id FROM employers WHERE email = '$email'";
    $freelancerResult = $conn->query($freelancerQuery);
    $employerResult = $conn->query($employerQuery);

    if ($freelancerResult->num_rows > 0) {
        $userType = 'freelancer';
        $userId = $freelancerResult->fetch_assoc()['id'];
    } elseif ($employerResult->num_rows > 0) {
        $userType = 'employer';
        $userId = $employerResult->fetch_assoc()['id'];
    } else {
        echo "<script> alert('Email does not exist.') </script>";
        echo "<script> window.history.back(); </script>";
        exit;
    }

    $token = bin2hex(random_bytes(50));
    $expiration = date("Y-m-d H:i:s", strtotime('+1 hour'));

    if ($userType == 'freelancer') {
        $updateTokenQuery = "UPDATE freelancers SET reset_token = '$token', token_expiration = '$expiration' WHERE id = $userId";
    } else {
        $updateTokenQuery = "UPDATE employers SET reset_token = '$token', token_expiration = '$expiration' WHERE id = $userId";
    }

    if ($conn->query($updateTokenQuery) === TRUE) {
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script> alert('Password reset link sent to your email.') </script>";
            echo "<script> window.location.href = './freelancerlogin.html' </script>";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Error: " . $updateTokenQuery . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
