<?php
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'freelancer') {
    die("Unauthorized access");
}

$freelancer_id = $_SESSION['user_id'];

// Fetch freelancer profile
$sql = "SELECT * FROM freelancers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
$freelancer = $result->fetch_assoc();

if (!$freelancer) {
    die("Freelancer profile not found");
}
?>

<h2>My Profile</h2>
<div class="profile-container">
    <div class="profile-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($freelancer['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($freelancer['email']) ?></p>
        <p><strong>Skills:</strong> <?= htmlspecialchars($freelancer['skills']) ?></p>
        <p><strong>Experience:</strong> <?= htmlspecialchars($freelancer['experience']) ?></p>
    </div>
    <div class="profile-actions">
        <button class="btn btn-primary" onclick="editProfile()">Edit Profile</button>
    </div>
</div>

<script>
function editProfile() {
    // Implement edit profile functionality
    alert('Edit profile functionality to be implemented');
}
</script>