<?php 
	include('class/User.php');
	$user = new User();
	$user->loginStatus();
	$message = '';
	$message = $user->editAccount();
	$userDetail = $user->getUser_Student();	// formerly getUser()
	include('include/header.php');
?>
<title>Student Management System</title>
<?php include('include/container.php');?>
<div class="container contact"> 
	<h2>Student Management System</h2> 
	<?php include('menu.php');?> 				
	<div>
		<div class="panel">
			<div class="panel-heading">
				<div class="panel-title">Edit Account Details</div>                
			</div>  
			<div class="panel-body col-md-7">
				<form class="form-horizontal" role="form" method="POST" action="" enctype="multipart/form-data">                
					<?php if($message != '') { ?>
						<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $message; ?></div>                            
					<?php } ?>
					<div class="form-group">
						<label for="firstname" class="col-md-3 control-label">First Name*</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?php echo $userDetail['first_name'] ?? ''; ?>" >
						</div>
					</div>
					<div class="form-group">
						<label for="lastname" class="col-md-3 control-label">Last Name</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php echo $userDetail['last_name'] ?? ''; ?>" >
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-md-3 control-label">Email*</label>
						<div class="col-md-9">
							<input type="email" class="form-control" name="email" placeholder="Email Address" value="<?php echo $userDetail['email'] ?? ''; ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-md-3 control-label">Mobile</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo $userDetail['mobile'] ?? ''; ?>" >
						</div>
					</div>
                <div class="form-group">
                    <label for="lastname" class="col-md-3 control-label">Designation</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="designationName" placeholder="Designation" value="<?php echo htmlspecialchars($userDetail['designation_name'] ?? $userDetail['designation'] ?? ''); ?>" readonly>
                        <input type="hidden" class="form-control" name="designation" value="<?php echo htmlspecialchars($userDetail['designation_id'] ?? $userDetail['designation'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="status" class="col-md-3 control-label">Status</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="status" value="<?php echo htmlspecialchars($userDetail['status'] ?? ''); ?>" readonly>
                    </div>
                </div>
					<div class="form-group">
						<label for="gender" class="col-md-3 control-label">Gender</label>
						<div class="col-md-9">
							<label class="radio-inline">
								<input type="radio" name="gender" value="male" <?php if(($userDetail['gender'] ?? '') == 'male') { echo 'checked'; } ?> required>Male
							</label>;
							<label class="radio-inline">
								<input type="radio" name="gender" value="female" <?php if(($userDetail['gender'] ?? '') == 'female') { echo 'checked'; } ?> required>Female
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
						<label for="photo" class="col-md-3 control-label">Photo</label>
						<div class="col-md-9">
							<input type="file" class="form-control" id="photo" name="photo" placeholder="Teacher photo">
						</div>
					</div>
					<div class="form-group">                                          
            <!-- Student academic and profile fields -->
            <div class="form-group">
                <label for="admission_no" class="col-md-3 control-label">Admission No</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="admission_no" placeholder="Admission No" value="<?php echo $userDetail['admission_no'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="roll_no" class="col-md-3 control-label">Roll No</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="roll_no" placeholder="Roll No" value="<?php echo $userDetail['roll_no'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="academic_year" class="col-md-3 control-label">Academic Year</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="academic_year" placeholder="Academic Year" value="<?php echo $userDetail['academic_year'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="admission_date" class="col-md-3 control-label">Admission Date</label>
                <div class="col-md-9">
                    <input type="date" class="form-control" name="admission_date" value="<?php echo $userDetail['admission_date'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="dob" class="col-md-3 control-label">Date of Birth</label>
                <div class="col-md-9">
                    <input type="date" class="form-control" name="dob" value="<?php echo $userDetail['dob'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="class_name" class="col-md-3 control-label">Class</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="class_name" value="<?php echo $userDetail['class_name'] ?? ''; ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="section_name" class="col-md-3 control-label">Section</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="section_name" value="<?php echo $userDetail['section_name'] ?? ''; ?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="current_address" class="col-md-3 control-label">Current Address</label>
                <div class="col-md-9">
                    <textarea class="form-control" name="current_address" placeholder="Current Address"><?php echo $userDetail['current_address'] ?? ''; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="permanent_address" class="col-md-3 control-label">Permanent Address</label>
                <div class="col-md-9">
                    <textarea class="form-control" name="permanent_address" placeholder="Permanent Address"><?php echo $userDetail['permanent_address'] ?? ''; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="father_name" class="col-md-3 control-label">Father's Name</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="father_name" placeholder="Father's Name" value="<?php echo $userDetail['father_name'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="father_mobile" class="col-md-3 control-label">Father's Mobile</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="father_mobile" placeholder="Father's Mobile" value="<?php echo $userDetail['father_mobile'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="father_occupation" class="col-md-3 control-label">Father's Occupation</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="father_occupation" placeholder="Father's Occupation" value="<?php echo $userDetail['father_occupation'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="mother_name" class="col-md-3 control-label">Mother's Name</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="mother_name" placeholder="Mother's Name" value="<?php echo $userDetail['mother_name'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="mother_mobile" class="col-md-3 control-label">Mother's Mobile</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="mother_mobile" placeholder="Mother's Mobile" value="<?php echo $userDetail['mother_mobile'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="stream" class="col-md-3 control-label">Stream</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="stream" placeholder="Stream" value="<?php echo $userDetail['stream'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="hostel" class="col-md-3 control-label">Hostel</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="hostel" placeholder="Hostel" value="<?php echo $userDetail['hostel'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="category" class="col-md-3 control-label">Category</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="category" placeholder="Category" value="<?php echo $userDetail['category'] ?? ''; ?>">
                </div>
            </div>
            <!-- End student profile fields -->
            <div class="col-md-offset-3 col-md-9">
							<button id="btn-signup" type="submit" name="update" value="update_account" class="btn btn-info"><i class="icon-hand-right"></i> &nbsp Save Changes</button>            
						</div>
					</div>                           
				</form>
			 </div>
		</div>
	</div> 
	<?php include('include/footer.php');?>
