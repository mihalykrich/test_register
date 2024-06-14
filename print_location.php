<?php

 

require_once __DIR__ . '/vendor/autoload.php';
//include_once 'db.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/tmp'
]);



use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//The name of the directory that we need to create.
$directoryName = $_REQUEST['location'];
$target_dir = "location_labels/$directoryName";



//Check if the directory already exists.
if(!is_dir($directoryName)){
    //Directory does not exist, so lets create it.
    mkdir($target_dir, 0755);
}


$file_name = $target_dir.'/'.$_REQUEST['location'].'.pdf';

//$jb= $_REQUEST['jobNumber']??null;
$lc= $_REQUEST['location']??null;
//$pn= $_REQUEST['partno']??null;
//$un= $_REQUEST['username']??null;
//$rp= $_REQUEST['report']??null;
//$dt= $_REQUEST['date']??null;
$print= $_REQUEST['print']??null;
//$saveonly= $_REQUEST['saveonly']??null;


$qrCode = new QrCode($lc);

$output = new Output\Png();

$qrCodeContent = $output->output($qrCode, 105, [255, 255, 255], [0, 0, 0], 0);


$mpdf = new \Mpdf\Mpdf( [
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
			        <td colspan="2" style="text-align:center;"><h3>Location:<br>'.$lc.'</h3></td>
					<td colspan="2" style="text-align:center;"><img src="data:image/png;base64, ' . base64_encode($qrCodeContent).'"/></td>
				</tr>
		   </thead> 
           <tbody>
				<tr>
				<td colspan="4"><img src="data:image/png;base64, ' . base64_encode(file_get_contents("tmp/logo_small.png")).'"/></td>
		  <tr>
		  </tbody>
			<tfoot>
				<tr>
				   
				   <
				   
				   
				 </tr>
			</tfoot>
			
		
		</table>
	</body>
	
	
	
</html>';

$mpdf->WriteHTML($html);

if (file_exists($file_name)) {
	$cnt = 1;
	
	$newFileName =  'location_labels/'.$lc.'/'.pathinfo($file_name,PATHINFO_FILENAME).'_'.($cnt++).'.pdf';

	
	while (file_exists($newFileName)) {
		$newFileName =  'location_labels/'.$lc.'/'.pathinfo($file_name,PATHINFO_FILENAME).'_'.($cnt++).'.pdf';	
	}
	
	$file_name = $newFileName; 
	
}

if ($print) {
	$mpdf->Output($file_name);
	$mpdf->Output(basename($file_name),'D');
} else {
	$mpdf->Output($file_name);	
	header("Location: repair_await.php");
}