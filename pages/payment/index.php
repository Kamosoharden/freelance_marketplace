<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['employer_user_email'])) {
    header("Location: ../employerlogin.html");
    exit();
}

$email = $_SESSION['employer_user_email'];

$query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employers WHERE email='$email'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Marketplace - Escrow Payment</title>
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="../../css/style.css" rel="stylesheet" />
    <link href="../../css/responsive.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1c1c1c;
            color: #ffffff;
        }
        .navbar {
            background-color: #2c2c2c;
            padding: 10px 0;
        }
        .navbar-brand {
            color: #ffffff !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .nav-link {
            color: #ffffff !important;
        }
        .nav-link:hover {
            color: #32cc32 !important;
        }
        .container {
            margin-top: 50px;
        }
        .payment-header {
            background-color: #2c2c2c;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .payment-header h1 {
            color: #ffffff;
            font-weight: bold;
        }
        .payment-header p {
            color: #cccccc;
        }
        .content-section {
            background-color: #2c2c2c;
            border-radius: 10px;
            padding: 20px;
        }
        .form-group label {
            color: #ffffff;
        }
        .form-control {
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #32cc32;
            border-color: #32cc32;
        }
        .btn-primary:hover {
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="payment-header">
        <h1>Ready to Start?</h1>
        <p>To start the job, you must first deposit funds into the escrow account. This ensures the freelancer will be paid upon successful completion of the project.</p>
    </div>
    
    <div class="content-section">
        <form action="./payment/pay.php" method="POST">
            <div class="form-group">
                <input type="hidden" class="form-control" id="email" name="email" value="<?php echo $query['email']; ?>" readonly>
            </div>
            <div class="form-group">
                <input type="hidden" class="form-control" id="name" name="name" value="<?php echo $query['name']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="amount">Amount (RWF)</label>
                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter the amount" required>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" name="pay">Send Payment</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>