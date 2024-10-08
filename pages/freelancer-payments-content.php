<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_log', 'php-error.log');
ini_set('log_errors', 1);

require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'freelancer') {
    die("Unauthorized access");
}

$freelancer_id = $_SESSION['user_id'];

// Fetch payments
$sql = "SELECT p.id as payment_id, jp.job_title, p.amount, p.payment_date, p.status
        FROM payments p
        JOIN job_posts jp ON p.job_id = jp.id
        WHERE p.freelancer_id = ?
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
?>

<h2>My Payments</h2>
<div id="payments-results">
    <?php if (count($payments) > 0): ?>
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['job_title']) ?></td>
                        <td><?= htmlspecialchars($payment['amount']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                        <td><?= htmlspecialchars($payment['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-payments">No payments received yet.</p>
    <?php endif; ?>
</div>