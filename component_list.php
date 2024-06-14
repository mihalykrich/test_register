<!DOCTYPE html>
<html>
<head>
    <title>Component List</title>
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

        .filter-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include('inc/navbar.php');?>

<div class="container">
    <?php
    include 'db.php';

    // Check if filter is applied
    $filterQuery = "";
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
        $filterQuery = "WHERE components.name LIKE '%$filter%'";
    }

    // Fetch all components with their associated test jig names
    $query = "SELECT components.*, testjigs.name as test_jig_name FROM components 
              LEFT JOIN testjigs ON components.test_jig_id = testjigs.id 
              $filterQuery";
    $result = mysqli_query($conn, $query);
    ?>

    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-file-text"></i> Component List
        </h5>
        <div class="card-body">
            <form method="GET" action="" class="filter-form">
                <div class="form-group">
                    <label for="filter">Filter by Component Name:</label>
                    <input type="text" class="form-control" id="filter" name="filter" value="<?php echo isset($_GET['filter']) ? $_GET['filter'] : ''; ?>">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    <a href="?" class="btn btn-secondary"><i class="fa fa-times"></i> Clear Filter</a>
                </div>
            </form>
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
                            <th>Test Jig</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $counter = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $counter . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "<td>" . $row['part_number'] . "</td>";
                                echo "<td>" . $row['description'] . "</td>";
                                echo "<td>" . $row['serial_number'] . "</td>";
                                echo "<td>" . $row['asset_tag'] . "</td>";
                                echo "<td>" . $row['calibration_cert_id'] . "</td>";
                                echo "<td>" . $row['calibrated_date'] . "</td>";
                                echo "<td>" . $row['calibration_expiry'] . "</td>";
                                echo "<td>" . $row['test_jig_name'] . "</td>";
                                echo "</tr>";
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='11'>No components found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    mysqli_close($conn);
    ?>
</div>

</body>
</html>
