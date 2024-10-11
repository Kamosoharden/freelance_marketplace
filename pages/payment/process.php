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

// Get the hire_id and type from the session
$hire_id = isset($_SESSION['current_hire_id']) ? $_SESSION['current_hire_id'] : 0;
$type = isset($_SESSION['payment_type']) ? $_SESSION['payment_type'] : '';

// Check if the payment was successful
if (isset($_GET['status']) && $_GET['status'] == 'successful') {
    if ($type == 'hire') {
        // It's from the hires table
        $sql = "UPDATE hires SET status = 'start' WHERE id = ?";
        $table = "hires";
    } elseif ($type == 'applied_job') {
        // It's from the applied_jobs table
        $sql = "UPDATE applied_jobs SET status = 'start' WHERE id = ?";
        $table = "applied_jobs";
    } else {
        echo "<script>alert('Error: Invalid job type');</script>";
        goto redirect;
    }

    // Prepare and execute the update statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hire_id);
    $result = $stmt->execute();

    if ($result) {
        // Fetch the freelancer_id
        $select_sql = "SELECT freelancer_id FROM $table WHERE id = ?";
        $stmt = $conn->prepare($select_sql);
        $stmt->bind_param("i", $hire_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $freelancer_id = $row['freelancer_id'];
            echo "<script>alert('Payment successful! Freelancer ID: " . $freelancer_id . " (from " . $table . " table)');</script>";
        } else {
            echo "<script>alert('Error: Could not fetch freelancer ID');</script>";
        }
    } else {
        echo "<script>alert('Error: Could not update status');</script>";
    }
} else {
    echo "<script>alert('Payment was not successful');</script>";
}

redirect:
// Clear the session variables
unset($_SESSION['current_hire_id']);
unset($_SESSION['payment_type']);

// Redirect back to the employer dashboard
echo "<script>window.location.href = '../employerdashboard.php';</script>";

$conn->close();
?>