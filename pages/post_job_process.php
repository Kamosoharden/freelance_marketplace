<?php
session_start(); 

$servername = "localhost";
$username = "root"; 
$password = "";    
$dbname = "freelance";

if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

$id = $_SESSION['user_id']; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data and sanitize input
$jobTitle = htmlspecialchars($_POST['job-title']);
$jobType = htmlspecialchars($_POST['job-type']);
$paymentRange = htmlspecialchars($_POST['payment-range']);
$jobDescription = htmlspecialchars($_POST['job-description']);
$projectDuration = htmlspecialchars($_POST['project-duration']);
$skillsRequired = htmlspecialchars($_POST['skills-required']);
$jobRules = htmlspecialchars($_POST['job-rules']);

$stmt = $conn->prepare("INSERT INTO job_posts (job_title, job_type, payment_range, job_description, project_duration, skills_required, employer_id, job_rules) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssss", $jobTitle, $jobType, $paymentRange, $jobDescription, $projectDuration, $skillsRequired, $id, $jobRules);

if ($stmt->execute()) {
    echo "New job posted successfully!";
    header("Location: employerdashboard.php");
    exit(); 
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
