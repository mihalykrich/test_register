<?php
include 'db.php';

// Check if POST data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    // Prepare update query
    $sql = "UPDATE Components SET $column = ? WHERE id = ?";
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "si", $value, $id);

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
