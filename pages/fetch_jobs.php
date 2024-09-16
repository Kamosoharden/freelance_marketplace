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

// Check and display jobs
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='job-item'>";
        echo "<h3>" . htmlspecialchars($row['job_title']) . "</h3>";
        echo "<p><strong>Job Type:</strong> " . htmlspecialchars($row['job_type']) . "</p>";
        echo "<p><strong>Payment Range:</strong> " . htmlspecialchars($row['payment_range']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['job_description']) . "</p>";
        echo "<p><strong>Duration:</strong> " . htmlspecialchars($row['project_duration']) . "</p>";
        echo "<p><strong>Skills Required:</strong> " . htmlspecialchars($row['skills_required']) . "</p>";
        echo "<p><strong>Rules:</strong> " . htmlspecialchars($row['job_rules']) . "</p>";
        echo "</div>";
    }
} else {
    echo "No jobs found matching your criteria.";
}

// Close connection
$conn->close();
?>
