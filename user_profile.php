<?php
session_start();
include 'db.php'; // Database connection

// Assuming user_id is stored in session when user logs in
$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT username, email, department, base_location, role FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user skills
$sql_skills = "SELECT skill_name FROM Skills s
               JOIN UserSkills us ON s.id = us.skill_id
               WHERE us.user_id = ?";
$stmt_skills = $conn->prepare($sql_skills);
$stmt_skills->bind_param("i", $user_id);
$stmt_skills->execute();
$result_skills = $stmt_skills->get_result();
$skills = [];
while ($row = $result_skills->fetch_assoc()) {
    $skills[] = $row['skill_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('inc/navbar.php'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-user"></i> User Profile</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Username</h5>
                            <p><?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Email</h5>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Department</h5>
                            <p><?php echo htmlspecialchars($user['department']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Base Location</h5>
                            <p><?php echo htmlspecialchars($user['base_location']); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Role</h5>
                            <p><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Skills</h5>
                            <ul>
                                <?php foreach ($skills as $skill): ?>
                                    <li><?php echo htmlspecialchars($skill); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="edit_profile.php" class="btn btn-primary"><i class="fa fa-edit"></i> Edit Profile</a>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    Last updated: <?php echo date('d-m-Y H:i:s'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
