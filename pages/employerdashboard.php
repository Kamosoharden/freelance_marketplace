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
                        WHERE aj.employer_id = $employer_id 
                        ORDER BY aj.id DESC";
$notifications_result = mysqli_query($conn, $notifications_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
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

        .nav a {
            text-decoration: none;
            color: #000000;
            padding: 10px 20px;
            background-color: #e9ecef;  
            border-radius: 15px;
            margin-right: 20px;
            margin-left: 10px;
            margin-top: 20px;
            font-weight: bold;
        }

        .nav a:hover {
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
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        #notifications {
            display: none;
        }

        .notification-list {
            list-style-type: none;
            padding: 0;
        }

        .notification-item:hover {
            background-color: #e9ecef;
        }

        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .notification-meta {
            color: #6c757d;
            font-size: 0.9em;
        }
        .notification-item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 15px;
            transition: background-color 0.3s;
        }

        .notification-content {
            flex-grow: 1;
        }

        .start-btn {
            padding: 5px 15px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .start-btn:hover {
            background-color: #0056b3;
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
            <p><a href="logout.php"> <strong>LOGOUT</strong></a></p>
        </div>
    </div>
</div><br><br><br>

<div class="container">
    <div class="header">
        <h1>Welcome <b><?=$query['company']?></b> to</h1> 
        <h1><span>Employer Dashboard</span></h1>
    </div>
    
    <div class="nav">
        <a href="#" onclick="showForm('post-job')">Post Job</a>
        <a href="#" onclick="showForm('browse-freelancers')">Browse Freelancers</a>
        <a href="#" onclick="showForm('notifications')">Notifications</a>
    </div>
    
    <div class="content">
        <div id="post-job" class="form-container">
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
                    <label for="payment-range">Payment Range</label>
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
                <button type="submit" class="btn">Post Job</button>
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
                            <a href="payment.php"><button class="start-btn" onclick="startAction(<?php echo $notification['id']; ?>)">Start job</button></a>
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

<script>
    // JavaScript to handle modal and form submission
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


    function showForm(formId) {
        document.querySelectorAll('.form-container').forEach(container => {
            container.classList.remove('active');
            container.style.display = 'none';
        });
        document.getElementById(formId).classList.add('active');
        document.getElementById(formId).style.display = 'flex';
    }

    function searchFreelancers(event) {
        event.preventDefault();
        // Add logic to handle searching freelancers and display results
        // Example:
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
