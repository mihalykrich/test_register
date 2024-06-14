<?php
// Database connection
include 'db.php'; // Assuming this file contains the database connection code

// Check if the unresolvedFailures data is sent via POST
if (isset($_POST['unresolvedFailures'])) {
    $unresolvedFailures = json_decode($_POST['unresolvedFailures'], true);

    // Email configuration
    $to = "mobillworld1980@gmail.com";
    $subject = "Failure Details Notification";

    // Construct HTML email message
    foreach ($unresolvedFailures as $failure) {
    $message = "<html><body>";
    $message .= "<h2>Failure Details</h2>";
    $message .= "<table border='1' cellpadding='10' cellspacing='0'>";
    $message .= "<thead><tr>";
    $message .= "<th>Test Jig</th>";
    $message .= "<th>Component ID</th>";
    $message .= "<th>Failure Date</th>";
    $message .= "<th>Reason</th>";
    $message .= "<th>Created At</th>";
    $message .= "</tr></thead>";
    $message .= "<tbody>";
    }

    // Loop through the unresolved failures and add them to the table
    foreach ($unresolvedFailures as $failure) {
        $message .= "<tr>";
        $message .= "<td>" . htmlspecialchars($failure['test_jig_id']) . "</td>";
        $message .= "<td>" . htmlspecialchars($failure['component_id']) . "</td>";
        $message .= "<td>" . htmlspecialchars($failure['failure_date']) . "</td>";
        $message .= "<td>" . htmlspecialchars($failure['reason']) . "</td>";
        $message .= "<td>" . htmlspecialchars($failure['created_at']) . "</td>";
        $message .= "</tr>";
    }

    $message .= "</tbody></table>";
    $message .= "</body></html>";

    // Set headers to indicate HTML content
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: mihaly.krich@topsupplement.co.uk" . "\r\n";

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "success"; // Email sent successfully
    } else {
        echo "error"; // Error sending email
    }
} else {
    echo "error"; // unresolvedFailures data not received via POST
}
?>
