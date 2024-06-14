<?php
session_start();
include 'db.php'; // Database connection

// Fetch all available skills to display them
$sql_all_skills = "SELECT id, skill_name FROM skills";
$result_all_skills = $conn->query($sql_all_skills);
$all_skills = [];
while ($row = $result_all_skills->fetch_assoc()) {
    $all_skills[] = $row;
}

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['skills'])) {
    $skills = isset($_POST['skills']) ? explode(',', $_POST['skills'][0]) : [];
    $inserted_skills = [];

    // Prepare the SQL statement to insert skills
    $sql_insert_skill = "INSERT INTO skills (skill_name) VALUES (?)";
    $stmt_insert_skill = $conn->prepare($sql_insert_skill);

    // Bind and execute the insertion for each selected skill
    foreach ($skills as $skill_name) {
        $skill_name = trim($skill_name);

        // Check if skill already exists
        $sql_check_skill = "SELECT id FROM skills WHERE skill_name = ?";
        $stmt_check_skill = $conn->prepare($sql_check_skill);
        $stmt_check_skill->bind_param("s", $skill_name);
        $stmt_check_skill->execute();
        $result_check_skill = $stmt_check_skill->get_result();

        if ($result_check_skill->num_rows == 0) {
            // If skill does not exist, insert it
            $stmt_insert_skill->bind_param("s", $skill_name);
            $stmt_insert_skill->execute();
            $inserted_skills[] = $skill_name;
        }
    }

    if (!empty($inserted_skills)) {
        $success_message = "Skills added successfully: " . implode(', ', $inserted_skills);
    } else {
        $success_message = "No new skills were added.";
    }

    // Refresh the list of all skills
    $result_all_skills = $conn->query($sql_all_skills);
    $all_skills = [];
    while ($row = $result_all_skills->fetch_assoc()) {
        $all_skills[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_skill_id'])) {
    $remove_skill_id = intval($_POST['remove_skill_id']);
    $sql_remove_skill = "DELETE FROM skills WHERE id = ?";
    $stmt_remove_skill = $conn->prepare($sql_remove_skill);
    $stmt_remove_skill->bind_param("i", $remove_skill_id);
    $stmt_remove_skill->execute();

    // Refresh the list of all skills
    $result_all_skills = $conn->query($sql_all_skills);
    $all_skills = [];
    while ($row = $result_all_skills->fetch_assoc()) {
        $all_skills[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Skill</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include('inc/navbar.php'); ?>
<div class="container mt-4">
    <div class="row">
        <!-- Column for adding new skills -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-plus"></i> Add Skill</h4>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="add_skill.php">
                        <div class="form-group">
                            <label for="skills_category">Skills Category</label>
                            <select id="skills_category" class="form-control" required>
                                <option value="" selected="selected">Select Category</option>
                                <!-- Populate options using JavaScript -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="skills_subcategory">Skills Subcategory</label>
                            <select id="skills_subcategory" class="form-control" required>
                                <option value="" selected="selected">Select Subcategory</option>
                                <!-- Populate options using JavaScript -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="skills">Skills</label>
                            <textarea class="form-control" id="skills" name="skills[]" rows="3" readonly></textarea>
                            <button type="button" id="add_skill_btn" class="btn btn-secondary mt-2">Add Selected Skill</button>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Skills</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Column for displaying already added skills -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Already Added Skills</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Skill Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_skills as $skill): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                                    <td>
                                        <form method="post" action="add_skill.php" style="display:inline;">
                                            <input type="hidden" name="remove_skill_id" value="<?php echo $skill['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var subjectObject = {
        <?php echo file_get_contents("dropdown_jig_list.txt"); ?>
    };

    var categorySel = document.getElementById("skills_category");
    var subcategorySel = document.getElementById("skills_subcategory");
    var skillsField = document.getElementById("skills");

    for (var category in subjectObject) {
        categorySel.options[categorySel.options.length] = new Option(category, category);
    }

    categorySel.onchange = function() {
        subcategorySel.length = 1; // Clear previous options, keep the first one
        var selectedCategory = subjectObject[this.value];
        for (var subcategory in selectedCategory) {
            subcategorySel.options[subcategorySel.options.length] = new Option(subcategory, subcategory);
        }
    }

    document.getElementById("add_skill_btn").onclick = function() {
        var selectedSkill = subcategorySel.value;
        if (selectedSkill) {
            var skillsText = skillsField.value.trim();
            skillsField.value = skillsText ? skillsText + ", " + selectedSkill : selectedSkill;
        }
    }
});
</script>
</body>
</html>
