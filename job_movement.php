<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .container {
            padding-top: 70px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<?php include('inc/navbar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-map-marker"></i> Add Location
        </h5>
        <div class="card-body">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include 'db.php';

                $serialno = $_POST['serialno'] ?? '';
                $location = $_POST['location'] ?? '';

                if (!empty($serialno) && !empty($location)) {
                    // Get job details ID based on the serial number
                    $jobDetailsIdQuery = "SELECT id FROM job_details WHERE serialno = ?";
                    $stmt = $conn->prepare($jobDetailsIdQuery);
                    $stmt->bind_param("s", $serialno);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $jobDetailsId = $result->fetch_assoc()['id'];

                        // Insert location into job_location_history
                        $insertQuery = "INSERT INTO job_location_history (jobDetailsId, location, timestamp) VALUES (?, ?, NOW())";
                        $stmt = $conn->prepare($insertQuery);
                        $stmt->bind_param("is", $jobDetailsId, $location);
                        
                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success" role="alert">Location added successfully!</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error adding location: ' . $conn->error . '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning" role="alert">No job found with the provided Serial Number.</div>';
                    }

                    $stmt->close();
                    $conn->close();
                } else {
                    echo '<div class="alert alert-warning" role="alert">Please fill in both fields.</div>';
                }
            }
            ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="serialno">Serial Number</label>
                    <input type="text" class="form-control" id="serialno" name="serialno" placeholder="Enter Serial Number" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter Location" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Location</button>
            </form>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>
