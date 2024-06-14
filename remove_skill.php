<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['skill_name'])) {
    $user_id = $_POST['user_id'];
    $skill_name = $_POST['skill_name'];

    // Fetch the skill ID based on skill name
    $sql_get_skill_id = "SELECT id FROM skills WHERE skill_name = ?";
    $stmt_get_skill_id = $conn->prepare($sql_get_skill_id);
    $stmt_get_skill_id->bind_param("s", $skill_name);
    $stmt_get_skill_id->execute();
    $result_get_skill_id = $stmt_get_skill_id->get_result();

    if ($result_get_skill_id->num_rows > 0) {
        $row = $result_get_skill_id->fetch_assoc();
        $skill_id = $row['id'];

        // Delete the skill from user's skills
        $sql_delete_skill = "DELETE FROM userskills WHERE user_id = ? AND skill_id = ?";
        $stmt_delete_skill = $conn->prepare($sql_delete_skill);
        $stmt_delete_skill->bind_param("ii", $user_id, $skill_id);
        $stmt_delete_skill->execute();
    }
}
?>
