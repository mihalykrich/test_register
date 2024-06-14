<?php
include 'db.php'; // Include database connection

// Check if the POST data is set
if (isset($_POST['componentId']) && isset($_POST['failureDate']) && isset($_POST['reason']) && isset($_POST['testJigId'])) {
    $componentId = $_POST['componentId'];
    $failureDate = $_POST['failureDate'];
    $reason = $_POST['reason'];
    $testJigId = $_POST['testJigId']; // Get the test jig ID from POST data

    // Insert data into the Failures table
    $insertQuery = "INSERT INTO Failures (component_id, failure_date, reason, test_jig_id) VALUES ($componentId, '$failureDate', '$reason', $testJigId)";
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
mysqli_close($conn);
?>
