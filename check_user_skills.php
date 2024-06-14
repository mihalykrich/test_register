<?php
include 'db.php';

$userId = $_POST['user_id'];
$testJigName = $_POST['test_jig_name'];

// Fetch user skills
$sqlUserSkills = "
    SELECT s.skill_name 
    FROM skills s 
    JOIN userskills us ON s.id = us.skill_id 
    WHERE us.user_id = ?";
$stmtUserSkills = $conn->prepare($sqlUserSkills);
$stmtUserSkills->bind_param('i', $userId);
$stmtUserSkills->execute();
$resultUserSkills = $stmtUserSkills->get_result();

$userSkills = [];
while ($row = $resultUserSkills->fetch_assoc()) {
    $userSkills[] = $row['skill_name'];
}
$stmtUserSkills->close();

// Check if user has the required skills for the test jig
$hasSkills = in_array($testJigName, $userSkills);

// If user does not have the skills, fetch users who do
$qualifiedUsers = [];
if (!$hasSkills) {
    $sqlQualifiedUsers = "
        SELECT u.id, u.username 
        FROM users u
        JOIN userskills us ON u.id = us.user_id
        JOIN skills s ON us.skill_id = s.id
        WHERE s.skill_name = ?";
    $stmtQualifiedUsers = $conn->prepare($sqlQualifiedUsers);
    $stmtQualifiedUsers->bind_param('s', $testJigName);
    $stmtQualifiedUsers->execute();
    $resultQualifiedUsers = $stmtQualifiedUsers->get_result();

    while ($row = $resultQualifiedUsers->fetch_assoc()) {
        $qualifiedUsers[] = ['id' => $row['id'], 'username' => $row['username']];
    }
    $stmtQualifiedUsers->close();
}

echo json_encode([
    'hasSkills' => $hasSkills,
    'qualifiedUsers' => $qualifiedUsers
]);

$conn->close();
?>
