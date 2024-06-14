<!DOCTYPE html>
<html>
<head>
    <title>Test Jig Details</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
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

        .editable {
            cursor: pointer;
        }

        .editable input {
            border: none;
            width: 100%;
            background-color: transparent;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include('inc/navbar.php');?>

<div class="container">
    <?php
    include 'db.php';

    // Check if test jig ID is provided in the URL
    if (isset($_GET['test_jig_id'])) {
        $testJigId = $_GET['test_jig_id'];
    
        // Fetch test jig name and customer based on test jig ID
        $testJigQuery = "SELECT name, customer FROM TestJigs WHERE id = $testJigId";
        $testJigResult = mysqli_query($conn, $testJigQuery);
        $testJigData = mysqli_fetch_assoc($testJigResult);
        $testJigName = $testJigData['name'];
        $customer = $testJigData['customer'];
    
        // Fetch components data based on test jig ID
        $componentsQuery = "SELECT * FROM Components WHERE test_jig_id = $testJigId";
        $componentsResult = mysqli_query($conn, $componentsQuery);
    
        ?>
        
        <div class="card">
            <h5 class="card-header">
                <i class="fa fa-file-text"></i> <?php echo $customer; ?> <?php echo $testJigName; ?>
            </h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Component Name</th>
                            <th>Quantity</th>
                            <th>Part Number</th>
                            <th>Description</th>
                            <th>Serial Number</th>
                            <th>Asset Tag</th>
                            <th>Calibration No</th>
                            <th>Calibrated Date</th>
                            <th>Calibration Expiry</th>
                            <th>Actions</th> <!-- Added column for delete button -->
                        </tr>
                    </thead>
                        <tbody>
                            <?php
                            if (!empty($componentsResult) && mysqli_num_rows($componentsResult) > 0) {
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($componentsResult)) {
                                    // Check if failure record exists for the component
                                    $failureQuery = "SELECT * FROM Failures WHERE component_id = {$row['id']}";
                                    $failureResult = mysqli_query($conn, $failureQuery);
                                    $failureCount = mysqli_num_rows($failureResult);
            
                                    echo "<tr>";
                                    echo "<td>" . $counter . "</td>";
                                    echo "<td class='' data-id='" . $row['id'] . "' data-column='name'>" . $row['name'] . "</td>";
                                    echo "<td class='' data-id='" . $row['id'] . "' data-column='quantity'>" . $row['quantity'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='part_number'>" . $row['part_number'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='description'>" . $row['description'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='serial_number'>" . $row['serial_number'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='asset_tag'>" . $row['asset_tag'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='calibration_cert_id'>" . $row['calibration_cert_id'] . "</td>";
                                    echo "<td class='editable' data-id='" . $row['id'] . "' data-column='calibrated_date'>" . $row['calibrated_date'] . "</td>";
                                    echo "<td class='editable' id='datepicker' data-id='" . $row['id'] . "' data-column='calibration_expiry'>" . $row['calibration_expiry'] . "</td>";

                                    echo "<td>"; // Open separate <td> for buttons
            
                                    // Check if failure records exist
                                    if ($failureCount > 0) {
                                        // Disable Report Failure button and add View Failure Details button
                                        echo "<button title='View Failure Details' class='btn btn-warning view-failure-details' data-id='" . $row['id'] . "'><i class='fa fa-eye'></i> </button>";
                                        echo "<button title='Report Failure' class='btn btn-warning failure-button' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "' disabled><i class='fa fa-exclamation-triangle'></i> </button>";
                                    } else {
                                        // Enable Report Failure button
                                        echo "<button title='Report Failure' class='btn btn-warning failure-button' data-id='" . $row['id'] . "' data-name='" . $row['name'] . "'><i class='fa fa-exclamation-triangle'></i> </button>";
                                    }
            
                                    // Add delete button
                                    echo "<button title='Delete' class='btn btn-danger delete-button' data-id='" . $row['id'] . "'><i class='fa fa-trash'></i> </button>";
                                    
                                    // Add upload image button
                                    echo "<button title='Upload Image' class='btn btn-primary upload-image-button' data-id='" . $row['id'] . "'><i class='fa fa-upload'></i> </button>";
                                    
                                    // Add view component button
                                    echo "<button title='View Component' class='btn btn-info view-component-button' data-id='" . $row['id'] . "'><i class='fa fa-eye'></i> </button>";
            
                                    echo "</td>"; // Close separate <td> for buttons
                                    echo "</tr>";
            
                                    $counter++;
                                }
                            } else {
                                echo "<tr><td colspan='6'>No components found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="card-footer">
    <a href="test_jigs.php" class="btn btn-primary"><i class="fa fa-list"></i> Test Jigs</a>
    <a href="#" class="btn btn-primary" id="printButton"><i class="fa fa-print"></i> Print</a>
    <!-- Add Component Button -->
    <button class="btn btn-primary" id="addComponentButton" data-toggle="modal" data-target="#addComponentModal"><i class="fa fa-plus"></i> Add Component</button>
</div>
        </div>
    <?php
    } else {
        echo "<h2 class='my-4'>Test Jig Details</h2>";
        echo "<p>No test jig ID provided.</p>";
    }
    
    mysqli_close($conn);
    ?>
</div>

<!-- Modal for failure report -->
<div class="modal fade" id="failureModal" tabindex="-1" role="dialog" aria-labelledby="failureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="failureModalLabel">Report Component Failure</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="failureForm">
                    <input type="hidden" id="componentId" name="componentId">
                    <div class="form-group">
                        <label for="failureDate">Failure Date:</label>
                        <input type="date" class="form-control" id="failureDate" name="failureDate" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason for Failure:</label>
                        <input type="text" class="form-control" id="reason" name="reason" required>
                    </div>
                    <!-- Add more fields as needed -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for upload image -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Upload Component Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadImageForm">
                    <input type="hidden" id="componentIdUpload" name="componentIdUpload">
                    <div class="form-group">
                        <label for="image">Select Image:</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                    </div>
                    <div id="uploadStatus"></div> <!-- Display upload status message here -->
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for view component details -->
<div class="modal fade" id="viewComponentModal" tabindex="-1" role="dialog" aria-labelledby="viewComponentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewComponentModalLabel">Component Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="componentDetails">
                <!-- Component details will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for delete confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this component?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding component -->
<div class="modal fade" id="addComponentModal" tabindex="-1" role="dialog" aria-labelledby="addComponentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addComponentModalLabel">Add Component</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for adding component -->
                <form id="addComponentForm">
                    <!-- Add your form fields for component details here -->
                    <div class="form-group">
                        <label for="componentName">Component Name:</label>
                        <input type="text" class="form-control" id="componentName" name="componentName" required>
                    </div>
                    <div class="form-group">
                        <label for="componentQuantity">Quantity:</label>
                        <input type="number" class="form-control" id="componentQuantity" name="componentQuantity" required>
                    </div>
                    <!-- Add more fields as needed -->
                    <button type="submit" class="btn btn-primary">Add Component</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function(){
    // Verify jQuery inclusion
    console.log("jQuery loaded");

    // Click event for failure button
    $('.failure-button').on('click', function(){
        var componentId = $(this).data('id');
        var componentName = $(this).data('name');
        
        // Set component id in the modal form
        $('#componentId').val(componentId);
        
        // Show failure modal
        $('#failureModal').modal('show');
    });

// Submit event for failure form
$('#failureForm').submit(function(event){
    event.preventDefault(); // Prevent default form submission

    // Get form data
    var componentId = $('#componentId').val();
    var failureDate = $('#failureDate').val();
    var reason = $('#reason').val();
    var testJigId = <?php echo $testJigId; ?>; // Get the test jig ID from PHP

    // Make AJAX call to report failure
    $.ajax({
        url: 'report_failure.php',
        type: 'POST',
        data: {
            componentId: componentId,
            failureDate: failureDate,
            reason: reason,
            testJigId: testJigId // Pass the test jig ID
            // Add more data fields here
        },
        success: function(response) {
            if (response === 'success') {
                $('#failureModal').modal('hide'); // Hide the modal
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Error reporting failure');
            }
        }
    });
});



    // Click event for delete button
    $('.delete-button').on('click', function(){
        console.log("Delete button clicked"); // Debug statement

        var id = $(this).data('id');
        
        // Show confirmation modal
        $('#deleteModal').modal('show');
        
        // Click event for confirm delete button in modal
        $('#confirmDelete').off('click').on('click', function(){
            // Make AJAX call to delete_component.php
            $.ajax({
                url: 'delete_component.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response === 'success') {
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error deleting component');
                    }
                }
            });
        });
    });

    // Click event for view failure details button
    $('.view-failure-details').on('click', function() {
        var componentId = $(this).data('id');
        window.location.href = 'view_failure_details.php?component_id=' + componentId;
    });

    // Click event for upload image button
    $('.upload-image-button').on('click', function(){
        var componentId = $(this).data('id');
        $('#componentIdUpload').val(componentId);
        $('#uploadImageModal').modal('show');
    });

    // Click event for view component button
    $('.view-component-button').on('click', function() {
        var componentId = $(this).data('id');
        // Make AJAX call to fetch component details
        $.ajax({
            url: 'get_component_details.php',
            type: 'GET',
            data: { componentId: componentId },
            success: function(response) {
                $('#componentDetails').html(response);
                $('#viewComponentModal').modal('show'); // Show view component modal
            }
        });
    });

    // Submit event for upload image form
$('#uploadImageForm').submit(function(event){
    event.preventDefault(); // Prevent default form submission
    var formData = new FormData(this);

    // Make AJAX call to upload image
    $.ajax({
        url: 'upload_image.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response === 'success') {
                // If upload is successful, hide the modal and reload the page
                $('#uploadImageModal').modal('hide');
                location.reload();
            } else {
                // If there's an error, display the error message in the modal
                $('#uploadStatus').text(response);
            }
        },
        error: function(xhr, status, error) {
            // If there's an error in the AJAX request, display a generic error message
            $('#uploadStatus').text('An error occurred while uploading the image.');
        }
    });
});

    // Inline editing
    $('.editable').on('click', function(){
        var id = $(this).data('id');
        var column = $(this).data('column');
        var value = $(this).text();
        var inputType = column === 'quantity' ? 'number' : 'text';

        // Replace the cell content with an input field
        $(this).html('<input type="' + inputType + '" class="form-control" value="' + value + '">');

        // Focus on the input field
        $(this).find('input').focus();

        // Update the database when the input field loses focus
        $(this).find('input').on('blur', function(){
            var newValue = $(this).val();
            if (newValue !== value) {
                $.ajax({
                    url: 'update_component.php',
                    type: 'POST',
                    data: { id: id, column: column, value: newValue },
                    success: function(response) {
                        if (response === 'success') {
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Error updating component');
                        }
                    }
                });
            } else {
                // If the value is unchanged, revert back to the original value
                $(this).parent().text(value);
            }
        });
    });

    // Check resolved status and update table row accordingly
    $('.failure-button').each(function() {
        var componentId = $(this).data('id');
        var failureButton = $(this);
        var tableRow = failureButton.closest('tr');
        $.ajax({
            url: 'check_resolved.php',
            type: 'GET',
            data: { componentId: componentId },
            success: function(response) {
                if (response === 'resolved') {
                    // Add green badge for resolved status to the Status column
                    tableRow.find('td').eq(1).append('<span class="badge badge-success"> Resolved <i class="fa fa-history" aria-hidden="true"></i></span>');
                } else if (response === 'unresolved') {
                    // Add red badge for unresolved status to the Status column
                    tableRow.find('td').eq(1).append('<span class="badge badge-danger"> Unresolved <i class="fa fa-history" aria-hidden="true"></i></span>');
                } else {
                    // Handle unknown status
                    // You can add a badge with a different color or icon for unknown status if needed
                }


                // Update row background color based on resolved status
                if (response === 'resolved') {
                    // Resolved: Remove background color, hide view failure details button, enable report failure button
                    tableRow.css('background-color', '');
                    failureButton.siblings('.view-failure-details').hide();
                    failureButton.prop('disabled', false);
                } else if (response === 'unresolved') {
                    // Unresolved: Set background color to light red, show view failure details button, disable report failure button
                    tableRow.css('background-color', '#ffcccc');
                    failureButton.siblings('.view-failure-details').show();
                    failureButton.prop('disabled', true);
                } else {
                    // Unknown: Do nothing
                }
            }
        });
    });

     // Click event for upload image button
     $('.upload-image-button').on('click', function() {
        var componentId = $(this).data('id');
        // Add your logic to open the modal for image upload
    });

    // Click event for view component button
    $('.view-component-button').on('click', function() {
        var componentId = $(this).data('id');
        // Add your logic to open the modal for viewing the component details
    });
});

