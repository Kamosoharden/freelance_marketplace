<?php
// Simulate that the funds are held in escrow
$funds_in_escrow = true;

if ($funds_in_escrow) {
    echo "<h1>Job Ready to Start</h1>";
    echo "<p>Funds are successfully deposited in escrow. The freelancer can now start the job.</p>";
} else {
    echo "<h1>Payment Pending</h1>";
    echo "<p>Funds have not been deposited to escrow. Please complete the payment to proceed.</p>";
}
?>
