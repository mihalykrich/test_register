<?php
// Include the database connection file
include 'db.php';

// Check if test jig ID is provided in the URL
if (isset($_GET['test_jig_id'])) {
    $testJigId = $_GET['test_jig_id'];

    // Fetch test jig name based on test jig ID
    $testJigNameQuery = "SELECT name FROM TestJigs WHERE id = $testJigId";
    $testJigNameResult = mysqli_query($conn, $testJigNameQuery);
    $testJigNameRow = mysqli_fetch_assoc($testJigNameResult);
    $testJigName = $testJigNameRow['name'];

    // Delete test jig and its records
    $deleteTestJigQuery = "DELETE FROM TestJigs WHERE id = $testJigId";
    $deleteComponentsQuery = "DELETE FROM Components WHERE test_jig_id = $testJigId";
    $deleteFailuresQuery = "DELETE FROM Failures WHERE test_jig_id = $testJigId";

    if (mysqli_query($conn, $deleteTestJigQuery) && mysqli_query($conn, $deleteComponentsQuery) && mysqli_query($conn, $deleteFailuresQuery)) {
        // Redirect back to test_jigs.php after deletion
        header("Location: test_jigs.php");
        exit();
    } else {
        // Handle deletion error
        echo "Error deleting test jig: " . mysqli_error($conn);
    }
} else {
    // Redirect or display error message if test jig ID is not provided
    // For example: header("Location: error.php");
    // exit();
    echo "Test jig ID not provided.";
}
?>
