<!DOCTYPE html>
<?php 
    include 'db.php';
?>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="/tmp/prima_favicon_32x32.png">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

    <!-- Cascading Dropdown Script -->
    <script>
        var subjectObject = {
            <?php echo file_get_contents( "dropdown_jig_list.txt" ); ?>
        }
        window.onload = function() {
            var subjectSel = document.getElementById("department");
            var topicSel = document.getElementById("location"); 
            for (var x in subjectObject) {
                subjectSel.options[subjectSel.options.length] = new Option(x, x);
            }
            subjectSel.onchange = function() {
                //empty Topics dropdown
                topicSel.length = 1;
                //display correct values
                for (var y in subjectObject[this.value]) {
                    topicSel.options[topicSel.options.length] = new Option(y, y);
                }
            }
        }
    </script>

    <!-- Navbar -->
    <?php include('inc/navbar.php');?>
</head>
    </br>
<body>
    <div id="wrap" class="mt-4"> <!-- Adjusted margin-top to match your other scripts -->
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <h5 class="card-header">
                            <i class="fa fa-qrcode" aria-hidden="true"></i> Test Jig QR Code 
                        </h5>
                        <div class="card-body">
                            <form class="form-horizontal well" action="submit_testjig.php" method="post">
                                <fieldset>
                                    <div class="col-auto mb-3"> <!-- Adjusted margin-bottom -->
                                        <label for="department">Customer</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                            </div>
                                            <select id="department" name="department" class="form-control input-md" required="">
                                                <option value="" selected="selected">Select Customer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto mb-3"> <!-- Adjusted margin-bottom -->
                                        <label for="location">Test Jig</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-cogs" aria-hidden="true"></i></div>
                                            </div>
                                            <select id="location" name="location" class="form-control input-md" required="">
                                                <option value="" selected="selected">Select Test Jig</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Add this input field inside the form -->
                                    <div class="col-auto mb-3">
                                        <label for="baseLocation">Base Location</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                            </div>
                                            <input type="text" id="baseLocation" name="baseLocation" class="form-control" required>
                                        </div>
                                    </div>

                                    <!-- Component Fields Container -->
                                    <div id="componentFieldsContainer"></div>
                                    <!-- Add Component Button -->
                                    <div class="form-group mb-3"> <!-- Adjusted margin-bottom -->
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-success" id="addComponentBtn"><i class="fa fa-plus"></i> Add Component</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button type="submit" name="submit" value="Submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>
                                            <button type="reset" name="reset" value="Reset" class="btn btn-danger"><i class="fa fa-refresh"></i> Reset</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Add Component Fields Dynamically -->
    <!-- JavaScript to Add and Remove Component Fields Dynamically -->
<script>
    $(document).ready(function(){
        // Counter for component fields
        var componentCounter = 0;

        // Function to add component fields
        function addComponentField() {
            componentCounter++;
            var componentFieldHTML = '<div class="component-field" id="componentField' + componentCounter + '">' +
                                        '<hr>' +
                                        '<div class="col-auto">' +
                                            '<label for="componentName' + componentCounter + '">Component Name</label>' +
                                            '<div class="input-group mb-2">' +
                                                '<div class="input-group-prepend">' +
                                                    '<div class="input-group-text"><i class="fa fa-cube" aria-hidden="true"></i></div>' +
                                                '</div>' +
                                                '<input type="text" id="componentName' + componentCounter + '" name="componentName[]" class="form-control" required>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="col-auto">' +
                                            '<label for="componentQuantity' + componentCounter + '">Quantity</label>' +
                                            '<div class="input-group mb-2">' +
                                                '<div class="input-group-prepend">' +
                                                    '<div class="input-group-text"><i class="fa fa-sort-amount-desc" aria-hidden="true"></i></div>' +
                                                '</div>' +
                                                '<input type="number" id="componentQuantity' + componentCounter + '" name="componentQuantity[]" class="form-control" required>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="col-auto">' +
                                            '<button type="button" class="btn btn-danger remove-component" data-target="#componentField' + componentCounter + '"><i class="fa fa-times"></i> Remove</button>' +
                                        '</div>' +
                                    '</div>';

            $('#componentFieldsContainer').append(componentFieldHTML);
        }

        // Event listener for add component button
        $('#addComponentBtn').click(function(){
            addComponentField();
        });

        // Event listener for remove component button
        $(document).on('click', '.remove-component', function(){
            var targetId = $(this).data('target');
            $(targetId).remove();
        });
    });
</script>

</body>
</html>
