<?php 
include('class/User.php');
$user = new User();
//$user->loginStatus();
$user->adminLoginStatus();
$message = '';
if(!empty($_POST["update"]) && $_POST["update"]) {
	$message = $user->editAccount();
}
//$userDetail = $user->userDetails();
$userDetail = $user->adminDetails();
include('include/header.php');
?>
<title>Admin Management System</title>
<?php include('include_files.php');?>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/classes.js"></script>
<?php include('include/container.php');?>
<div class="container">	
	<?php include('menus.php');	?>
	<div class="content">
		<div class="container-fluid">
			<!-- customization begin -->
			<div class="panel">
				<div class="panel-heading">
					<div class="panel-title">Edit Account Details</div>				
				</div>  
				<div class="panel-body col-md-7">
					<form class="form-horizontal" role="form" method="POST" action="">				
						<?php if($message != '') { ?>
							<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $message; ?></div>                            
						<?php } ?>	
						<div class="form-group">
							<label for="firstname" class="col-md-3 control-label">First Name*</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?php echo $userDetail['first_name'];?>" >
							</div>
						</div>
						<div class="form-group">
							<label for="lastname" class="col-md-3 control-label">Last Name</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php echo $userDetail['last_name'];?>" >
							</div>
						</div>					
						<div class="form-group">
							<label for="email" class="col-md-3 control-label">Email*</label>
							<div class="col-md-9">
								<input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $userDetail['email'];?>" required>
							</div>
						</div>	
						<div class="form-group">
							<label for="email" class="col-md-3 control-label">Mobile</label>
							<div class="col-md-9">
								<input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo $userDetail['mobile'];?>" >
							</div>
						</div>	
						<div class="form-group">
							<label for="lastname" class="col-md-3 control-label">Designation</label>
							<div class="col-md-9">
							<input type="text" class="form-control" name="designation" placeholder="Designation" value="<?php echo $userDetail['designation_name'];?>" readonly> 
							<!--
							<select name="designation" id="designation" class="form-control" required>
								<option value="">Select Designation</option>
								<?php //echo $user->getDesignationOptions(); ?>	
							</select>
							-->
							</div>
						</div>	
						<div class="form-group">
							<label for="gender" class="col-md-3 control-label">Gender</label>
							<div class="col-md-9">
								<label class="radio-inline">
									<input type="radio" name="gender" value="male" <?php if($userDetail['gender'] == 'male') { echo 'checked'; } ?> required>Male
								</label>;
								<label class="radio-inline">
									<input type="radio" name="gender" value="female" <?php if($userDetail['gender'] == 'female') { echo 'checked'; } ?> required>Female
								</label>
							</div>
						</div>	
						<div class="form-group">
							<label for="password" class="col-md-3 control-label">Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="passwd" placeholder="Password" value="">
							</div>
						</div>	
						<div class="form-group">
							<label for="password" class="col-md-3 control-label">Confirm Password</label>
							<div class="col-md-9">
								<input type="password" class="form-control" name="cpasswd" placeholder="Confirm Password" value="">
							</div>
						</div>						
						<div class="form-group">						                                  
							<div class="col-md-offset-3 col-md-9">
								<button id="btn-signup" type="submit" name="update" value="update_account" class="btn btn-info"><i class="icon-hand-right"></i> &nbsp Save Changes</button>			
							</div>
						</div>							
					</form>
				</div>
			</div>			
			<!-- customization end -->
		</div>		
	</div>	
</div>	
<?php include('include/footer.php');?>