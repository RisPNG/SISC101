<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Student Information System - My Attendance</title>

<script src="admin/js/jquery.dataTables.min.js"></script>
<script src="admin/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="admin/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php'); ?>
<script src="js/My_Attendance.js"></script>

<div class="container-fluid contact">
    <h2>Student Information System</h2>
    <?php include 'menu.php'; ?>

    <div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
        <form method="post" id="attnForm" action="my_action.php"> <!-- Action updated for consistency, though JS handles it -->
            <a href="#"><strong><span class="fa fa-dashboard"></span> My Attendance</strong></a>
            <hr>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="panel-title">The following is the list of classes currently attending:</h3>
                    </div>
                    <div class="col-md-6" align="right">
                        <!-- Hidden fields for AJAX submission, populated by JS -->
                        <input type="hidden" name="studentid" id="studentid" value="">
                        <input type="hidden" name="classid" id="classid" value="">
                        <input type="hidden" name="sectionid" id="sectionid" value="">
                        <input type="hidden" name="attendancedate" id="attendancedate" value="">
                        <input type="hidden" name="attendancetime" id="attendancetime" value="">
                        <input type="hidden" name="attendancestatus" id="attendancestatus" value="">
                        <input type="hidden" name="checkedInOut" id="checkedInOut" value="">
                        <!-- This button is now clicked programmatically -->
                        <input type="hidden" name="action" value="addKelas"> <!-- Default action for this form -->
                        <input type="button" name="add" id="addKelas" style="display:none;" value="Save Attendance">
                    </div>
                </div>
            </div>
            <table id="kelasList" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th> <!-- Hidden -->
                        <th>Class</th>
                        <th>Session</th>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Scheduled Start</th>
                        <th>Checked In</th>   <!-- NEW -->
                        <th>Checked Out</th>  <!-- NEW -->
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </form>
    </div>

    <!-- User Modal (Edit User Profile - likely separate functionality) -->
    <div id="userModal" class="modal fade">
        <div class="modal-dialog">
            <form method="post" id="userForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="firstname" class="control-label">First Name*</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="control-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                        </div>
                        <div class="form-group" id="passwordSection">
                            <label for="password" class="control-label">Password*</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="gender" class="control-label">Gender</label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="male" value="male">Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="female" value="female">Female
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="mobile" class="control-label">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile">
                        </div>
                        <div class="form-group">
                            <label for="designation" class="control-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" placeholder="designation">
                        </div>
                        <div class="form-group">
                            <label for="status" class="control-label">Status</label>
                            <label class="radio-inline">
                                <input type="radio" name="status" id="active" value="active" required>Active
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="status" id="pending" value="pending" required>Pending
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="user_type" class="control-label">User Type</label>
                            <label class="radio-inline">
                                <input type="radio" name="user_type" id="general" value="general" required>General
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="user_type" id="administrator" value="administrator" required>Administrator
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="userid" id="userid" />
                        <input type="hidden" name="action" id="action_user_form" value="updateUser" /> <!-- Changed id to avoid conflict -->
                        <input type="submit" name="save" id="save_user_form" class="btn btn-info" value="Save" /> <!-- Changed id -->
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
<?php include('include/footer.php'); ?>