<?php
include 'db.php';

if(isset($_GET['componentId'])) {
    $componentId = $_GET['componentId'];
    
    // Prepare SQL statement with a placeholder for componentId
    $query = "SELECT resolved 
              FROM Failures 
              WHERE component_id = ? 
              ORDER BY id DESC 
              LIMIT 1";

    // Prepare and bind the statement
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $componentId);
    
    // Execute the statement
    mysqli_stmt_execute($stmt);
    
    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $resolvedStatus = $row['resolved'];

        // Check if the resolved status is 1 (resolved)
        if($resolvedStatus == 1) {
            echo 'resolved';
        } else {
            echo 'unresolved';
        }
    } else {
        // If no failure report exists, consider it unknown
        echo 'unknown';
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    echo 'error';
}

mysqli_close($conn);
?>
