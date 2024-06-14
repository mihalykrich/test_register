<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Jigs</title>
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
    <input type="text" id="filterInput" class="form-control mb-2" placeholder="Search by Name">

    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-file-text"></i> Test Jigs
        </h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="testJigsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Name</th>
                            <th>Base Location</th>
                            <th>Latest Current Location</th>
                            <th>Moved By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include the database connection file
                        include 'db.php';

                        // Fetch the list of test jigs from the database
                        $sql = "SELECT t.id, t.customer, t.name, l.base_location, l.current_location, u.username AS moved_by
                                FROM TestJigs t 
                                LEFT JOIN (
                                    SELECT test_jig_id, MAX(movement_date) AS latest_movement_date 
                                    FROM LocationHistory 
                                    GROUP BY test_jig_id
                                ) latest ON t.id = latest.test_jig_id
                                LEFT JOIN LocationHistory l ON t.id = l.test_jig_id AND l.movement_date = latest.latest_movement_date
                                LEFT JOIN Users u ON l.user_id = u.id";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any test jigs
                        if (mysqli_num_rows($result) > 0) {
                            // Display each test jig in a table row with a button for further details
                            while ($row = mysqli_fetch_assoc($result)) {
                                $testJigId = $row['id'];
                                $testJigCustomer = $row['customer'];
                                $testJigName = $row['name'];
                                $baseLocation = $row['base_location'];
                                $currentLocation = $row['current_location'];
                                $movedBy = $row['moved_by'] ? $row['moved_by'] : 'N/A';

                                // Check if base location matches current location
                                $locationIcon = ($baseLocation == $currentLocation) ? 'fa fa-check-circle' : 'fa fa-times-circle';

                                echo '<tr>';
                                echo '<td>' . $testJigId . '</td>';
                                echo '<td>' . $testJigCustomer . '</td>';
                                echo '<td>' . $testJigName . '</td>';
                                echo '<td>' . $baseLocation . ' <i class="' . $locationIcon . '"></i></td>';
                                echo '<td>' . $currentLocation . '</td>';
                                echo '<td>' . $movedBy . '</td>';
                                echo '<td>';
                                echo '<a href="jig_details.php?test_jig_id=' . $testJigId . '" class="btn btn-primary"><i class="fa fa-info-circle"></i> Details</a>';
                                echo '<a href="location_history.php?test_jig_id=' . $testJigId . '" class="btn btn-info"><i class="fa fa-history"></i> Location History</a>';
                                echo '<button class="btn btn-danger delete-btn" data-test-jig-id="' . $testJigId . '" data-toggle="modal" data-target="#confirmDeleteModal"><i class="fa fa-trash"></i> Delete</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7">No test jigs found.</td></tr>';
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this test jig and all its records?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filter table rows based on input value
            $('#filterInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#testJigsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Variable to store the test jig ID to be deleted
            var testJigIdToDelete;

            // Function to handle click event on delete button
            $('.delete-btn').click(function() {
                // Get the test jig ID associated with the delete button
                testJigIdToDelete = $(this).data('test-jig-id');
            });

            // Function to handle click event on confirm delete button
            $('#confirmDeleteBtn').click(function() {
                // Redirect to a PHP script to delete the test jig and its records
                window.location.href = 'delete_test_jig.php?test_jig_id=' + testJigIdToDelete;
            });
        });
    </script>

</body>
</html>
