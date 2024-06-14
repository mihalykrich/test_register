<?php include 'auth.php'; ?>
<!DOCTYPE html>
<?php include 'db.php'; ?>
<!-- Navbar -->
<?php include('inc/navbar.php');?>

<html>
<head>
<link rel="icon" type="image/x-icon" href="/tmp/prima_favicon_32x32.png">
</head>
<body>

<link rel="stylesheet" href="https://unpkg.com/bootstrap-submenu@3.0.1/dist/css/bootstrap-submenu.css">


<script src="https://unpkg.com/bootstrap-submenu@3.0.1/dist/js/bootstrap-submenu.js" defer></script>

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
            var subjectSel = document.getElementById("customer");
            var topicSel = document.getElementById("partno"); 
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


	<script>
	$(document).ready(function() {
  var len = 0;
  var maxchar = 100;

  $( '#report' ).keyup(function(){
    len = this.value.length
    if(len > maxchar){
        return false;
    }
    else if (len > 0) {
        $( "#remainingC" ).html( "Remaining characters: " +( maxchar - len ) );
    }
    else {
        $( "#remainingC" ).html( "Remaining characters: " +( maxchar ) );
    }
  })
});
	</script>




<form method="post" action="print_repair.php">
</br>
<div class="container">
<div class="row">
		<div class="col-md-12">
			<div class="card">
				<h5 class="card-header"><i class="fa fa-tag" aria-hidden="true"></i>
					Unit Details
				</h5>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
								    <span class="badge badge-primary">
									MO/RO Number
									</span>
								</h5>
								<div class="card-body">
									<input id="jobNumber" name="jobNumber" type="text" placeholder="Job / RMA No." class="form-control input-md" required="" onkeyup="sync()">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
									<span class="badge badge-primary">
									Serial Number
									</span>
								</h5>
								<div class="card-body">
									<input id="serialno" name="serialno" type="text" placeholder="SN No." class="form-control input-md" required="">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
									<span class="badge badge-success">
									Customer
									</span>
								</h5>
								<div class="card-body">
									<select type="text" id="customer" name="customer" class="form-control input-md req" placeholder="Text input" onchange="GetDetail(this.value)" value="" required>
										<option value="" selected="selected">Select Customer</option></select>
								</div>
							</div>
						</div>
					</div>
					</br>
					<div class="row">
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
									<span class="badge badge-success">
									Product Name
									</span>
								</h5>
								<div class="card-body">
									<select type="text" id="partno" name="partno" class="form-control input-md req" placeholder="937-XXXX" required>
										<option value="" selected="selected">Please select Customer first</option></select>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
									<span class="badge badge-primary">
									Department
									</span>
								</h5>
								<div class="card-body">
									<input id="department" name="department" readonly="readonly" type="text" value="<?php include('fetch_department.php');?>" placeholder="<?php include('fetch_department.php');?>" class="form-control input-md" required="">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card">
								<h5 class="card-header">
									<span class="badge badge-danger">
									Date
									</span>
								</h5>
								<div class="card-body">
									<div class="input-group">
									<input class="form-control" type="date" id="date" name="date" placeholder="MM/DD/YYYY" type="text"/>
										<!--<input class="form-control" id="date" name="date" placeholder="MM/DD/YYYY" type="text"/>-->
									</div>
								</div>
							</div>
						</div>
					</div>
					</br>
					<div class="row">
						
				    </div>
					</br>
					<div class="row">
						<div class="col-md-6">
							<div class="card">
								<h6 class="card-header">
									Fault Description
								</h6>
								<div class="card-body">
									<textarea class="form-control" id="report" name="report" maxlength="100"></textarea>
									<span id='remainingC'></span>
								</div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="card">
								<h6 class="card-header">
									User
								</h6>
								<div class="card-body">
									
									<input id="username" name="username" type="text" readonly="readonly" placeholder="<?php echo htmlspecialchars($_SESSION['username']); ?>" class="form-control input-md" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">	  
								</div>
							</div>	
						</div>
						
						<div class="col-md-6">
						</br>
							<!--<button id="button1id" name="saveonly" class="btn btn-success" >Save Only</button>-->
							<button type="submit" name="print" value="Generate" class="btn btn-primary"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
							<button type="reset" name="reset" value="Reset" class="btn btn-danger"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</button>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>

</div>

<script>document.getElementById('date').value = new Date().toISOString().slice(0, 10);</script>

<script>
function sync()
{
  var n1 = document.getElementById('jobNumber');
  var n2 = document.getElementById('serialno');
  n2.value = n1.value;
}
</script>
    
</body>
</html>