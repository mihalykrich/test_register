<!DOCTYPE html>
<?php 
	//include 'db.php';
 
?>

<html>
<head>
<link rel="icon" type="image/x-icon" href="/tmp/prima_favicon_32x32.png">
</head>
<body>

<link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<!-- Cascading Dropdown Script -->
<script>
var subjectObject = {
<?php echo file_get_contents( "dropdown_location.txt" ); ?>
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


</br>
<div id="wrap">
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<div class="card">
					<h5 class="card-header">
						<i class="fa fa-qrcode" aria-hidden="true"></i> Location QR Code 
					</h5>
							<div class="card-body">
								<form class="form-horizontal well" action="print_location.php" method="post">
									<fieldset>
										<div class="col-auto">
											  <label for="textInput">Department</label>
											  <div class="input-group mb-2">
												<div class="input-group-prepend">
												  <div class="input-group-text"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
												</div>
												<select id="department" name="department" class="form-control input-md" required="">
													<option value="" selected="selected">Select Department</option>
												</select>
											  </div>
											  
										</div>
										
										<div class="col-auto">
											  <label for="textInput">Location</label>
											  <div class="input-group mb-2">
												<div class="input-group-prepend">
												  <div class="input-group-text"><i class="fa fa-cogs" aria-hidden="true"></i></div>
												</div>
												<select id="location" name="location" class="form-control input-md" required="">
													<option value="" selected="selected">Select Location</option>
												</select>
											  </div>
											  
										</div>
										
									</fieldset>
									<div class="control-group">
										<div class="controls">
											<center>
												<button type="submit" name="print" value="Generate" class="btn btn-primary"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
												<button type="reset" name="reset" value="Reset" class="btn btn-danger"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</button>
											</center>
										</div>
									</div>
								</form>
							</div>
					<div class="card-footer">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
    
</body>
</html>
