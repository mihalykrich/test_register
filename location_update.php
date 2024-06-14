<!DOCTYPE html>
<?php 
	include 'db.php';
 
    $serial = $_GET['serial']??null;
	$location = $_GET['location']??null;
	
	

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE redtag SET location='$location',completed_status='Allocated To Technician' WHERE Serialno='$serial'";

if ($conn->query($sql) === TRUE) {
  echo "<script type=\"text/javascript\">
						alert(\"Record updated successfully.\");
						window.location = \"location_select_hht.php\"
					</script>";
} else {
  echo "Error updating record: " . $conn->error;
}

$conn->close();
?>

