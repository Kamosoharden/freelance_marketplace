<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SESSION['user_email']) {
    $email = $_SESSION['user_email'];
    $query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM freelancers WHERE email='$email'"));
    $notifications = mysqli_query($conn, "SELECT * FROM job_offers WHERE freelancer_email='$email'");
} else {
    header("Location: freelancerlogin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_POST['job_id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        $update_query = "UPDATE job_offers SET status='Accepted' WHERE job_id='$job_id'";
    } else if ($action == 'reject') {
        $update_query = "UPDATE job_offers SET status='Rejected' WHERE job_id='$job_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        header("Location: freelancerdashboard.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Dashboard</title>
    <link rel="stylesheet" href="./../layout/styles/freelancerlogin.css">
    <style>
        body {
            font-family: cursive;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
            margin: 0 auto;
            padding: 20px;
            background-color: #6a747b;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 70px;
            border-radius: 100px;
            margin-top: 3;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 35px;
            display: inline-block;
            color: #000000;
            font-weight: bold;
        }

        .header h1 span {
            color: #32cc32;
        }

        .header a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            margin-top: 10px;
        }

        .nav {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .nav a, .nav button {
            text-decoration: none;
            color: #000000;
            padding: 10px 20px;
            background-color: #e9ecef;
            border-radius: 15px;
            margin-right: 20px;
            margin-left: 10px;
            margin-top: 20px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .nav a:hover, .nav button:hover {
            background-color: #d0d4d8;
        }

        .content {
            margin-top: 20px;
        }

        .form-container {
            display: none;
            flex-direction: column;
            align-items: center;
            color: #000000;
            font-size: medium;
            font-weight: bold;
            text-transform: uppercase;
        }

        .form-container.active {
            display: flex;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%;
            max-width: 500px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            text-align: left;
            font-size: medium;
            color: #000000;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .btn {
            padding: 10px 25px;
            background-color: #28a745;
            color: #ffffff;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #218838;
        }

        .dropdown {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .dropdown .form-group {
            width: 100%;
            max-width: 300px;
        }

        #notifications {
            display: none;
            flex-direction: column;
            align-items: center;
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            max-width: 350px;
            width: 100%;
            margin-left: 240px;
            margin-bottom: 40px;
        }

        #notifications.active {
            display: flex;
        }

        .notification-item {
            width: 100%;
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .notification-actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .notification-actions .accept {
            background-color: #28a745;
            color: #ffffff;
        }

        .notification-actions .reject {
            background-color: #dc3545;
            color: #ffffff;
        }
    
    </style>
</head>
<body>
<div class="wrapper row2">
    <div id="header" class="clear">
        <div class="fl_left">
            <h1><a href="./../index.html">Freelance Marketplace</a></h1>
            <p>Connecting Talent with Opportunity</p>
        </div>
        <div class="fl_right">
            <p><a href="logout.php"><strong>LOGOUT</strong></a></p>
        </div>
    </div>
</div><br><br><br>
<div class="container">
    <div class="header">
        <h1>Welcome <b><?=$query['username']?></b> to</h1> 
        <h1><span>Freelancer Dashboard</span></h1>
    </div>
    

    
    <div class="content">
        <div id="browse-job" class="form-container">
            <h2>Browse Jobs</h2>
            <div class="dropdown">
                <div class="form-group">
                    <label for="job-type">Job Type</label>
                    <select id="job-type" name="job-type">
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                    </select>
                </div>
            </div>
            <button class="btn">Search Jobs</button>
        </div>

        <div id="notifications" class="notification-container">
            <h2>Notifications</h2>
            <?php include("fetch_notifications.php"); ?>
        </div>
    </div>
</div>
<div class="nav">
        <a href="#" onclick="showForm('browse-job')">Browse Job</a>
        <a href="#" onclick="showForm('notifications')">Notifications</a>
    </div>

<script>
    function showForm(formId) {
        var forms = document.getElementsByClassName('form-container');
        for (var i = 0; i < forms.length; i++) {
            forms[i].classList.remove('active');
        }
        document.getElementById(formId).classList.add('active');
    }

    function fetchJobs(jobType) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_jobs.php?job-type=' + jobType, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('job-results').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    function toggleNotifications() {
        var notifications = document.getElementById('notifications');
        notifications.classList.toggle('active');
        if (notifications.classList.contains('active')) {
            fetchNotifications();
        }
    }

    function fetchNotifications() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_notifications.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('notifications').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    function handleNotification(action, id, type) {
        let reason = '';
        if (action === 'reject' && type === 'hire_request') {
            reason = prompt('Please provide a reason for rejection:');
            if (reason === null || reason.trim() === '') {
                return; // Cancel the rejection if no reason is provided
            }
        }

        fetch('handle_notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action, id, type, reason }),
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function showCancelReason(id) {
        handleNotification('reject', id, 'hire_request');
    }
</script>
</body>
</html>
