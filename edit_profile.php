<?php
session_start();
include 'db.php'; // Database connection

// Determine if admin is editing another user's profile
if (isset($_GET['user_id']) && $_SESSION['role'] == 'admin') {
    $user_id = $_GET['user_id'];
} else {
    $user_id = $_SESSION['user_id'];
}

// Fetch user details
$sql = "SELECT username, email, department, base_location, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch all available skills
$sql_all_skills = "SELECT id, skill_name FROM skills";
$result_all_skills = $conn->query($sql_all_skills);
$all_skills = [];
while ($row = $result_all_skills->fetch_assoc()) {
    $all_skills[] = $row;
}

// Fetch user skills
$sql_skills = "SELECT skills.id, skills.skill_name FROM userskills JOIN skills ON userskills.skill_id = skills.id WHERE userskills.user_id = ?";
$stmt_skills = $conn->prepare($sql_skills);
$stmt_skills->bind_param("i", $user_id);
$stmt_skills->execute();
$result_skills = $stmt_skills->get_result();
$user_skills = [];
while ($row = $result_skills->fetch_assoc()) {
    $user_skills[] = $row;
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $base_location = $_POST['base_location'];
    $skills = isset($_POST['skills']) ? explode(',', $_POST['skills'][0]) : [];
    $skills_to_remove = isset($_POST['skills_to_remove']) ? explode(',', $_POST['skills_to_remove'][0]) : [];

    // Update users table
    $sql_update = "UPDATE users SET username = ?, email = ?, department = ?, base_location = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $username, $email, $department, $base_location, $user_id);
    $stmt_update->execute();

    // Insert new user skills
    $sql_insert_skill = "INSERT INTO userskills (user_id, skill_id) VALUES (?, ?)";
    $stmt_insert_skill = $conn->prepare($sql_insert_skill);

    $inserted_skills = [];
    foreach ($skills as $skill_name) {
        // Trim any extra spaces from skill names
        $skill_name = trim($skill_name);

        // Check if the skill is already assigned
        if (!in_array($skill_name, array_column($user_skills, 'skill_name'))) {
            // Fetch the skill ID based on skill name
            $sql_get_skill_id = "SELECT id FROM skills WHERE skill_name = ?";
            $stmt_get_skill_id = $conn->prepare($sql_get_skill_id);
            $stmt_get_skill_id->bind_param("s", $skill_name);
            $stmt_get_skill_id->execute();
            $result_get_skill_id = $stmt_get_skill_id->get_result();

            if ($result_get_skill_id->num_rows > 0) {
                $row = $result_get_skill_id->fetch_assoc();
                $skill_id = $row['id'];

                // Bind user_id and skill_id and execute insertion
                $stmt_insert_skill->bind_param("ii", $user_id, $skill_id);
                $stmt_insert_skill->execute();

                $inserted_skills[] = $skill_name;
            }
        }
    }

    // Remove user skills
    $sql_delete_skill = "DELETE FROM userskills WHERE user_id = ? AND skill_id = ?";
    $stmt_delete_skill = $conn->prepare($sql_delete_skill);

    $removed_skills = [];
    foreach ($skills_to_remove as $skill_name) {
        // Trim any extra spaces from skill names
        $skill_name = trim($skill_name);

        // Check if the skill is assigned to the user
        foreach ($user_skills as $user_skill) {
            if ($user_skill['skill_name'] == $skill_name) {
                $skill_id = $user_skill['id'];

                // Bind user_id and skill_id and execute deletion
                $stmt_delete_skill->bind_param("ii", $user_id, $skill_id);
                $stmt_delete_skill->execute();

                $removed_skills[] = $skill_name;
            }
        }
    }

    // Set a session variable for success message
    $messages = [];
    if (!empty($inserted_skills)) {
        $messages[] = "Skills added successfully: " . implode(', ', $inserted_skills);
    }
    if (!empty($removed_skills)) {
        $messages[] = "Skills removed successfully: " . implode(', ', $removed_skills);
    }
    $_SESSION['success_message'] = implode('. ', $messages);

    // Redirect after update
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_manage_users.php");
    } else {
        header("Location: user_profile.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('inc/navbar.php'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-edit"></i> Edit Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success" id="success-message">
                            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                            <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="edit_profile.php<?php echo isset($_GET['user_id']) ? '?user_id=' . $_GET['user_id'] : ''; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <select class="form-control" id="department" name="department" required>
                                        <option value="" <?php echo empty($user['department']) ? 'selected' : ''; ?>>Select a department</option>
                                        <option value="Test" <?php echo $user['department'] == 'Test' ? 'selected' : ''; ?>>Test</option>
                                        <option value="AOI" <?php echo $user['department'] == 'AOI' ? 'selected' : ''; ?>>AOI</option>
                                        <option value="SMT" <?php echo $user['department'] == 'SMT' ? 'selected' : ''; ?>>SMT</option>
                                        <option value="Mech" <?php echo $user['department'] == 'Mech' ? 'selected' : ''; ?>>Mech</option>
                                        <option value="Conventional" <?php echo $user['department'] == 'Conventional' ? 'selected' : ''; ?>>Conventional</option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="base_location">Base Location</label>
                                    <input type="text" class="form-control" id="base_location" name="base_location" value="<?php echo htmlspecialchars($user['base_location']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <div class="form-group">
                                        <label for="available_skills">Available Skills</label>
                                        <select id="available_skills" class="form-control">
                                            <option value="" selected="selected">Select Skill</option>
                                            <?php foreach ($all_skills as $skill): ?>
                                                <option value="<?php echo htmlspecialchars($skill['skill_name']); ?>"><?php echo htmlspecialchars($skill['skill_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" id="add_skill_btn" class="btn btn-secondary mt-2">Add Selected Skill</button>
                                    </div>
                                    <div class="form-group">
                                        <label for="skills">Skills</label>
                                        <textarea class="form-control" id="skills" name="skills[]" rows="6" readonly><?php echo htmlspecialchars(implode(', ', array_column($user_skills, 'skill_name'))); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="skills_to_remove">Remove Skills</label>
                                        <select id="skills_to_remove" class="form-control">
                                            <option value="" selected="selected">Select Skill to Remove</option>
                                            <?php foreach ($user_skills as $skill): ?>
                                                <option value="<?php echo htmlspecialchars($skill['skill_name']); ?>"><?php echo htmlspecialchars($skill['skill_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" id="remove_skill_btn" class="btn btn-danger mt-2">Remove Selected Skill</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                    </form>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    var availableSkillsSelect = document.getElementById("available_skills");
    var skillsField = document.getElementById("skills");
    var removeSkillsSelect = document.getElementById("skills_to_remove");

    document.getElementById("add_skill_btn").onclick = function() {
        var selectedSkill = availableSkillsSelect.value;
        if (selectedSkill) {
            var skillsText = skillsField.value.trim();
            var skillsArray = skillsText ? skillsText.split(', ') : [];
            if (!skillsArray.includes(selectedSkill)) {
                skillsArray.push(selectedSkill);
                skillsField.value = skillsArray.join(', ');

                // Add to remove skills select
                var option = document.createElement("option");
                option.value = selectedSkill;
                option.textContent = selectedSkill;
                removeSkillsSelect.appendChild(option);

                // Remove notification if it exists
                removeNotification();

                // Store notification state in session storage
                sessionStorage.setItem('successMessage', 'Skill added successfully: ' + selectedSkill);

                // Display notification
                displayNotification();
            }
        }
    };

    document.getElementById("remove_skill_btn").onclick = function() {
        var selectedSkill = removeSkillsSelect.value;
        if (selectedSkill) {
            var skillsText = skillsField.value.trim();
            var skillsArray = skillsText ? skillsText.split(', ') : [];
            var index = skillsArray.indexOf(selectedSkill);
            if (index > -1) {
                skillsArray.splice(index, 1);
                skillsField.value = skillsArray.join(', ');

                // Remove from remove skills select
                removeSkillsSelect.remove(removeSkillsSelect.selectedIndex);

                // Remove notification if it exists
                removeNotification();

                // Store notification state in session storage
                sessionStorage.setItem('successMessage', 'Skill removed successfully: ' + selectedSkill);

                // Display notification
                displayNotification();

                // Send data to server for deletion
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'remove_skill.php'); // Replace 'remove_skill.php' with the actual URL of your PHP script to handle skill removal
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    // Handle response from server if needed
                };
                xhr.send('user_id=<?php echo $user_id; ?>&skill_name=' + encodeURIComponent(selectedSkill));
            }
        }
    };

    // Function to display notification
    function displayNotification() {
        var successMessage = document.createElement('div');
        successMessage.className = 'alert alert-success';
        successMessage.textContent = sessionStorage.getItem('successMessage');
        document.querySelector('.card-body').insertBefore(successMessage, document.querySelector('form'));

        // Remove notification after a few seconds
        setTimeout(function() {
            removeNotification();
            sessionStorage.removeItem('successMessage');
        }, 3000);
    }

    // Function to remove notification
    function removeNotification() {
        var existingNotification = document.querySelector('.alert.alert-success');
        if (existingNotification) {
            existingNotification.remove();
        }
    }

    // Check session storage for existing notification
    if (sessionStorage.getItem('successMessage')) {
        displayNotification();
    }

    // Clear session storage when navigating away from the page
    window.addEventListener('beforeunload', function() {
        sessionStorage.removeItem('successMessage');
    });
});



</script>
</body>
</html>
