<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Uploaded Images</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
    <style>
        .container {
            padding-top: 70px;
            margin-bottom: 20px;
        }
        .card-img-top {
            width: 100%;
            height: auto;
        }
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<?php include('inc/navbar.php'); ?>

<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-image"></i> Uploaded Images
        </h5>
        <div class="card-body">
            <div class="row">
                <?php
                // Include the database connection file
                include 'db.php';

                // Get the serial number from the query string
                $serialno = isset($_GET['serialno']) ? $_GET['serialno'] : '';

                // Fetch the images for the serial number from the database
                $sql = "SELECT * FROM images WHERE serialno = '$serialno'";
                $result = mysqli_query($conn, $sql);

                // Fetch the department and report from the job_details table
                $details_sql = "SELECT department, report FROM job_details WHERE serialno = '$serialno' LIMIT 1";
                $details_result = mysqli_query($conn, $details_sql);
                $details_row = mysqli_fetch_assoc($details_result);
                $department = isset($details_row['department']) ? $details_row['department'] : 'No department available';
                $report = isset($details_row['report']) ? $details_row['report'] : 'No report available';

                // Check if there are any images
                if (mysqli_num_rows($result) > 0) {
                    // Display each image in a card
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Convert the upload date to UK format
                        $uploadDate = new DateTime($row['upload_date']);
                        $ukUploadDate = $uploadDate->format('d/m/Y H:i:s'); // UK format with time

                        echo '<div class="col-md-4">';
                        echo '<div class="card mb-4">';
                        echo '<a href="' . htmlspecialchars($row['image_path']) . '" data-lightbox="image-'. $row['id'] .'" data-title="' . htmlspecialchars($row['image_type']) . '">';
                        echo '<img class="card-img-top" src="' . htmlspecialchars($row['image_path']) . '" alt="Image">';
                        echo '</a>';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">Uploaded by: ' . htmlspecialchars($row['image_type']) . '</h5>';
                        //echo '<p class="card-text">' . htmlspecialchars($row['image_type']) . '</p>';
                        echo '<p class="card-text">Uploaded on: ' . htmlspecialchars($ukUploadDate) . '</p>'; // Display the upload date in UK format
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No images found for this serial number.</p>';
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </div>
            <div class="report-section">
                <h5>Fault Report</h5>
                <p><?php echo htmlspecialchars($report); ?></p>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
</body>
</html>
