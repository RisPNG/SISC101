<?php 
include('class/School.php');
$school = new School();
$school->adminLoginStatus();
include('inc/header.php');
?>
<title>School Management System</title>
<?php include('include_files.php');?>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/attendance_report.js"></script>
<style>
.dataTables_filter {
display: none; 
}
</style>
<?php include('inc/container.php');?>
<div class="container">	
	<?php include('side-menu.php');	?>
	<div class="content">
		<div class="container-fluid">
			<strong><span class="ti-bar-chart-alt"></span> Student Attendance Report</strong>
			<div class="row">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title"><i class="fa fa-search"></i> Select Criteria</h3>
						</div>
						<form id="form1" action="" method="post" accept-charset="utf-8">
							<div class="box-body">						
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="classid">Class</label><small class="req"> *</small>
											<select id="classid" name="classid" class="form-control" required>
												<option value="">Select</option>
												<?php echo $school->getClassOptions(); ?>												
											</select>
											<span class="text-danger"></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="sectionid">Section</label><small class="req"> *</small>
												<select name="sectionid" id="sectionid" class="form-control" required>
													<option value="">Select</option>
													<?php echo $school->getSectionOptions(); ?>
												</select>
											<span class="text-danger"></span>
										</div>
									</div> 	
									<div class="col-md-4">
										<div class="form-group">
											<label for="attendanceDate">Attendance Date</label><small class="req"> *</small>			
											<input type='text' class="form-control" id="attendanceDate" name="attendanceDate" value="" placeholder="yyyy-mm-dd" />
										</div>
									</div> 										
								</div>
							</div>
							<div class="box-footer">
								<button type="button" id="search" name="search" value="search" style="margin-bottom:10px;" class="btn btn-primary btn-sm  checkbox-toggle"><i class="fa fa-search"></i> Search</button> <br>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="row">					
				<form id="attendanceForm" method="post">					
					<table id="studentList" class="table table-bordered table-striped hidden">
						<thead>
							<tr>
								<th>No.</th>
								<th>Student ID.</th>
								<th>Admission No.</th.>
								<th>Roll No.</th>	
								<th>Name</th>
								<th>Attendance</th>													
							</tr>
						</thead>
					</table>					
				</form>
			</div>					
	</div>	                                
</div>	
<?php include('inc/footer.php');?>
