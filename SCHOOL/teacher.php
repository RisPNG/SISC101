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
<script src="js/teacher.js"></script>
<?php include('inc/container.php');?>
<div class="container">	
	<?php include('side-menu.php');	?>
	<div class="content">
		<div class="container-fluid">
			<div>   
				<a href="#"><strong><span class="ti-id-badge"></span> Teacher&nbsp;Section</strong></a>
				<hr>		
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-10">
							<h3 class="panel-title"></h3>
						</div>
						<div class="col-md-2" align="right">
							<button type="button" name="add" id="addTeacher" class="btn btn-success btn-xs">Add&nbsp;Teacher</button>
						</div>
					</div>
				</div>
				<table id="teacherList" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No.</th>
							<th>ID</th>
							<th>Teacher</th>	
							<th>Program</th>	
							<th>Class</th>	
							<th>Section</th>								
							<th></th>
							<th></th>							
						</tr>
					</thead>
				</table>
				
			</div>			
		</div>		
	</div>	
</div>	
<div id="teacherModal" class="modal fade">
	<div class="modal-dialog">
		<form method="post" id="teacherForm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-plus"></i> Add and assign teacher to program</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="teacher" class="control-label">Teacher*</label>
						<input type="text" class="form-control" id="teacher_name" name="teacher_name" placeholder="Teacher's Name" required>					
					</div>	
					<div class="form-group">
						<label for="program" class="control-label">Program*</label>	
						<select name="program_id" id="program_id" class="form-control" required>
							<option value="">Select Program</option>
							<?php echo $school->getProgramOptions(); //formerly getProgramList ?>	
						</select>
					</div>										
				</div>
				<div class="modal-footer">
					<input type="hidden" name="teacherid" id="teacherid" />
					<input type="hidden" name="action" id="action" value="updateTeacher" />
					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>