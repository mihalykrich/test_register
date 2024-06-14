<?php
require_once __DIR__ . '/vendor/autoload.php';

// Get table data from POST request
$tableData = $_POST['tableData'];

// Get customer and test jig name
$customer = isset($_POST['customer']) ? $_POST['customer'] : '';
$testJigName = isset($_POST['testJigName']) ? $_POST['testJigName'] : '';

// Create HTML content for MPDF
$html = '<!DOCTYPE html>
<html>
<head>
    <title>Test Jig Details</title>
    <style>
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
        .header-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        .header-table th, .header-table td {
            border: 1px solid #dee2e6;
            padding: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <table class="header-table">
        <tr>
            <td><strong>Customer:</strong></td>
            <td>' . $customer . '</td>
        </tr>
        <tr>
            <td><strong>Test Jig Name:</strong></td>
            <td>' . $testJigName . '</td>
        </tr>
    </table>
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
                </tr>
            </thead>
            <tbody>';

// Add table rows with data
foreach ($tableData as $rowData) {
    $html .= '<tr>';
    foreach ($rowData as $cellData) {
        $html .= '<td>' . $cellData . '</td>';
    }
    $html .= '</tr>';
}

// Close HTML tags
$html .= '</tbody>
        </table>
    </div>
</div>
</body>
</html>';

// Create MPDF instance and generate PDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output();
