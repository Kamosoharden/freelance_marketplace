<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'freelancer') {
    die("Unauthorized access");
}

$freelancer_id = $_SESSION['user_id'];

// Fetch applied jobs
$sql = "SELECT aj.id as application_id, jp.job_title, jp.payment_range, jp.project_duration, 
               e.company, aj.apply_date, aj.status
        FROM applied_jobs aj
        JOIN job_posts jp ON aj.job_id = jp.id
        JOIN employers e ON jp.employer_id = e.id
        WHERE aj.freelancer_id = ?
        ORDER BY aj.apply_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}
?>

<h2>My Applications</h2>
<div id="applications-results">
    <?php if (count($applications) > 0): ?>
        <?php foreach ($applications as $application): ?>
            <div class="job-item">
                <h3><?= htmlspecialchars($application['job_title']) ?></h3>
                <div class="job-details">
                    <p><strong>Company:</strong> <?= htmlspecialchars($application['company']) ?></p>
                    <p><strong>Payment range:</strong> <?= htmlspecialchars($application['payment_range']) ?></p>
                    <p><strong>Duration:</strong> <?= htmlspecialchars($application['project_duration']) ?></p>
                </div>
                <p><strong>Application Date:</strong> <?= htmlspecialchars($application['apply_date']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-applications">You haven't applied to any jobs yet.</p>
    <?php endif; ?>
</div>