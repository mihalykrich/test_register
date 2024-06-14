<?php
// Include the database connection file
include 'db.php';

// Check if component ID is provided
if(isset($_GET['componentId'])) {
    $componentId = $_GET['componentId'];

    // Fetch component details based on component ID
    $query = "SELECT * FROM Components WHERE id = $componentId";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
        $quantity = $row['quantity'];
        $partNumber = $row['part_number'];
        $description = $row['description'];
        $imagePath = $row['image_path'];

        // Output component details
        echo "<h5>Name: $name</h5>";
        echo "<p>Quantity: $quantity</p>";
        echo "<p>Part Number: $partNumber</p>";
        echo "<p>Description: $description</p>";
        // Here you can add code to display the image
        echo "<img src='$imagePath' class='img-fluid'>";
    } else {
        echo "Component not found.";
    }
} else {
    echo "Component ID not provided.";
}

// Close the database connection
mysqli_close($conn);
?>
