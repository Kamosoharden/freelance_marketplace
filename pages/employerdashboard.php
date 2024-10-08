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

$notifications_query = "SELECT aj.*, f.name AS freelancer_name, j.job_title AS job_title 
                        FROM applied_jobs aj 
                        JOIN freelancers f ON aj.freelancer_id = f.id 
                        JOIN job_posts j ON aj.job_id = j.id 
                        WHERE aj.id = $employer_id 
                        ORDER BY aj.id DESC";
$notifications_result = mysqli_query($conn, $notifications_query);
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
        .notification-item {
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .start-btn {
            background-color: #32cc32;
            color: #ffffff;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .start-btn:hover {
            background-color: #28a745;
        }
        .modal-content {
            background-color: #2c2c2c;
            color: #ffffff;
            border-radius: 15px;
        }
        .hire-button {
            background-color: #32cc32;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .hire-button:hover {
            background-color: #28a745;
            transform: translateY(-2px);
        }

        .hire-button:active {
            transform: translateY(0);
        }

        .hire-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        /* Style for the modal */
        .modal-content {
            background-color: #2c2c2c;
            color: #ffffff;
        }

        .modal-header, .modal-footer {
            border-color: #4c4c4c;
        }

        .modal-header .close {
            color: #ffffff;
        }

        .modal-body .form-control {
            background-color: #3c3c3c;
            border: 1px solid #4c4c4c;
            color: #ffffff;
        }

        .modal-footer .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .modal-footer .btn-primary {
            background-color: #32cc32;
            border-color: #32cc32;
        }

        .modal-footer .btn-primary:hover {
            background-color: #28a745;
            border-color: #28a745;
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
                    <a class="nav-link" href="#" onclick="showSection('post-job')">Post Job</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showSection('browse-freelancers')">Browse Freelancers</a>
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
        <h1>Welcome <b><?=$query['company']?></b> to</h1> 
        <h1><span>Employer Dashboard</span></h1>
    </div>

    <div class="content-section" id="post-job">
        <h2>Post a Job</h2>
        <form action="post_job_process.php" method="POST">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="job-title">Job Title</label>
                    <input type="text" id="job-title" name="job-title" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="job-type">Job Type</label>
                    <select id="job-type" name="job-type" class="form-control" required>
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="payment-range">Payment Range</label>
                    <input type="text" id="payment-range" name="payment-range" class="form-control" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="project-duration">Project Duration</label>
                    <input type="text" id="project-duration" name="project-duration" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="job-description">Job Description</label>
                <textarea id="job-description" name="job-description" rows="5" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="skills-required">Skills Required</label>
                <input type="text" id="skills-required" name="skills-required" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="job-rules">Rules and Regulations</label>
                <textarea id="job-rules" name="job-rules" rows="5" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Job</button>
        </form>
    </div>

        <div id="browse-freelancers" class="form-container">
            <h2>Browse Freelancers</h2>
            <form id="search-form" method="POST" onsubmit="searchFreelancers(event)">
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
                <button type="submit" class="btn">Search Freelancers</button>
            </form>
            <div id="results"></div>
        </div>
        <div id="notifications" class="form-container">
            <h2>Notifications</h2>
            <ul class="notification-list">
                <?php if (mysqli_num_rows($notifications_result) > 0): ?>
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
                            <a href="payment/index.php"><button class="start-btn" onclick="startAction(<?php echo $notification['id']; ?>)">Start job</button></a>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="notification-item">No new notifications</li>
                <?php endif; ?>
            </ul>
        </div>
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
            <button type="submit" class="btn">Confirm Hire</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function hireFreelancer(freelancerId) {
        console.log('Hiring freelancer with ID:', freelancerId);
        document.getElementById('freelancer_id').value = freelancerId;
        $('#hireModal').modal('show');
    }

    function submitHireForm() {
        var formData = new FormData(document.getElementById('hireForm'));
        
        fetch('hire_freelancer.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                $('#hireModal').modal('hide');
                searchFreelancers(new Event('submit')); // Refresh the search results
            } else if (data.error) {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function showSection(sectionId) {
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
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

    function showPaymentConfirmation(jobId) {
        fetch('payment/index.php?job_id=' + jobId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('payment-confirmation-' + jobId).innerHTML = data;
                document.getElementById('payment-confirmation-' + jobId).style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    }

    function proceedPayment(jobId) {
        // AJAX call to process the payment
        fetch('./payment/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'job_id=' + jobId
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show success or error message
            location.reload(); // Reload the page to update the status
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function cancelPayment(jobId) {
        document.getElementById('payment-confirmation-' + jobId).style.display = 'none';
    }
</script>
</body>
</html>