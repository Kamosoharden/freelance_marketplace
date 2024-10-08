<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skills = isset($_POST['skills']) ? $conn->real_escape_string($_POST['skills']) : '';
    $categories = isset($_POST['categories']) ? $conn->real_escape_string($_POST['categories']) : '';
    $location = isset($_POST['location']) ? $conn->real_escape_string($_POST['location']) : '';
    $degree = isset($_POST['degree']) ? $conn->real_escape_string($_POST['degree']) : '';
    
    // These fields are not used in the search query, so we can remove them or set default values
    // $money = isset($_POST['money']) ? $conn->real_escape_string($_POST['money']) : '';
    // $job_category = isset($_POST['job_category']) ? $conn->real_escape_string($_POST['job_category']) : '';
    // $period_time = isset($_POST['period_time']) ? $conn->real_escape_string($_POST['period_time']) : '';

    $results = [];

    // Modify the query to be more flexible
    $query = "SELECT * FROM freelancers WHERE 1=1";
    
    if (!empty($skills)) {
        $query .= " AND skills LIKE '%$skills%'";
    }
    if (!empty($categories)) {
        $query .= " AND category = '$categories'";
    }
    if (!empty($location)) {
        $query .= " AND location = '$location'";
    }
    if (!empty($degree)) {
        $query .= " AND degree = '$degree'";
    }
    
    $query .= " LIMIT 10";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }

    if (count($results) > 0) {
        foreach ($results as $freelancer) {
            $freelancer_id = $freelancer['id'];
            $hire_query = "SELECT * FROM hires WHERE freelancer_id='$freelancer_id' AND status='pending'";
            $hire_result = $conn->query($hire_query);
            $is_hired = $hire_result && $hire_result->num_rows > 0;

            echo "<div class='freelancer-card'>";
            echo "<div class='freelancer-info'>";
            echo "<p class='freelancer-name'>Name: " . htmlspecialchars($freelancer['name']) . "</p>";
            echo "<p class='freelancer-skills'>Skills: " . htmlspecialchars($freelancer['skills']) . "</p>";
            echo "<p class='freelancer-category'>Category: " . htmlspecialchars($freelancer['category']) . "</p>";
            echo "<p class='freelancer-location'>Location: " . htmlspecialchars($freelancer['location']) . "</p>";
            echo "<p class='freelancer-degree'>Degree: " . htmlspecialchars($freelancer['degree']) . "</p>";
            echo "</div>";
            if ($is_hired) {
                echo "<button class='hire-button' disabled>Hired</button>";
            } else {
                echo "<button class='hire-button' onclick='hireFreelancer(" . $freelancer['id'] . ")'>Hire</button>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No matches found.</p>";
    }

    $conn->close();
}
?>