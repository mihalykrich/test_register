<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
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

        .fa-check-circle {
            color: green;
        }

        .fa-times-circle {
            color: red;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<?php include('inc/navbar.php'); ?>

<div class="container mt-4">
    <!-- Filter input field -->
    <input type="text" id="filterInput" class="form-control mb-2" placeholder="Search by Serial Number">

    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-file-text"></i> Job Details
        </h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="jobDetailsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job Number</th>
                            <th>Serial Number</th>
                            <th>Customer</th>
                            <th>Part Number</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Report</th>
                            <th>Username</th>
                            <th>Latest Location</th>
                            <th>Timestamp</th>
                            <th>Details</th>
                            <th>Image Upload</th>
                            <?php if ($_SESSION['role'] == 'admin') echo '<th>Upload Image</th>'; ?>
                            <?php if ($_SESSION['role'] == 'admin') echo '<th>Delete</th>'; ?>
                            <!-- Add the "View Images" button column in the table -->
                            <th>View Images</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include the database connection file
                        include 'db.php';

                        // Fetch the latest job details for each serial number from the database
                        $sql = "SELECT jd.id, jd.jobNumber, jd.serialno, jd.customer, jd.partno, jd.department, jd.date, jd.report, jd.username, jlh.location, jlh.timestamp, COUNT(jd.serialno) AS record_count
                                FROM job_details jd 
                                LEFT JOIN (
                                    SELECT jobDetailsId, location, timestamp
                                    FROM job_location_history
                                    WHERE (jobDetailsId, timestamp) IN (
                                        SELECT jobDetailsId, MAX(timestamp)
                                        FROM job_location_history
                                        GROUP BY jobDetailsId
                                    )
                                ) jlh ON jd.id = jlh.jobDetailsId
                                GROUP BY jd.serialno
                                ORDER BY jd.serialno, jd.date DESC";
                        $result = mysqli_query($conn, $sql);
                        
                        // Check if there are any job details
if (mysqli_num_rows($result) > 0) {
    // Display each job detail in a table row
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['jobNumber'] . '</td>';
        echo '<td>' . $row['serialno'] . '</td>';
        echo '<td>' . $row['customer'] . '</td>';
        echo '<td>' . $row['partno'] . '</td>';
        echo '<td>' . $row['department'] . '</td>';
        $date = new DateTime($row['date']);
        $ukDate = $date->format('d/m/Y'); // Format the date to UK format
        echo '<td>' . $ukDate . '</td>';
        echo '<td>' . $row['report'] . '</td>';
        echo '<td>' . $row['username'] . '</td>';
        echo '<td>' . $row['location'] . '</td>';
        $timestamp = new DateTime($row['timestamp']);
        $ukDateTime = $timestamp->format('d/m/Y H:i:s'); // Format the timestamp to UK format
        echo '<td>' . $ukDateTime . '</td>';
        echo '<td>';
        if ($row['record_count'] > 1) {
            echo '<a href="javascript:void(0);" class="view-details" data-serialno="' . $row['serialno'] . '"><i class="fa fa-list"></i></a>';
        }
        echo '</td>';
        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user') {
            echo '<td>';
            echo '<button class="btn btn-primary upload-image-btn" data-serialno="' . $row['serialno'] . '"><i class="fa fa-upload"></i> Upload Image</button>';
            echo '</td>';
        }
        if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'user') {
            echo '<td>';
            echo '<button class="btn btn-info view-images-btn" data-serialno="' . $row['serialno'] . '"><i class="fa fa-image"></i> View Images</button>';
            echo '</td>';
        }
        if ($_SESSION['role'] == 'admin') {
            echo '<td><button class="btn btn-danger delete-btn" data-id="' . $row['id'] . '"><i class="fa fa-trash"></i></button></td>';
        }
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="14">No job details found.</td></tr>';
}


                        // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
        </div>
    </div>

    <!-- Modal for showing all records of a serial number -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Job Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Job Number</th>
                                    <th>Serial Number</th>
                                    <th>Customer</th>
                                    <th>Part Number</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Report</th>
                                    <th>Username</th>
                                    <th>Location</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody">
                                <!-- Records will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for image upload -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadImageModalLabel">Upload Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadImageForm" action="fault_image_upload.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="modal_serialno">Serial Number:</label>
                            <input type="text" id="modal_serialno" name="serialno" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="modal_image_type">Image Type:</label>
                            <input id="modal_image_type" name="image_type" readonly="readonly" type="text" value="<?php include('fetch_department.php');?>" placeholder="<?php include('fetch_department.php');?>" class="form-control input-md" required="">
                        </div>
                        <div class="form-group">
                            <label for="modal_image">Choose image:</label>
                            <input type="file" id="modal_image" name="image" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Image</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Filter table rows based on input value
        $('#filterInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#jobDetailsTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Handle click on view details icon
        $('.view-details').click(function() {
            var serialno = $(this).data('serialno');

            // Fetch all records for the serial number via AJAX
            $.ajax({
                url: 'fetch_records.php',
                method: 'POST',
                data: { serialno: serialno },
                success: function(response) {
                    $('#modalTableBody').html(response);
                    $('#detailsModal').modal('show');
                }
            });
        });

        // Handle delete button click
        $('.delete-btn').click(function() {
            if (confirm('Are you sure you want to delete this record?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'delete_record.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting record.');
                        }
                    }
                });
            }
        });

        // Handle click on upload image button
        $('.upload-image-btn').click(function() {
            var serialno = $(this).data('serialno');
            $('#modal_serialno').val(serialno);
            $('#uploadImageModal').modal('show');
        });

        // Handle click on view images button
        $('.view-images-btn').click(function() {
            var serialno = $(this).data('serialno');
            window.location.href = 'view_images.php?serialno=' + serialno;
        });
    });
</script>


</body>
</html>
