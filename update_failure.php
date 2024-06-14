<?php
// Include database connection
include 'db.php';

// Check if the POST data is set
if (isset($_POST['failureId'])) {
    $failureId = $_POST['failureId'];
    
    // Update supervisor notified status if provided
    if (isset($_POST['supervisorNotified'])) {
        $supervisorNotified = $_POST['supervisorNotified'];
        $updateQuery = "UPDATE Failures SET supervisor_notified = '$supervisorNotified' WHERE id = $failureId";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            echo 'error';
            exit;
        }
    }
    
    // Update action taken if provided
    if (isset($_POST['actionTaken'])) {
        $actionTaken = $_POST['actionTaken'];
        $updateQuery = "UPDATE Failures SET action_taken = '$actionTaken' WHERE id = $failureId";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            echo 'error';
            exit;
        }
    }
    
    // Update replacement date if provided
    if (isset($_POST['replacementDate'])) {
        $replacementDate = $_POST['replacementDate'];
        $updateQuery = "UPDATE Failures SET replacement_date = '$replacementDate' WHERE id = $failureId";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            echo 'error';
            exit;
        }
    }
    
    // Update resolved status if provided
    if (isset($_POST['resolved'])) {
        $resolved = $_POST['resolved'];
        $updateQuery = "UPDATE Failures SET resolved = '$resolved' WHERE id = $failureId";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            echo 'error';
            exit;
        }
    }

    // If all updates were successful
    echo 'success';
} else {
    // If POST data is not set
    echo 'error';
}
?>
