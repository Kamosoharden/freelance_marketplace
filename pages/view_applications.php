<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch application details
$query = "SELECT aj.*, jp.job_title, f.name AS freelancer_name, f.email AS freelancer_email, f.skills
          FROM applied_jobs aj 
          JOIN job_posts jp ON aj.job_id = jp.id 
          JOIN freelancers f ON aj.freelancer_id = f.id 
          WHERE aj.id = '$application_id' AND aj.employer_id = '$employer_id'";
$result = mysqli_query($conn, $query);
$application = mysqli_fetch_assoc($result);

if (!$application) {
    echo "Application not found or you don't have permission to view it.";
    exit();
}

// Mark the application as read
mysqli_query($conn, "UPDATE applied_jobs SET is_read = 1 WHERE id = '$application_id'");

// Handle application status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $update_query = "UPDATE applied_jobs SET status = '$new_status' WHERE id = '$application_id'";
    mysqli_query($conn, $update_query);
    $application['status'] = $new_status;
}

// Display application details and allow status update
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
</head>
<body>
    <h1>Application Details</h1>
    <p>Job Title: <?php echo htmlspecialchars($application['job_title']); ?></p>
    <p>Freelancer: <?php echo htmlspecialchars($application['freelancer_name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($application['freelancer_email']); ?></p>
    <p>Skills: <?php echo htmlspecialchars($application['skills']); ?></p>
    <p>Applied on: <?php echo htmlspecialchars($application['apply_date']); ?></p>
    <p>Current Status: <?php echo htmlspecialchars($application['status']); ?></p>

    <form method="POST">
        <label for="status">Update Status:</label>
        <select name="status" id="status">
            <option value="Pending" <?php echo $application['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Approved" <?php echo $application['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="Rejected" <?php echo $application['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <button type="submit">Update Status</button>
    </form>
</body>
</html>