<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SESSION['employer_user_email']) {
    $email = $_SESSION['employer_user_email'];
    $query = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employers WHERE email='$email'"));
    $employer_id = $_SESSION['user_id']; // Assuming you store user_id in the session
} else {
    header("Location: employerlogin.html");
    exit();
}
$employer_id = $_SESSION['user_id'];

// Add this near the top of the file, after session_start()
if (isset($_GET['payment'])) {
    $paymentStatus = $_GET['payment'];
    if ($paymentStatus == 'success') {
        echo "<script>alert('Payment successful! The freelancer has been notified.');</script>";
    } elseif ($paymentStatus == 'cancelled') {
        echo "<script>alert('Payment was cancelled.');</script>";
    } elseif ($paymentStatus == 'fraud') {
        echo "<script>alert('Potential fraudulent transaction detected.');</script>";
    } elseif ($paymentStatus == 'failed') {
        echo "<script>alert('Payment failed. Please try again.');</script>";
    }
}

// Update the notifications query to exclude paid hires
// $notifications_query = "SELECT h.id as hire_id, h.freelancer_id, h.status, h.money, h.job_category, h.period_time, f.name as freelancer_name, j.job_title 
//                         FROM hires h 
//                         JOIN freelancers f ON h.freelancer_id = f.id 
//                         LEFT JOIN job_posts j ON h.job_category = j.job_type 
//                         WHERE h.employer_id = $employer_id AND h.status != 'paid'";
$notifications_query = "SELECT aj.*, f.name AS freelancer_name, j.job_title AS job_title 
                        FROM applied_jobs aj 
                        JOIN freelancers f ON aj.freelancer_id = f.id 
                        JOIN job_posts j ON aj.job_id = j.id 
                        WHERE aj.employer_id = $employer_id 
                        ORDER BY aj.id DESC";
$notifications_result = mysqli_query($conn, $notifications_query);

// Fetch accepted hires with freelancer names
$accepted_hires_query = "
    SELECT hires.id, hires.money, hires.job_category, hires.period_time, hires.hired_at, freelancers.name AS freelancer_name 
    FROM hires 
    JOIN freelancers ON hires.freelancer_id = freelancers.id
    WHERE hires.status = 'accepted' AND hires.employer_id = '$employer_id'
";

