<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Marketplace - Escrow Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
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
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
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
    <p>To start the job, the employer must first deposit funds into the escrow account. This ensures the freelancer will be paid upon successful completion of the project.</p>
    
    <form action="pay.php" method="POST">
        <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Enter the name" required>
        </div>
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
