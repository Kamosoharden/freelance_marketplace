<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $profile_description = $_POST['profile_description'];

    // Check if email already exists
    $checkEmailQuery = "SELECT email FROM freelancers WHERE email = '$email'";
    $checkEmployerEmailQuery = "SELECT email FROM employers WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);
    $employerResult = $conn->query($checkEmailQuery);

    if($password != $confirm_password){
        echo "<script> alert('Password does not match.') </script>";
        echo "<script> window.history.back(); </script>"; // Redirect back to the signup form
    }elseif ($result->num_rows > 0 || $employerResult->num_rows > 0) {
        // Email already exists
        echo "<script> alert('Email already exists. Please use a different email.') </script>";
        echo "<script> window.history.back(); </script>"; // Redirect back to the signup form
    } else {
        // Email does not exist, proceed with insertion
        if (isset($_POST['register_freelancer'])) {
            $skills = $_POST['skills'];
            
            $sql = "INSERT INTO freelancers (username, email, password, name, description, skills)
                    VALUES ('$username', '$email', '$password', '$full_name', '$profile_description', '$skills')";
                    
            if ($conn->query($sql) === TRUE) {
                echo "<script> alert('Registered successfully') </script>";
                echo "<script> window.location.href = './freelancerlogin.html' </script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } elseif (isset($_POST['register_employer'])) {
            $company_name = $_POST['company_name'];
            
            $sql = "INSERT INTO employers (username, email, password, name, description, company)
                    VALUES ('$username', '$email', '$password', '$full_name', '$profile_description', '$company_name')";

            if ($conn->query($sql) === TRUE) {
                echo "<script> alert('Registered successfully') </script>";
                echo "<script> window.location.href = './employerlogin.html' </script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    }

    $conn->close();
}
?>

