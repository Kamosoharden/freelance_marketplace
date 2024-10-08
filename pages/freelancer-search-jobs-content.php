<?php
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

$freelancer_id = 1; 

$jobs = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['job_type']) && isset($_GET['skills_required'])) {
    $job_type = $_GET['job_type'];
    $skills_required = $_GET['skills_required'];

    $sql = "SELECT jp.id, jp.job_title, jp.job_description, jp.payment_range, jp.project_duration, jp.skills_required, aj.freelancer_id AS applied
            FROM job_posts jp
            LEFT JOIN applied_jobs aj ON jp.id = aj.job_id AND aj.freelancer_id = '$freelancer_id'
            WHERE jp.job_type LIKE '%$job_type%' AND jp.skills_required LIKE '%$skills_required%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job_id'])) {
    $job_id = intval($_POST['apply_job_id']);

    // Fetch the employer_id for the job
    $job_query = "SELECT employer_id FROM job_posts WHERE id = '$job_id'";
    $job_result = mysqli_query($conn, $job_query);
    $job_data = mysqli_fetch_assoc($job_result);
    $employer_id = $job_data['employer_id'];

    $check_query = "SELECT * FROM applied_jobs WHERE freelancer_id='$freelancer_id' AND job_id='$job_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO applied_jobs (job_id, freelancer_id, employer_id, status) VALUES ('$job_id', '$freelancer_id', '$employer_id', 'Pending')";
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode(['status' => 'success', 'message' => 'You have successfully applied for the job.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error applying for the job: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'You have already applied for this job.']);
    }
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Jobs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .search-jobs-header {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: center;
        }
        .search-jobs-header h1 {
            margin: 0;
        }
        .search-form {
            margin-top: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .job-feed {
            margin-top: 20px;
        }
        .job-item {
            background-color: #ffffff;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
        }
        .job-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .job-item h3 {
            color: #2980b9;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 22px;
        }
        .job-description {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .job-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #7f8c8d;
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
        .btn-apply {
            padding: 8px 15px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }
        .btn-apply:hover {
            background-color: #2ecc71;
        }
        .btn-applied {
            padding: 8px 15px;
            background-color: #95a5a6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: not-allowed;
            font-weight: bold;
            font-size: 14px;
        }
        .no-jobs {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="search-jobs-header">
    <h1>Search Jobs</h1>
    <p>Find jobs that match your skills and preferences.</p>
</div>

<div class="container">
    <div class="search-form">
        <form id="search-jobs-form" method="GET" action="">
            <div class="form-group">
                <label for="job-type">Job Type</label>
                <select id="job-type" name="job_type" class="form-control">
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                </select>
            </div>
            <div class="form-group">
                <label for="skills-required">Skills Required</label>
                <input type="text" id="skills-required" name="skills_required" class="form-control" placeholder="Enter skills...">
            </div>
            <button type="submit" class="btn btn-primary">Search Jobs</button>
        </form>
    </div>

    <div class="job-feed">
        <h2>Search Results</h2>
        <div id="job-results">
            <?php if (count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="job-item">
                        <h3><?= htmlspecialchars($job['job_title']) ?></h3>
                        <p class="job-description"><?= htmlspecialchars($job['job_description']) ?></p>
                        <div class="job-details">
                            <p><strong>Payment range:</strong> <?= htmlspecialchars($job['payment_range']) ?></p>
                            <p><strong>Duration:</strong> <?= htmlspecialchars($job['project_duration']) ?></p>
                        </div>
                        <p><strong>Skills required:</strong> <?= htmlspecialchars($job['skills_required']) ?></p>
                        <div class="job-actions">
                            <?php if ($job['applied']): ?>
                                <button class="btn-applied" disabled>Applied</button>
                            <?php else: ?>
                                <button class="btn-apply" data-job-id="<?= $job['id'] ?>">Apply</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-jobs">No jobs found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('search-jobs-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const jobType = document.getElementById('job-type').value;
            const skillsRequired = document.getElementById('skills-required').value;

            fetch(`freelancer-search-jobs-content.php?job_type=${jobType}&skills_required=${skillsRequired}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('job-results').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching jobs:', error);
                    document.getElementById('job-results').innerHTML = '<p class="no-jobs">Error fetching jobs.</p>';
                });
        });

        document.querySelectorAll('.btn-apply').forEach(button => {
            button.addEventListener('click', function() {
                const jobId = this.getAttribute('data-job-id');
                fetch('freelancer-search-jobs-content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `apply_job_id=${jobId}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        this.classList.remove('btn-apply');
                        this.classList.add('btn-applied');
                        this.textContent = 'Applied';
                        this.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error applying for the job:', error);
                    alert('Error applying for the job.');
                });
            });
        });
    });
</script>

</body>
</html>