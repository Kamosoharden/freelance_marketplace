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

// Get filters from URL parameters
$jobType = isset($_GET['job-type']) ? $_GET['job-type'] : '';
$skillsRequired = isset($_GET['skills-required']) ? $_GET['skills-required'] : '';

// Prepare SQL query with filters
$query = "SELECT * FROM job_posts WHERE 1=1";

if ($jobType) {
    $query .= " AND job_type = '$jobType'";
}

if ($skillsRequired) {
    $query .= " AND skills_required LIKE '%$skillsRequired%'";
}

// Execute the query
$result = $conn->query($query);

// Check if the freelancer is logged in
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];

    // Fetch freelancer's ID
    $freelancer_id_query = mysqli_query($conn, "SELECT id FROM freelancers WHERE email='$email'");
    $freelancer = mysqli_fetch_assoc($freelancer_id_query);
    $freelancer_id = $freelancer['id'];

    // Display jobs and apply button
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $job_id = $row['id'];

            // Check if the freelancer has already applied for the job
            $applied_query = mysqli_query($conn, "SELECT * FROM applied_jobs WHERE freelancer_id='$freelancer_id' AND job_id='$job_id'");
            $has_applied = mysqli_num_rows($applied_query) > 0;

            echo "<div class='job-item'>";
            echo "<h3>" . htmlspecialchars($row['job_title']) . "</h3>";
            echo "<p><strong>Job Type:</strong> " . htmlspecialchars($row['job_type']) . "</p>";
            echo "<p><strong>Payment Range:</strong> " . htmlspecialchars($row['payment_range']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['job_description']) . "</p>";
            echo "<p><strong>Duration:</strong> " . htmlspecialchars($row['project_duration']) . "</p>";
            echo "<p><strong>Skills Required:</strong> " . htmlspecialchars($row['skills_required']) . "</p>";
            echo "<p><strong>Rules:</strong> " . htmlspecialchars($row['job_rules']) . "</p>";

            // Show Apply button if not applied yet
            if (!$has_applied) {
                echo '<button onclick="showJobRules(' . $row['id'] . ')">Apply</button>';
            } else {
                echo '<button disabled>You have already applied</button>';
            }

            echo "</div>";
        }
    } else {
        echo "No jobs found matching your criteria.";
    }
} else {
    echo "You must be logged in to view and apply for jobs.";
}

// Handle the job application process
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
        echo "<p>You have already applied for this job.</p>";
    }
}

// Close connection
$conn->close();
?>

<!-- HTML for showing job rules and accepting the terms -->
<div id="job-rules-modal" style="display:none;">
    <h3>Job Rules</h3>
    <div id="job-rules-content"></div>
    <form method="post" id="apply-job-form">
        <input type="hidden" id="apply_job_id" name="apply_job_id">
        <label>
            <input type="checkbox" id="accept-rules-checkbox" required>
            I accept the job rules
        </label>
        <button type="submit" id="apply-confirm-button">Confirm and Apply</button>
    </form>
</div>

<script>
    // JavaScript to handle showing job rules and submitting the form
    function showJobRules(job_id) {
        // Fetch job rules (if needed) and show the modal
        document.getElementById('apply_job_id').value = job_id;
        document.getElementById('job-rules-modal').style.display = 'block';
    }

    // Handling form submission
    document.getElementById('apply-job-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Ensure that the checkbox is checked
        if (document.getElementById('accept-rules-checkbox').checked) {
            // Submit the form programmatically via POST request
            this.submit(); // Submits the form
        } else {
            alert('You must accept the job rules to apply.');
        }
    });
</script>
