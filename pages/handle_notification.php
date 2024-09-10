<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'];
    $id = $data['id'];
    $type = $data['type'];
    $reason = isset($data['reason']) ? mysqli_real_escape_string($conn, $data['reason']) : '';

    if ($type === 'hire_request') {
        if ($action === 'accept') {
            $sql = "UPDATE hires SET status='approved' WHERE id='$id'";
        } elseif ($action === 'reject') {
            $sql = "UPDATE hires SET status='canceled', cancel_reason='$reason' WHERE id='$id'";
        }
    } elseif ($type === 'job_offer') {
        // Handle job offer actions if needed
    }

    if (mysqli_query($conn, $sql)) {
        echo "Action successfully processed.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}

mysqli_close($conn);
?>
