<?php

require_once __DIR__ . '/vendor/autoload.php';
include_once 'db.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/tmp'
]);

$mpdf->keep_table_proportions = true;

use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//The name of the directory that we need to create.
$directoryName = $_REQUEST['serialno'];
$target_dir = "red_tag_labels/$directoryName";

//Check if the directory already exists.
if(!is_dir($directoryName)){
    //Directory does not exist, so lets create it.
    mkdir($target_dir, 0755);
}

$file_name = $target_dir.'/'.$_REQUEST['serialno'].'.pdf';

$jb= $_REQUEST['jobNumber']??null;
$sn= $_REQUEST['serialno']??null;
$cust= $_REQUEST['customer']??null;
$pn= $_REQUEST['partno']??null;
$un= $_REQUEST['username']??null;
$rp= $_REQUEST['report']??null;
$dp= $_REQUEST['department']??null;
$dt= $_REQUEST['date']??null;
$print= $_REQUEST['print']??null;
$saveonly= $_REQUEST['saveonly']??null;

$qrCode = new QrCode($sn);

$output = new Output\Png();

$qrCodeContent = $output->output($qrCode, 70, [255, 255, 255], [0, 0, 0], 0);

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [50, 100],
    'orientation' => 'L',
    'margin_left' => 2,
    'margin_right' => 2,
    'margin_top' => 2,
    'margin_bottom' => 1,
    'margin_header' => 1,
    'margin_footer' => 1
]);

$html ='<html>
<style>
table {
  font-family: arial, sans-serif; font-weight: bold; font-size: 15px;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 2px solid #dddddd;
  text-align: left;
  padding: 8px;
}

</style>
</head>
<body>
    <table width="100%">
        <thead>
            <tr>
                <td style="text-align:center;"><h4>MO/RO No.:'.$jb.'</h4></td>
                <td style="text-align:center;"><h4>Sn.:'.$sn.'</h4></td>
                <td style="text-align:center;"><h4>PN.:'.$pn.'</h4></td>
                <td style="text-align:center;"><img src="data:image/png;base64, ' . base64_encode($qrCodeContent).'"/></td>
            </tr>
       </thead>
       <tbody>
            <tr>
                <td colspan="4" style="text-align:center; padding-bottom: 75px;"><h4>Reason:'.$rp.'</h4></td>
            </tr>
       </tbody>
        <tfoot>
            <tr>
               <td><img src="data:image/png;base64, ' . base64_encode(file_get_contents("tmp/OSIS.png")).'" width="75" height="22"/></td>
               <td style="text-align:center;">Date:'.$dt.'</td>
               <td style="text-align:center;">Stage:'.$dp.'</td>
               <td style="text-align:center;">User:'.$un.'</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>';

$mpdf->WriteHTML($html);

if (file_exists($file_name)) {
    $cnt = 1;
    $newFileName = 'red_tag_labels/'.$sn.'/'.pathinfo($file_name, PATHINFO_FILENAME).'_'.($cnt++).'.pdf';
    while (file_exists($newFileName)) {
        $newFileName = 'red_tag_labels/'.$sn.'/'.pathinfo($file_name, PATHINFO_FILENAME).'_'.($cnt++).'.pdf';
    }
    $file_name = $newFileName;
}

if ($print) {
    $mpdf->Output($file_name);
    $mpdf->Output(basename($file_name),'D');
} else {
    $mpdf->Output($file_name);
    header("Location: index.php");
}

// Insert data into the database
$sql = "INSERT INTO job_details (jobNumber, serialno, customer, partno, department, date, report, username)
VALUES ('$jb', '$sn', '$cust', '$pn', '$dp', '$dt', '$rp', '$un')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>