$(document).ready(function(){
    // Click event for print button
    $('#printButton').on('click', function(){
        var customer = "<?php echo $customer; ?>";
        var testJigName = "<?php echo $testJigName; ?>";
        // Extract table data
        var tableData = [];
        $('table tbody tr').each(function(){
            var rowData = [];
            $(this).find('td').each(function(){
                rowData.push($(this).text());
            });
            tableData.push(rowData);
        });

        // Make AJAX call to generate PDF using PHP script
        $.ajax({
            url: 'generate_pdf.php',
            type: 'POST',
            data: { tableData: tableData, customer: customer,
                testJigName: testJigName }, // Send table data to PHP script
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                // Create a blob object from the response and trigger download
                var blob = new Blob([response], { type: 'application/pdf' });
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = 'test_jig_details.pdf';
                a.click();
            },
            error: function(xhr, status, error) {
                alert('Error generating PDF');
            }
        });
    });
});

$(document).ready(function(){
    // Click event for Add Component button
    $('#addComponentButton').on('click', function(){
        // Clear the form fields when the modal is opened
        $('#addComponentForm')[0].reset();
    });

    // Submit event for Add Component form
    $('#addComponentForm').submit(function(event){
        event.preventDefault(); // Prevent default form submission

        // Get form data
        var componentName = $('#componentName').val();
        var componentQuantity = $('#componentQuantity').val();
        var testJigId = <?php echo $testJigId; ?>; // Get the test jig ID from PHP

        // Make AJAX call to submit component data
        $.ajax({
            url: 'submit_component.php', // Change the URL to your PHP script for submitting component data
            type: 'POST',
            data: {
                testJigId: testJigId, // Pass the test jig ID
                componentName: componentName,
                componentQuantity: componentQuantity
                // Add more data fields as needed
            },
            success: function(response) {
                if (response === 'success') {
                    $('#addComponentModal').modal('hide'); // Hide the modal
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error adding component');
                }
            }
        });
    });
});

</script>

</body>
</html>
