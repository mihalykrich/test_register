<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/x-icon" href="/tmp/prima_favicon_32x32.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">





</head>   
<body>

</br>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
				
					
						<h4><i class="fa fa-map-marker" aria-hidden="true"></i> Location Updater</h4>
					
							
								<form class="form-horizontal well" action="location_update.php" method="get">
									<fieldset>
										<div class="col-auto">
											  <label for="textInput">Serial Number</label>
											  <div class="input-group mb-2">
												<div class="input-group-prepend">
												  <div class="input-group-text"><i class="fa fa-qrcode" aria-hidden="true"></i></div>
												</div>
												<input id="serial" name="serial" type="text" placeholder="SN No." class="form-control" required="" autofocus>
											  </div>
											  <label for="textInput">Location</label>
											  <div class="input-group mb-2">
												<div class="input-group-prepend">
												  <div class="input-group-text"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
												</div>
												<input id="location" name="location" type="text" placeholder="Location" class="form-control" required="">
											  </div>
										</div></br>
											<div class="control-group">
												<div class="controls">
													<center>
													<button type="submit" id="submit" name="search" class="btn btn-primary button-loading" data-loading-text="Loading..."><i class="fa fa-refresh" aria-hidden="true"></i> Update Location</button>
													</center>
												</div>
											</div>
									</fieldset>
								</form>
							
					
				
			</div>
		</div>
	</div>

	

</body>
</html>