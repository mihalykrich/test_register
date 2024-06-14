<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location History</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .container {
            padding-top: 70px;
            margin-bottom: 20px;
        }

        .table {
            border: 1px solid #dee2e6;
            border-collapse: collapse;
            width: 100%;
        }

        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 8px 12px;
            font-size: 14px;
            height: 36px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<?php include('inc/navbar.php');?>

<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-history"></i> Location History
        </h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Current Location</th>
                            <th>Moved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include the database connection file
                        include 'db.php';

                        // Check if test jig ID is provided in the URL
                        if (isset($_GET['test_jig_id'])) {
                            // Sanitize the input
                            $testJigId = mysqli_real_escape_string($conn, $_GET['test_jig_id']);

                            // Fetch location history for the specified test jig from the database
                            $sql = "SELECT lh.movement_date, lh.current_location, u.username AS moved_by 
                                    FROM LocationHistory lh
                                    LEFT JOIN Users u ON lh.user_id = u.id
                                    WHERE lh.test_jig_id = $testJigId 
                                    ORDER BY lh.movement_date DESC";
                            $result = mysqli_query($conn, $sql);

                            // Check if there are any location history records
                            if (mysqli_num_rows($result) > 0) {
                                // Display each location history record in a table row
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>';
                                    echo '<td>' . $row['movement_date'] . '</td>';
                                    echo '<td>' . $row['current_location'] . '</td>';
                                    echo '<td>' . $row['moved_by'] . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="3">No location history found for this test jig.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3">Test jig ID not provided.</td></tr>';
                        }

                        // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
