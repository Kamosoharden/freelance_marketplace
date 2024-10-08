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

// Check if employer is logged in
if (!isset($_SESSION['employer_user_email'])) {
    echo "You must be logged in as an employer to hire a freelancer.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the data
    $employer_email = $_SESSION['employer_user_email'];
    $freelancer_id = mysqli_real_escape_string($conn, $_POST['freelancer_id']);
    $money = mysqli_real_escape_string($conn, $_POST['money']);
    $job_category = mysqli_real_escape_string($conn, $_POST['job_category']);
    $period_time = mysqli_real_escape_string($conn, $_POST['period_time']);

    // Get the employer's ID from the email
    $employer_query = mysqli_query($conn, "SELECT id FROM employers WHERE email='$employer_email'");
    $employer_result = mysqli_fetch_assoc($employer_query);
    $employer_id = $employer_result['id'];

    // Insert into hires table
    $sql = "INSERT INTO hires (employer_id, freelancer_id, status, money, job_category, period_time)
            VALUES ('$employer_id', '$freelancer_id', 'pending','$money', '$job_category', '$period_time')";

    if (mysqli_query($conn, $sql)) {
        echo "Freelancer successfully hired!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
mysqli_close($conn);
?>
