<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $skills = $_POST['skills'];
    $categories = $_POST['categories'];
    $location = $_POST['location'];
    $degree = $_POST['degree'];
    $money = $_POST['money']; // Money field
    $job_category = $_POST['job_category']; // Job category field
    $period_time = $_POST['period_time']; // Period time field

    $results = [];

    // Modify queries to include additional criteria
    $query = "SELECT * FROM freelancers WHERE skills LIKE '%$skills%' AND category='$categories' AND location='$location' AND degree='$degree' LIMIT 10";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        $query = "SELECT * FROM freelancers WHERE category='$categories' AND location='$location' AND degree='$degree' LIMIT 10";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            $query = "SELECT * FROM freelancers WHERE category='$categories' AND location='$location' LIMIT 10";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
            }
        }
    }

    if (count($results) > 0) {
        foreach ($results as $freelancer) {
            $freelancer_id = $freelancer['id'];
            $hire_query = "SELECT * FROM hires WHERE freelancer_id='$freelancer_id' AND status='pending'";
            $hire_result = $conn->query($hire_query);
            $is_hired = $hire_result->num_rows > 0;

            echo "<div class='freelancer-item'>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($freelancer['name']) . "</p>";
            echo "<p><strong>Skills:</strong> " . htmlspecialchars($freelancer['skills']) . "</p>";
            echo "<p><strong>Category:</strong> " . htmlspecialchars($freelancer['category']) . "</p>";
            echo "<p><strong>Location:</strong> " . htmlspecialchars($freelancer['location']) . "</p>";
            echo "<p><strong>Degree:</strong> " . htmlspecialchars($freelancer['degree']) . "</p>";
            if ($is_hired) {
                echo "<button class='hired-btn' disabled>Hired</button>";
            } else {
                echo "<button class='hire-btn' onclick='hireFreelancer($freelancer_id)'>Hire</button>";
            }
            echo "</div>";
        }
    } else {
        echo "<p class='no-results'>No matches found.</p>";
    }

    $conn->close();
}
?>
