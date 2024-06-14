<!DOCTYPE html>
<html>
<head>
    <title>View Failure Details</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .container {
            padding-top: 70px;
        }

        .table-responsive {
            margin-top: 20px;
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

        .form-control {
            width: 150px;
        }

        .resolved-row {
            background-color: #d4edda !important; /* Light green background */
        }

        .resolved-row select, .resolved-row button {
            pointer-events: none; /* Disable pointer events */
            opacity: 0.5; /* Reduce opacity */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include('inc/navbar.php');?>

<div class="container">
    
    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-exclamation-triangle"></i> Failure Details
        </h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Failure Date</th>
                            <th>Supervisor Notified</th>
                            <th>Replacement Date</th>
                            <th>Reason</th>
                            <th>Action Taken</th>
                            <th>Resolved</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'db.php';

                        if (isset($_GET['component_id'])) {
                            $componentId = $_GET['component_id'];

                            $failureQuery = "SELECT * FROM Failures WHERE component_id = $componentId";
                            $failureResult = mysqli_query($conn, $failureQuery);

                            if ($failureResult && mysqli_num_rows($failureResult) > 0) {
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($failureResult)) {
                                    $testJigId = $row['test_jig_id']; // Get test_jig_id from the row
                                    echo "<tr" . ($row['resolved'] ? " class='resolved-row'" : "") . ">";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td>" . $row['failure_date'] . "</td>";
                                    echo "<td>";
                                    echo "<select class='form-control supervisor-notified' data-id='" . $row['id'] . "'" . ($row['resolved'] ? " disabled" : "") . ">";
                                    echo "<option value='' selected>Select</option>"; // Blank option
                                    echo "<option value='1'" . ($row['supervisor_notified'] == 1 ? ' selected' : '') . ">Yes</option>";
                                    echo "<option value='0'" . ($row['supervisor_notified'] == 0 ? ' selected' : '') . ">No</option>";
                                    echo "</select>";
                                    echo "</td>";
                                    echo "<td>" . $row['replacement_date'] . "</td>";
                                    echo "<td>" . $row['reason'] . "</td>";
                                    echo "<td>";
                                    echo "<select class='form-control action-taken' data-id='" . $row['id'] . "'" . ($row['resolved'] ? " disabled" : "") . ">";
                                    echo "<option value='' selected>Select</option>"; // Blank option
                                    echo "<option value='Replace'" . ($row['action_taken'] == 'Replace' ? ' selected' : '') . ">Replaced</option>";
                                    echo "<option value='Repair'" . ($row['action_taken'] == 'Repair' ? ' selected' : '') . ">Repair</option>";
                                    echo "<option value='Other'" . ($row['action_taken'] == 'Other' ? ' selected' : '') . ">Other</option>";
                                    echo "</select>";
                                    echo "</td>";
                                    echo "<td>";
                                    if ($row['resolved']) {
                                        echo "<button class='btn btn-success' disabled><i class='fa fa-check'></i> Resolved</button>";
                                    } else {
                                        echo "<button class='btn btn-success resolved-button' data-id='" . $row['id'] . "'><i class='fa fa-check'></i> Resolved</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";

                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='7'>No failure details found</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Component ID not provided</td></tr>";
                        }

                        // Assume $testJigId is already defined
                        $testJigName = '';
                        if (isset($testJigId) && !empty($testJigId)) {
                            // Replace with your actual database connection and query
                            $query = "SELECT name FROM testjigs WHERE ID = ?";
                            if ($stmt = $conn->prepare($query)) {
                                $stmt->bind_param("i", $testJigId);
                                $stmt->execute();
                                $stmt->bind_result($testJigName);
                                $stmt->fetch();
                                $stmt->close();
                            }
                        }

                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary notify-supervisor-button"><i class="fa fa-envelope"></i> Notify Supervisor</button>
            <a href="jig_details.php?test_jig_id=<?php echo isset($testJigId) ? $testJigId : ''; ?>" class="btn btn-primary">Test-Jig View</a>
        </div>
        
    </div>
            
    
</div>

<script>
$(document).ready(function(){
    // Handle change event for supervisor notified dropdown
    $('.supervisor-notified').on('change', function(){
        var failureId = $(this).data('id');
        var supervisorNotified = $(this).val();

        // Make AJAX call to update supervisor notified status
        $.ajax({
            url: 'update_failure.php',
            type: 'POST',
            data: { failureId: failureId, supervisorNotified: supervisorNotified },
            success: function(response) {
                if (response !== 'success') {
                    alert('Error updating supervisor notified status');
                }
            }
        });
    });

    // Handle change event for action taken dropdown
    $('.action-taken').on('change', function(){
        var failureId = $(this).data('id');
        var actionTaken = $(this).val();

        // If action taken is 'Replace', set replacement date
        var replacementDate = null;
        if (actionTaken === 'Replace') {
            replacementDate = getCurrentDate();
            $(this).closest('tr').find('td:nth-child(4)').text(replacementDate);
        }

        // Make AJAX call to update action taken and replacement date
        $.ajax({
            url: 'update_failure.php',
            type: 'POST',
            data: { failureId: failureId, actionTaken: actionTaken, replacementDate: replacementDate },
            success: function(response) {
                if (response !== 'success') {
                    alert('Error updating action taken');
                }
            }
        });
    });

    // Handle click event for resolved button
    $('.resolved-button').on('click', function(){
        var failureId = $(this).data('id');

        // Make AJAX call to update resolved status
        $.ajax({
            url: 'update_failure.php',
            type: 'POST',
            data: { failureId: failureId, resolved: 1 },
            success: function(response) {
                if (response === 'success') {
                    // Disable all elements in the row and add background color
                    var row = $(this).closest('tr');
                    row.addClass('resolved-row');
                    row.find('select, button').prop('disabled', true).css('opacity', '0.5');
                } else {
                    alert('Error updating resolved status');
                }
            }.bind(this) // Bind the current context to use 'this' inside the success function
        });
    });

    // Function to get current date in YYYY-MM-DD format
    function getCurrentDate() {
        var date = new Date();
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var day = date.getDate().toString().padStart(2, '0');
        return year + '-' + month + '-' + day;
    }
});

// Handle click event for "Notify Supervisor" button
$('.notify-supervisor-button').on('click', function(){
    // Collect unresolved failure details
    var unresolvedFailures = [];
    $('.table tbody tr').each(function(){
        var row = $(this);
        if (!row.hasClass('resolved-row')) {
            var failureDetails = {
                test_jig_id: "<?php echo $testJigName; ?>",
                component_id: "<?php echo $componentId; ?>", // Assuming component_id is known
                failure_date: row.find('td:nth-child(2)').text().trim(),
                reason: row.find('td:nth-child(5)').text().trim(),
                created_at: new Date().toISOString().slice(0, 19).replace('T', ' '), // Assuming current time as created_at
            };
            unresolvedFailures.push(failureDetails);
        }
    });

    // Make AJAX call to send email with unresolved failure details
    $.ajax({
        url: 'send_email.php', // PHP script to handle email sending
        type: 'POST',
        data: { unresolvedFailures: JSON.stringify(unresolvedFailures) },
        success: function(response) {
            if (response === 'success') {
                alert('Email sent successfully to the supervisor.');
            } else {
                alert('Error sending email to the supervisor.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error sending email:', error);
            alert('Error sending email to the supervisor.');
        }
    });
});


</script>

</body>
</html>
