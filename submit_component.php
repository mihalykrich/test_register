<?php
// Include the database connection file
include 'db.php';

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the test jig ID and component details from POST data
    $testJigId = $_POST['testJigId'];
    $componentName = $_POST['componentName'];
    $componentQuantity = $_POST['componentQuantity'];

    // Insert component details into the Components table
    $insertComponentQuery = "INSERT INTO Components (test_jig_id, name, quantity) VALUES ($testJigId, '$componentName', $componentQuantity)";
    
    // Execute the query
    if (mysqli_query($conn, $insertComponentQuery)) {
        // If insertion is successful, send a success message
        echo 'success';
    } else {
        // If there's an error, send an error message
        echo 'error';
    }
} else {
    // If form data is not submitted, send an error message
    echo 'error';
}

// Close the database connection
mysqli_close($conn);
?>
