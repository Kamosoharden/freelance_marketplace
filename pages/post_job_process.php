<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Change this to your DB username
$password = "";     // Change this to your DB password
$dbname = "freelance";

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
$jobRules = htmlspecialchars($_POST['job-rules']); // New field

// Prepare and bind the SQL statement
$stmt = $conn->prepare("INSERT INTO job_posts (job_title, job_type, payment_range, job_description, project_duration, skills_required, job_rules) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $jobTitle, $jobType, $paymentRange, $jobDescription, $projectDuration, $skillsRequired, $jobRules);

// Execute the statement
if ($stmt->execute()) {
    echo "New job posted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
