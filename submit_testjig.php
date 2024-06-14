<?php
// Include the database connection file
include 'db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $customer = $_POST['department'];
    $testJig = $_POST['location'];
    $baseLocation = $_POST['baseLocation']; // Added base location

    // Insert test jig details into TestJigs table
    $insertTestJigQuery = "INSERT INTO TestJigs (customer, name) VALUES ('$customer', '$testJig')";
    mysqli_query($conn, $insertTestJigQuery);

    // Get the ID of the inserted test jig
    $testJigId = mysqli_insert_id($conn);

    // Insert base location into LocationHistory table
    $insertLocationQuery = "INSERT INTO LocationHistory (test_jig_id, base_location, current_location) VALUES ($testJigId, '$baseLocation', '$baseLocation')";
    mysqli_query($conn, $insertLocationQuery);

    // Loop through component data if available and insert into Components table
    if (isset($_POST['componentName']) && isset($_POST['componentQuantity'])) {
        $componentNames = $_POST['componentName'];
        $componentQuantities = $_POST['componentQuantity'];
        for ($i = 0; $i < count($componentNames); $i++) {
            $componentName = $componentNames[$i];
            $componentQuantity = $componentQuantities[$i];
            // Insert component details into Components table
            $insertComponentQuery = "INSERT INTO Components (test_jig_id, name, quantity) VALUES ($testJigId, '$componentName', $componentQuantity)";
            mysqli_query($conn, $insertComponentQuery);
        }
    }

    // Redirect to a success page or back to the form page
    header("Location: success.php");
    exit();
} else {
    // Redirect to an error page or back to the form page if form is not submitted
    header("Location: error.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
