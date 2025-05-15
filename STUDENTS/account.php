<?php 
include('class/User.php');
$user = new User();
$user->loginStatus();
   // fetch logged-in student's basic user info
   $userDetail = $user->getUser_Student();
include('include/header.php');
?>
<title>Student Management System</title>
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Student Management System</h2>	
	<?php include('menu.php');?>
	<div class="table-responsive">		
		<div><span style="font-size:20px;">User Account Details:</span><div class="pull-right"><a href="edit_account.php">Edit Account</a></div>
		<table>		
		<tr>
			<td valign="top" >
				<img width='100' height='100' style="border:3px outset lightgrey;  padding:0px; margin:10px;" src="../SCHOOL/upload/<?php echo $userDetail['image']; ?>">
			</td>	
			<td>
				<table class="table table-boredered">			
					<tr>
						<th>Name</th>
						<td><?php echo $userDetail['first_name']." ".$userDetail['last_name']; ?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?php echo $userDetail['email']; ?></td>
					</tr>
					<tr>
						<th>Password</th>
						<td>**********</td>
					</tr>
					<tr>
						<th>Gender</th>
						<td><?php echo $userDetail['gender']; ?></td>
					</tr>
					<tr>
						<th>Mobile</th>
						<td><?php echo $userDetail['mobile']; ?></td>
					</tr>
            <tr>
                <th>Designation</th>
                <td><?php echo htmlspecialchars($userDetail['designation_name'] ?? $userDetail['designation'] ?? ''); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo htmlspecialchars($userDetail['status'] ?? ''); ?></td>
            </tr>		
            <!-- Student profile details -->
            <tr>
                <th>Admission No</th>
                <td><?php echo $userDetail['admission_no'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Roll No</th>
                <td><?php echo $userDetail['roll_no'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Academic Year</th>
                <td><?php echo $userDetail['academic_year'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Admission Date</th>
                <td><?php echo $userDetail['admission_date'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?php echo $userDetail['dob'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Class</th>
                <td><?php echo $userDetail['class_name'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Section</th>
                <td><?php echo $userDetail['section_name'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Current Address</th>
                <td><?php echo $userDetail['current_address'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Permanent Address</th>
                <td><?php echo $userDetail['permanent_address'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Father's Name</th>
                <td><?php echo $userDetail['father_name'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Father's Mobile</th>
                <td><?php echo $userDetail['father_mobile'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Father's Occupation</th>
                <td><?php echo $userDetail['father_occupation'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Mother's Name</th>
                <td><?php echo $userDetail['mother_name'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Mother's Mobile</th>
                <td><?php echo $userDetail['mother_mobile'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Stream</th>
                <td><?php echo $userDetail['stream'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Hostel</th>
                <td><?php echo $userDetail['hostel'] ?? ''; ?></td>
            </tr>
            <tr>
                <th>Category</th>
                <td><?php echo $userDetail['category'] ?? ''; ?></td>
            </tr>
            <!-- End student profile details -->
        </table>
			</td>
		</tr>
		</table>
	</div>	
</div>	
<?php include('include/footer.php');?>