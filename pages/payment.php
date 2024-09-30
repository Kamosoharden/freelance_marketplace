<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>PAYMENT</h1>

    <form method="post" action="checkout.php">
        <p>DEPOSIT</p>
        <input type="text" name="product_name" placeholder="Product Name" required>
        <input type="text" name="amount" placeholder="Amount" required>
        <input type="text" name="currency" placeholder="Currency" required>
        <input type="text" name="description" placeholder="Description" required>
        <input type="text" name="payment_method" placeholder="Payment Method" required>
        <input type="text" name="customer" placeholder="Customer" required>

        <button>Pay</button>
    </form>

</body>
</html>
