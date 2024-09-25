<?php
session_start();
$servername = "localhost";
$username = "root"; // Adjust with your actual DB username
$password = "";     // Adjust with your actual DB password
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the freelancer is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: freelancerlogin.html");
    exit();
}

$email = $_SESSION['user_email'];

$query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM freelancers WHERE email='$email'"));
$freelancer_id = $query['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job_id'])) {
    $job_id = intval($_POST['apply_job_id']);

    // Check if the freelancer has already applied for the job
    $check_query = "SELECT * FROM applied_jobs WHERE freelancer_id='$freelancer_id' AND job_id='$job_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Insert into applied_jobs table
        $insert_query = "INSERT INTO applied_jobs (job_id, freelancer_id) VALUES ('$job_id', '$freelancer_id')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<p>You have successfully applied for the job.</p>";
        } else {
            echo "<p>Error applying for the job: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<script>alert('You have already applied for this job.')</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id']) && isset($_POST['action'])) {
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

$job_results = "";
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['job-type']) && isset($_GET['skills-required'])) {
    $job_type = $_GET['job-type'];
    $skills_required = $_GET['skills-required'];

    $search_query = "SELECT * FROM job_posts WHERE job_type LIKE '%$job_type%' AND skills_required LIKE '%$skills_required%'";
    $search_result = mysqli_query($conn, $search_query);

    if (mysqli_num_rows($search_result) > 0) {
        while ($row = mysqli_fetch_assoc($search_result)) {
            $job_results .= "<div class='job-item'>";
            $job_results .= "<h3>" . htmlspecialchars($row['job_title']) . "</h3>";
            $job_results .= "<p class='job-description'>" . htmlspecialchars($row['job_description']) . "</p>";
            $job_results .= "<div class='job-details'>";
            $job_results .= "<p><strong>Payment range:</strong> " . htmlspecialchars($row['payment_range']) . "</p>";
            $job_results .= "<p><strong>Duration:</strong> " . htmlspecialchars($row['project_duration']) . "</p>";
            $job_results .= "</div>";
            $job_results .= "<p><strong>Skills required:</strong> " . htmlspecialchars($row['skills_required']) . "</p>";
            $job_results .= "<div class='job-actions'>";
            // $job_results .= "<button class='btn-apply' onclick='applyForJob(" . $row['id'] . ")'>Apply</button>";
            $job_results .= "<button class='btn-rules' onclick='showJobRules(" . $row['id'] . ", `" . htmlspecialchars($row['job_rules']) . "`)'>Apply</button>";
            $job_results .= "</div>";
            $job_results .= "</div>";
        }
    } else {
        $job_results = "<p class='no-jobs'>No jobs found.</p>";
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
        #job-results {
            margin-top: 30px;
        }

        .job-item {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
            text-align: left;
        }

        .job-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .job-item h3 {
            color: #32cc32;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .job-description {
            margin-bottom: 15px;
            color: #333;
        }

        .job-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }

        .job-item p {
            margin: 5px 0;
        }

        .job-actions {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-apply, .btn-rules {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }

        .btn-apply {
            background-color: #32cc32;
            color: white;
        }

        .btn-apply:hover {
            background-color: #28a745;
        }

        .btn-rules {
            background-color: #007bff;
            color: white;
        }

        .btn-rules:hover {
            background-color: #0056b3;
        }

        .no-jobs {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-top: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        #rulesCheckbox {
            margin-right: 10px;
        }

        #confirmApply {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #32cc32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        #confirmApply:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Welcome <b><?=$query['username']?></b> to</h1> 
        <h1><span>Freelancer Dashboard</span></h1>
    </div>

    <div class="content">
        <!-- Job Search Section -->
        <div id="browse-job" class="form-container active">
            <h2>Browse Jobs</h2>
            <div class="dropdown">
                <div class="form-group">
                    <label for="job-type">Job Type</label>
                    <select id="job-type" name="job-type">
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="skills-required">Skills Required</label>
                    <input type="text" id="skills-required" name="skills-required" placeholder="Enter skills...">
                </div>
            </div>
            <button class="btn" onclick="searchJobs()">Search Jobs</button>
            
            <div id="job-results">
                <?=$job_results?>
            </div>
        </div>

        <!-- Notifications Section -->
        <div id="notifications" class="notification-container">
            <h2>Notifications</h2>
            <?php
            // Fetch job notifications
            $notifications = mysqli_query($conn, "SELECT * FROM job_offers WHERE freelancer_email='$email'");
            if (mysqli_num_rows($notifications) > 0) {
                while ($notification = mysqli_fetch_assoc($notifications)) {
                    echo "<div class='notification-item'>";
                    echo "<p><strong>Job Offer:</strong> " . htmlspecialchars($notification['job_title']) . "</p>";
                    echo "<p><strong>Description:</strong> " . htmlspecialchars($notification['job_description']) . "</p>";
                    echo "<div class='notification-actions'>";
                    echo "<form method='post'><input type='hidden' name='job_id' value='" . $notification['job_id'] . "'>";
                    echo "<button type='submit' name='action' value='accept' class='accept'>Accept</button>";
                    echo "<button type='submit' name='action' value='reject' class='reject'>Reject</button>";
                    echo "</form>";
                    echo "</div></div>";
                }
            } else {
                echo "<p>No notifications available.</p>";
            }
            ?>
        </div>
    </div>
</div>

<div class="nav">
    <a href="#" onclick="showForm('browse-job')">Browse Job</a>
    <a href="#" onclick="showForm('notifications')">Notifications</a>
</div>

<div id="jobRulesModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Job Rules</h2>
        <p id="jobRulesContent"></p>
        <label>
            <input type="checkbox" id="rulesCheckbox">
            I accept the job rules
        </label>
        <br>
        <button id="confirmApply" disabled>Confirm Application</button>
    </div>
</div>

<script>
    var modal = document.getElementById("jobRulesModal");
    var span = document.getElementsByClassName("close")[0];
    var checkbox = document.getElementById("rulesCheckbox");
    var confirmButton = document.getElementById("confirmApply");
    var currentJobId;

    function showJobRules(jobId, jobRules) {
        currentJobId = jobId;
        document.getElementById("jobRulesContent").textContent = jobRules;
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    checkbox.onchange = function() {
        confirmButton.disabled = !checkbox.checked;
    }

    confirmButton.onclick = function() {
        if (checkbox.checked) {
            applyForJob(currentJobId);
            modal.style.display = "none";
        }
    }
    function searchJobs() {
        var jobType = document.getElementById("job-type").value;
        var skillsRequired = document.getElementById("skills-required").value;
        window.location.href = "freelancerdashboard.php?job-type=" + jobType + "&skills-required=" + skillsRequired;
    }
    function applyForJob(jobId) {
        // Create a form and submit it
        var form = document.createElement('form');
        form.method = 'post';
        form.action = window.location.href;

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'apply_job_id';
        input.value = jobId;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>

</body>
</html>
