<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if freelancer is logged in
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];

    // Fetch job offers
    $job_query = mysqli_query($conn, "SELECT * FROM job_offers WHERE freelancer_email='$email'");

    // Fetch hire requests
    $hire_query = mysqli_query($conn, "SELECT h.*, e.company FROM hires h JOIN employers e ON h.employer_id = e.id WHERE h.freelancer_id = (SELECT id FROM freelancers WHERE email='$email') AND h.status='pending'");

    echo '<div class="notifications">';

    // Display job offers
    while ($row = mysqli_fetch_assoc($job_query)) {
        echo '<div class="notification-item">';
        echo '<p><strong>Job Title:</strong> ' . $row['job_title'] . '</p>';
        echo '<p><strong>Job Description:</strong> ' . $row['job_description'] . '</p>';
        echo '<div class="notification-actions">';
        echo '<button class="accept" onclick="handleNotification(\'accept\', ' . $row['job_id'] . ', \'job_offer\')">Accept</button>';
        echo '<button class="reject" onclick="handleNotification(\'reject\', ' . $row['job_id'] . ', \'job_offer\')">Reject</button>';
        echo '</div>';
        echo '</div>';
    }

    // Display hire requests
    while ($row = mysqli_fetch_assoc($hire_query)) {
        echo '<div class="notification-item">';
        echo '<p><strong>Company:</strong> ' . $row['company'] . '</p>';
        echo '<p><strong>Job Category:</strong> ' . $row['job_category'] . '</p>';
        echo '<p><strong>Payment:</strong> ' . $row['money'] . '</p>';
        echo '<p><strong>Period:</strong> ' . $row['period_time'] . '</p>';
        echo '<div class="notification-actions">';
        echo '<button class="accept" onclick="handleNotification(\'accept\', ' . $row['id'] . ', \'hire_request\')">Accept</button>';
        echo '<button class="reject" onclick="showCancelReason(' . $row['id'] . ')">Reject</button>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
} else {
    echo "You must be logged in to view notifications.";
}
?>