$accepted_hires_result = mysqli_query($conn, $accepted_hires_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Employer Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/responsive.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1c1c1c;
            color: #ffffff;
            padding-top: 56px; /* Adjust this value based on your navbar height */
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
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
            margin-top: 50px;
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
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            color: #ffffff;
            display: block;
            margin-bottom: 5px;
        }
        .form-control, input[type="text"], input[type="email"], input[type="password"], textarea, select {
            width: 100%;
            padding: 10px;
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            border-radius: 5px;
            color: #ffffff;
        }
        .btn, button[type="submit"] {
            padding: 10px 20px;
            background-color: #32cc32;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover, button[type="submit"]:hover {
            background-color: #28a745;
        }
        .notification-item {
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .notification-title {
            color: #32cc32;
        }
        .notification-meta {
            color: #cccccc;
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
        #results {
            margin-top: 20px;
        }

        .freelancer-item {
            background-color: #2c2c2c;
            border: 1px solid #3c3c3c;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .freelancer-item p {
            margin: 5px 0;
        }

        .hire-btn, .hired-btn {
            background-color: #32cc32;
            color: #ffffff;
            border: none;
            padding: 5px 15px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hire-btn:hover {
            background-color: #28a745;
        }

        .hired-btn {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .no-results {
            color: #ffffff;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="./../index.html">Freelance Marketplace</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showForm('post-job')">Post Job</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showForm('browse-freelancers')">Browse Freelancers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showForm('notifications')">Notifications</a>
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
        <h1>Welcome <span><b><?=$query['company']?></b></span></h1> 
    </div>
    
    <div class="content-section" id="post-job">
        <h2>Post a Job</h2>
        <form action="post_job_process.php" method="POST">
            <div class="form-group">
                <label for="job-title">Job Title</label>
                <input type="text" id="job-title" name="job-title" required>
            </div>
            <div class="form-group">
                <label for="job-type">Job Type</label>
                <select id="job-type" name="job-type" required>
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                </select>
            </div>
            <div class="form-group">
                <label for="payment-range">Payment</label>
                <input type="text" id="payment-range" name="payment-range" required>
            </div>
            <div class="form-group">
                <label for="job-description">Job Description</label>
                <textarea id="job-description" name="job-description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="project-duration">Project Duration</label>
                <input type="text" id="project-duration" name="project-duration" required>
            </div>
            <div class="form-group">
                <label for="skills-required">Skills Required</label>
                <input type="text" id="skills-required" name="skills-required" required>
            </div>
            <div class="form-group">
                <label for="job-rules">Rules and regularions</label>
                <textarea id="job-rules" name="job-rules" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Job</button>
        </form>
    </div>

    <div class="content-section" id="browse-freelancers" style="display: none;">
        <h2>Browse Freelancers</h2>
        <form onsubmit="searchFreelancers(event)">
            <div class="dropdown">
                <div class="form-group">
                    <label for="skills">Skills</label>
                    <input type="text" id="skills" name="skills" required>
                </div>
                <div class="form-group">
                    <label for="categories">Categories</label>
                    <select id="categories" name="categories">
                        <option value="it">IT & Programming</option>
                        <option value="design">Design & Multimedia</option>
                        <option value="writing">Writing & Translation</option>
                        <option value="business">Business & Consulting</option>
                        <option value="agriculture">Agriculture & Environment</option>
                        <option value="education">Education & Training</option>
                        <option value="health">Health & Wellness</option>
                        <option value="local_services">Local Services</option>
                        <option value="social_impact">Social Impact</option>
                        <option value="technology">Technology & Innovation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <select id="location" name="location">
                        <option value="kigali">Kigali City</option>
                        <option value="north">Northern Province</option>
                        <option value="south">Southern Province</option>
                        <option value="east">Eastern Province</option>
                        <option value="west">Western Province</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="degree">Degree</label>
                    <select id="degree" name="degree">
                        <option value="highschool">Highschool Graduate</option>
                        <option value="bachelors">Bachelors</option>
                        <option value="masters">Masters</option>
                        <option value="phd">PhD</option>
                        <option value="none">None of the above</option>
                    </select>
                </div>
                <div class="form-group" style="display: none;">
                    <label for="money">Money</label>
                    <input type="text" id="money" name="money">
                </div>
                <div class="form-group"  style="display: none;">
                    <label for="job_category">Job Category</label>
                    <input type="text" id="job_category" name="job_category">
                </div>
                <div class="form-group" style="display: none;">
                    <label for="period_time">Period Time</label>
                    <input type="text" id="period_time" name="period_time">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Search Freelancers</button>
        </form>
        <div id="results"></div>
    </div>

    <div class="content-section" id="notifications" style="display: none;">
        <h2>Notifications</h2>
        <ul class="notification-list">
            <?php if (mysqli_num_rows($accepted_hires_result) > 0): ?>
            <li class="notification-header">Accepted Hires</li>
            <?php while ($hire = mysqli_fetch_assoc($accepted_hires_result)): ?>
                <li class="notification-item">
                    <div class="notification-content">
                        <div class="notification-title">
                            Hire Offer from <?php echo htmlspecialchars($hire['freelancer_name']); ?>
                        </div>
                        <div class="notification-meta">
                            <p><strong>Payment:</strong> $<?php echo htmlspecialchars($hire['money']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($hire['job_category']); ?></p>
                            <p><strong>Period:</strong> <?php echo htmlspecialchars($hire['period_time']); ?></p>
                            <p><strong>Offered at:</strong> <?php echo date('F j, Y', strtotime($hire['hired_at'])); ?></p>
                        </div>
                    </div>
                    <a href="payment/index.php"><button class="start-btn" onclick="startAction(<?php echo $hire['id']; ?>)">Start Job</button></a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="notification-item">No accepted hires</li>
        <?php endif; ?>

        <!-- Display Notifications -->
        <?php if (mysqli_num_rows($notifications_result) > 0): ?>
            <li class="notification-header">New Applications</li>
            <?php while($notification = mysqli_fetch_assoc($notifications_result)): ?>
                <li class="notification-item">
                    <div class="notification-content">
                        <div class="notification-title">
                            <?php echo htmlspecialchars($notification['freelancer_name']); ?> applied for "<?php echo htmlspecialchars($notification['job_title']); ?>"
                        </div>
                        <div class="notification-meta">
                            Applied on: <?php echo date('F j, Y', strtotime($notification['apply_date'])); ?>
                        </div>
                    </div>
                    <a href="payment/index.php"><button class="start-btn" onclick="startAction(<?php echo $notification['id']; ?>)">Start Job</button></a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="notification-item">No new notifications</li>
        <?php endif; ?>
        </ul>
    </div>
</div>

<div id="hire-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Hire Freelancer</h2>
        <form id="hire-form">
            <div class="form-group">
                <label for="money">Payment Amount</label>
                <input type="text" id="money" name="money" required>
            </div>
            <div class="form-group">
                <label for="job_category">Job Category</label>
                <select id="job_category" name="job_category" required>
                    <option value="it">IT & Programming</option>
                    <option value="design">Design & Multimedia</option>
                    <option value="writing">Writing & Translation</option>
                    <option value="business">Business & Consulting</option>
                    <option value="agriculture">Agriculture & Environment</option>
                    <option value="education">Education & Training</option>
                    <option value="health">Health & Wellness</option>
                    <option value="local_services">Local Services</option>
                    <option value="social_impact">Social Impact</option>
                    <option value="technology">Technology & Innovation</option>
                </select>
            </div>
            <div class="form-group">
                <label for="period_time">Period of Time</label>
                <input type="text" id="period_time" name="period_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Hire</button>
        </form>
    </div>
</div>

<script>
    function showForm(formId) {
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(formId).style.display = 'block';

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    function hireFreelancer(freelancerId) {
        // Show the modal
        var modal = document.getElementById("hire-modal");
        var span = document.getElementsByClassName("close")[0];

        // Open the modal
        modal.style.display = "block";

        // Close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Handle form submission
        document.getElementById("hire-form").onsubmit = function(event) {
            event.preventDefault();
            
            var formData = new FormData(this);
            formData.append("freelancer_id", freelancerId);

            fetch('hire_freelancer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                modal.style.display = "none";
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
    }

    function searchFreelancers(event) {
        event.preventDefault();
        var formData = new FormData(event.target);
        fetch('search_freelancers.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('results').innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
</body>
</html>