<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'freelancer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$freelancer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);

    // Check if already applied
    $check_query = "SELECT * FROM applied_jobs WHERE freelancer_id = ? AND job_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $freelancer_id, $job_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert into applied_jobs table
            $insert_query = "INSERT INTO applied_jobs (job_id, freelancer_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ii", $job_id, $freelancer_id);
            $insert_stmt->execute();

            // Get the employer_id for the job
            $employer_query = "SELECT employer_id FROM job_posts WHERE id = ?";
            $employer_stmt = $conn->prepare($employer_query);
            $employer_stmt->bind_param("i", $job_id);
            $employer_stmt->execute();
            $employer_result = $employer_stmt->get_result();
            $employer_row = $employer_result->fetch_assoc();
            $employer_id = $employer_row['employer_id'];

            // Insert notification for the employer
            $notification_query = "INSERT INTO notifications (user_id, message, is_read) VALUES (?, 'A freelancer has applied for your job post.', 0)";
            $notification_stmt = $conn->prepare($notification_query);
            $notification_stmt->bind_param("i", $employer_id);
            $notification_stmt->execute();

            // Commit the transaction
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'You have successfully applied for the job.']);
        } catch (Exception $e) {
            // An error occurred; rollback the transaction
            $conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error applying for the job: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You have already applied for this job.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();
?>