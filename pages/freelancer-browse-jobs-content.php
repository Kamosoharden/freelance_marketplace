<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'freelancer') {
    die("Unauthorized access");
}

$freelancer_id = $_SESSION['user_id'];

// Fetch available jobs
$sql = "SELECT jp.id, jp.job_title, jp.job_description, jp.payment_range, jp.project_duration, jp.skills_required, 
               CASE WHEN aj.freelancer_id IS NOT NULL THEN 1 ELSE 0 END AS applied
        FROM job_posts jp
        LEFT JOIN applied_jobs aj ON jp.id = aj.job_id AND aj.freelancer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
?>

<h2>Available Jobs</h2>
<div id="job-results">
    <?php if (count($jobs) > 0): ?>
        <?php foreach ($jobs as $job): ?>
            <div class="job-item">
                <h3><?= htmlspecialchars($job['job_title']) ?></h3>
                <p class="job-description"><?= htmlspecialchars($job['job_description']) ?></p>
                <div class="job-details">
                    <p><strong>Payment range:</strong> <?= htmlspecialchars($job['payment_range']) ?></p>
                    <p><strong>Duration:</strong> <?= htmlspecialchars($job['project_duration']) ?></p>
                </div>
                <p><strong>Skills required:</strong> <?= htmlspecialchars($job['skills_required']) ?></p>
                <div class="job-actions">
                    <?php if ($job['applied']): ?>
                        <button class="btn btn-applied" disabled>Applied</button>
                    <?php else: ?>
                        <button class="btn btn-apply" data-job-id="<?= $job['id'] ?>">Apply</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-jobs">No jobs found.</p>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.btn-apply').forEach(button => {
    button.addEventListener('click', function() {
        const jobId = this.getAttribute('data-job-id');
        fetch('apply-for-job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `job_id=${jobId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                this.textContent = 'Applied';
                this.disabled = true;
                this.classList.remove('btn-apply');
                this.classList.add('btn-applied');
            }
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while applying for the job.');
        });
    });
});
</script>