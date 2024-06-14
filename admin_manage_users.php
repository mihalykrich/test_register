<?php
session_start();
include 'db.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, username, email, department, base_location, role FROM Users";
$result = $conn->query($sql);

// Fetch skills for each user
$user_skills = [];
$sql_user_skills = "SELECT users.id as user_id, skills.skill_name 
                    FROM users 
                    JOIN userskills ON users.id = userskills.user_id 
                    JOIN skills ON userskills.skill_id = skills.id";
$result_user_skills = $conn->query($sql_user_skills);

while ($row = $result_user_skills->fetch_assoc()) {
    $user_skills[$row['user_id']][] = $row['skill_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('inc/navbar.php'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-users"></i> Manage Users</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Base Location</th>
                                <th>Role</th>
                                <th>Skills</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                                    <td><?php echo htmlspecialchars($row['base_location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars(implode(', ', $user_skills[$row['id']] ?? [])); ?></td>
                                    <td>
                                        <a href="edit_profile.php?user_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted">
                    Total users: <?php echo $result->num_rows; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
