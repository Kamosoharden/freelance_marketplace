<?php
session_start();
// Assuming you have a database connection established
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SESSION['employer_user_email']) {
    $email = $_SESSION['employer_user_email'];
    $query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employers WHERE email='$email'"));
    $employer_name = $query['company']; // Assuming 'company' is the field for employer name
} else {
    header("Location: ../employerlogin.html");
    exit();
}

// Add this near the top of the file
$hire_id = isset($_GET['hire_id']) ? intval($_GET['hire_id']) : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Marketplace - Escrow Payment</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1c1c1c;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #2c2c2c;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1, p {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 80%;
            margin: 0 auto;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
            align-self: flex-start;
        }

        input {
            width: 95%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #4c4c4c;
            border-radius: 5px;
            font-size: 16px;
            background-color: #3c3c3c;
            color: #ffffff;
        }

        input[type="submit"] {
            background-color: #32cc32;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #28a745;
        }

        .button-container {
            text-align: center;
            align-self: center;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Ready to Start?</h1>
    <p>To start the job, you must first deposit funds into the escrow account. This ensures the freelancer will be paid upon successful completion of the project.</p>
    
    <form action="pay.php" method="POST">
        <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <input type="hidden" id="name" name="name" value="<?php echo htmlspecialchars($employer_name); ?>" required>
        <input type="hidden" id="hire_id" name="hire_id" value="<?php echo $hire_id; ?>" required>
        
        <div>
        <label for="amount">Amount (RWF)</label>
        <input type="number" id="amount" name="amount" placeholder="Enter the amount" required>
        </div>

        <div class="button-container">
            <input type="submit" name="pay" value="Send Payment">
        </div>
    </form>
</div>

</body>
</html>
