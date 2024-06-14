<?php 

include 'db.php';

// Assuming the username is stored in a session variable called 'username'
$username = $_SESSION['username'];

$sql = "SELECT department FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // output data of the logged-in user's department
    while($row = $result->fetch_assoc()) {
        echo $row["department"];
    }
} else {
    echo "No department found for the logged-in user.";
}

$stmt->close();
$conn->close();
?>
