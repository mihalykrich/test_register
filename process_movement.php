<?php
// Include the database connection file
include 'db.php';

// Get the form data
$testJigName = $_POST['test_jig_name'];
$newLocation = $_POST['new_location'];
$userId = $_POST['user_id'];

// Prepare and execute a query to retrieve the test jig ID based on the provided name
$sqlTestJigId = "SELECT id FROM TestJigs WHERE name = ?";
$stmt = $conn->prepare($sqlTestJigId);
$stmt->bind_param('s', $testJigName);
$stmt->execute();
$resultTestJigId = $stmt->get_result();

// Check if the query was successful
if ($resultTestJigId && $resultTestJigId->num_rows > 0) {
    // Fetch the test jig ID from the result
    $rowTestJigId = $resultTestJigId->fetch_assoc();
    $testJigId = $rowTestJigId['id'];

    // Prepare and execute a query to fetch the base location from the previous record in the location history table
    $sqlBaseLocation = "SELECT base_location FROM LocationHistory WHERE test_jig_id = ? ORDER BY movement_date DESC LIMIT 1";
    $stmtBaseLocation = $conn->prepare($sqlBaseLocation);
    $stmtBaseLocation->bind_param('i', $testJigId);
    $stmtBaseLocation->execute();
    $resultBaseLocation = $stmtBaseLocation->get_result();

    // Check if the query was successful
    if ($resultBaseLocation && $resultBaseLocation->num_rows > 0) {
        // Fetch the base location from the result
        $rowBaseLocation = $resultBaseLocation->fetch_assoc();
        $baseLocation = $rowBaseLocation['base_location'];

        // Prepare and execute the SQL query to insert into the location history table
        $sqlInsert = "INSERT INTO LocationHistory (test_jig_id, base_location, current_location, user_id) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param('issi', $testJigId, $baseLocation, $newLocation, $userId);
        $resultInsert = $stmtInsert->execute();

        // Check if the insertion was successful
        if ($resultInsert) {
            // Redirect back to the page where movement was entered
            header("Location: jig_movement.php");
            exit();
        } else {
            // Display an error message if the insertion fails
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Display an error message if the base location could not be retrieved
        echo "Error: Unable to fetch base location.";
    }
} else {
    // Display an error message if the test jig name is not found
    echo "Test jig with the provided name does not exist.";
}

// Close the database connection
$conn->close();
?>
