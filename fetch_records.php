<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['serialno'])) {
    $serialno = $_POST['serialno'];

    // Fetch all records for the serial number from the database
    $sql = "SELECT jd.id, jd.jobNumber, jd.serialno, jd.customer, jd.partno, jd.department, jd.date, jd.report, jd.username, jlh.location, jlh.timestamp
            FROM job_details jd 
            LEFT JOIN job_location_history jlh ON jd.id = jlh.jobDetailsId
            WHERE jd.serialno = ?
            ORDER BY jd.date DESC, jlh.timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $serialno);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any job details
    if ($result->num_rows > 0) {
        // Display each job detail in a table row
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['jobNumber'] . '</td>';
            echo '<td>' . $row['serialno'] . '</td>';
            echo '<td>' . $row['customer'] . '</td>';
            echo '<td>' . $row['partno'] . '</td>';
            echo '<td>' . $row['department'] . '</td>';
            echo '<td>' . $row['date'] . '</td>';
            echo '<td>' . $row['report'] . '</td>';
            echo '<td>' . $row['username'] . '</td>';
            echo '<td>' . $row['location'] . '</td>';
            echo '<td>' . $row['timestamp'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="11">No job details found.</td></tr>';
    }

    $stmt->close();
    $conn->close();
}
?>
