<?php
include 'db.php';

// Check if POST data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $id = $_POST['id'];

    // Prepare delete query
    $sql = "DELETE FROM Components WHERE id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Execute statement
    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($conn);
?>
