<?php
// Get the form data from the HTML
$amount = $_POST['amount'];
$payment_method = $_POST['payment_method'];

// Validate the amount
if ($amount <= 0) {
    echo 'Invalid payment amount. Please enter a valid amount.';
    exit;
}

// Simulate payment processing
echo "<h1>Payment Processing</h1>";
echo "<p>Processing payment of RWF $amount using " . ucfirst($payment_method) . "...</p>";

// Simulate different payment methods (In reality, you would integrate with a payment gateway's API)
if ($payment_method === 'mobile_money') {
    // Simulate Mobile Money API interaction
    echo "<p>Mobile Money payment initiated. Funds will be deposited to the escrow account.</p>";
} elseif ($payment_method === 'credit_debit_card') {
    // Simulate Credit/Debit card payment interaction
    echo "<p>Credit/Debit Card payment processed. Funds will be deposited to the escrow account.</p>";
}

// Escrow Logic
// Store the payment in escrow (In real-world applications, you would store this in a database for later release)
echo "<p><strong>Funds of RWF $amount are held in escrow until the job is completed.</strong></p>";

// Simulate redirection to success page
echo '<p><a href="job_start.php">Click here to proceed with starting the job</a></p>';
?>
