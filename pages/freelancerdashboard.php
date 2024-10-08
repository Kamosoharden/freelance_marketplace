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
    $employer_query = "SELECT * FROM job_posts WHERE id='$job_id'";
    $employer = mysqli_fetch_assoc(mysqli_query($conn, $employer_query))['employer_id'];

    if (mysqli_num_rows($check_result) == 0) {
        // Insert into applied_jobs table
        $insert_query = "INSERT INTO applied_jobs (job_id, freelancer_id, employer_id) VALUES ('$job_id', '$freelancer_id', '$employer')";
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
            $job_results .= "<button class='btn-apply' onclick='showJobRules(" . $row['id'] . ", `" . htmlspecialchars($row['job_rules']) . "`)'>Apply</button>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Freelancer Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/responsive.css" rel="stylesheet" />
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
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .nav-link:hover {
            color: #32cc32 !important;
        }
        .container {
            margin-top: 20px;
        }
        .dashboard-header {
            background-color: #2c2c2c;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            color: #ffffff;
            font-weight: bold;
        }
        .dashboard-header h1 span {
            color: #32cc32;
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
        .job-item {
            background-color: #3c3c3c;
            margin-bottom: 20px;
            border: 1px solid #4c4c4c;
            border-radius: 5px;
            padding: 15px;
        }
        .job-item h3 {
            margin-top: 0;
            color: #32cc32;
        }
        .job-description {
            margin-bottom: 10px;
        }
        .job-details {
            font-size: 0.9em;
            color: #cccccc;
        }
        .job-actions {
            margin-top: 15px;
        }
        .no-jobs {
            text-align: center;
            color: #cccccc;
            font-style: italic;
        }
        .notification-item {
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .notification-actions {
            margin-top: 10px;
        }
        .modal-content {
            background-color: #2c2c2c;
            color: #ffffff;
        }
        .modal-header, .modal-footer {
            border-color: #4c4c4c;
        }
        .close {
            color: #ffffff;
        }
        .form-check-label {
            color: #ffffff;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #2c2c2c;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #4c4c4c;
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
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="./../index.html">Freelance Marketplace</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showSection('browse-job')">Browse Jobs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showSection('notifications')">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="dashboard-header">
        <h1>Welcome <b><?=$query['username']?></b> to</h1> 
        <h1><span>Freelancer Dashboard</span></h1>
    </div>

    <div class="content-section" id="browse-job">
        <h2>Browse Jobs</h2>
        <form onsubmit="searchJobs(event)">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="job-type">Job Type</label>
                    <select id="job-type" name="job-type" class="form-control">
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="skills-required">Skills Required</label>
                    <input type="text" id="skills-required" name="skills-required" class="form-control" placeholder="Enter skills...">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Search Jobs</button>
        </form>
        <div id="job-results" class="mt-4">
            <?=$job_results?>
        </div>
    </div>

    <div class="content-section" id="notifications" style="display: none;">
        <h2>Notifications</h2>
        <?php
        // Fetch job notifications
        $notifications = mysqli_query($conn, "SELECT * FROM job_offers WHERE freelancer_email='$email' AND status = 'Pending'");
        if (mysqli_num_rows($notifications) > 0) {
            while ($notification = mysqli_fetch_assoc($notifications)) {
                echo "<div class='notification-item'>";
                echo "<p><strong>Job Offer:</strong> " . htmlspecialchars($notification['job_title']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($notification['job_description']) . "</p>";
                echo "<div class='notification-actions'>";
                echo "<form method='post'><input type='hidden' name='job_id' value='" . $notification['job_id'] . "'>";
                echo "<button type='submit' name='action' value='accept' class='btn btn-success'>Accept</button> ";
                echo "<button type='submit' name='action' value='reject' class='btn btn-danger'>Reject</button>";
                echo "</form>";
                echo "</div></div>";
            }
        } else {
            echo "<p>No notifications available.</p>";
        }
        ?>
    </div>
</div>

<div id="jobRulesModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Job Rules</h2>
        <p id="jobRulesContent"></p>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="rulesCheckbox">
            <label class="form-check-label" for="rulesCheckbox">I accept the job rules</label>
        </div>
        <br>
        <button id="confirmApply" class="btn btn-primary" disabled>Confirm Application</button>
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
        checkbox.checked = false;
        confirmButton.disabled = true;
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

    function searchJobs(event) {
        event.preventDefault();
        var jobType = document.getElementById("job-type").value;
        var skillsRequired = document.getElementById("skills-required").value;
        window.location.href = "freelancerdashboard.php?job-type=" + jobType + "&skills-required=" + skillsRequired;
    }

    function applyForJob(jobId) {
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

    function showSection(sectionId) {
        // Hide all sections
        document.getElementById('browse-job').style.display = 'none';
        document.getElementById('notifications').style.display = 'none';

        document.getElementById(sectionId).style.display = 'block';

        // Update active state for buttons
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    // Show Browse Job section by default
    document.addEventListener('DOMContentLoaded', function() {
        showSection('browse-job');
    });
</script>

</body>
</html>