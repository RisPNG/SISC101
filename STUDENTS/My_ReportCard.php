<?php 
include('class/User.php');
$user = new User();
$user->loginStatus();
//$userStudent = $user->getUser_Student();	//formally userDetails
$userReport = $user->getUser_Report();
$userGrades = $user->getUser_Grades();
include('include/header.php');
?>
<title>Student Information System</title>

<script src="admin/js/jquery.dataTables.min.js"></script>
<script src="admin/js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="admin/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php');?>
<?php 
	//echo $_POST("selExam");
	$times=0;
		
	
	$total = array($userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['sc_exam']+$userGrades['sc_project'],$userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['s2_exam']+$userGrades['sc_project'],$userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['s3_exam']+$userGrades['sc_project']);
	$grade = array(" "," "," ");

	//$total=$userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['sc_exam']; 
	for ($i=0; $i<=2; $i++)
	{
	if (floatval($total[$i])>90) $grade[$i]='A+'; 
	else if (floatval($total[$i])>85) $grade[$i]='A'; 
	else if (floatval($total[$i])>80) $grade[$i]='A-'; 
	else if (floatval($total[$i])>75) $grade[$i]='B+'; 
	else if (floatval($total[$i])>70) $grade[$i]='B'; 
	else if (floatval($total[$i])>65) $grade[$i]='B-'; 
	else if (floatval($total[$i])>60) $grade[$i]='C+'; 
	else if (floatval($total[$i])>55) $grade[$i]='C'; 
	else if (floatval($total[$i])>50) $grade[$i]='C-'; 
	else if (floatval($total[$i])>0) $grade[$i]='F'; 
	}
/*
	//$total2=$userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['s2_exam']; 
	if (floatval($total2)>90) $grade2='A+'; 
	else if (floatval($total2)>85) $grade2='A'; 
	else if (floatval($total2)>80) $grade2='A-'; 
	else if (floatval($total2)>75) $grade2='B+'; 
	else if (floatval($total2)>70) $grade2='B'; 
	else if (floatval($total2)>65) $grade2='B-'; 
	else if (floatval($total2)>60) $grade2='C+'; 
	else if (floatval($total2)>55) $grade2='C'; 
	else if (floatval($total2)>50) $grade2='C-'; 
	else if (floatval($total2)>0) $grade2='F'; 


	//$total3=$userGrades['sc_homework']+$userGrades['sc_participation']+$userGrades['s3_exam']; 
	if (floatval($total3)>90) $grade3='A+'; 
	else if (floatval($total3)>85) $grade3='A'; 
	else if (floatval($total3)>80) $grade3='A-'; 
	else if (floatval($total3)>75) $grade3='B+'; 
	else if (floatval($total3)>70) $grade3='B'; 
	else if (floatval($total3)>65) $grade3='B-'; 
	else if (floatval($total3)>60) $grade3='C+'; 
	else if (floatval($total3)>55) $grade3='C'; 
	else if (floatval($total3)>50) $grade3='C-'; 
	else if (floatval($total3)>0) $grade3='F'; 
*/	
?>
<script src="js/My_Attendance.js"></script>	
<script language="javascript">
function printDiv(divName) {
	
	document.getElementById("btnPrint").style.display="none";

	var htmlPrepBy=document.getElementById("spnPrepBy").innerHTML;
	var valPrepBy=document.getElementById("txtPrepBy").value;
	document.getElementById("spnPrepBy").innerHTML=valPrepBy;

	var htmlPrepDate=document.getElementById("spnPrepDate").innerHTML;
	var valPrepDate=document.getElementById("txtPrepDate").value;
	document.getElementById("spnPrepDate").innerHTML=valPrepDate;

	var htmlIntBy=document.getElementById("spnIntBy").innerHTML;
	var valIntBy=document.getElementById("txtIntBy").value;
	document.getElementById("spnIntBy").innerHTML=valIntBy;

	var htmlIntDate=document.getElementById("spnIntDate").innerHTML;
	var valIntDate=document.getElementById("txtIntDate").value;
	document.getElementById("spnIntDate").innerHTML=valIntDate;
	
		 var printContents = document.getElementById(divName).innerHTML;
		 var originalContents = document.body.innerHTML;

		 document.body.innerHTML = printContents;

		 window.print();

		 document.body.innerHTML = originalContents;

	 
	document.getElementById("spnPrepBy").innerHTML=htmlPrepBy;
	document.getElementById("txtPrepBy").value=valPrepBy;

	document.getElementById("spnPrepDate").innerHTML=htmlPrepDate;
	document.getElementById("txtPrepDate").value=valPrepDate;

	document.getElementById("spnIntBy").innerHTML=htmlIntBy;
	document.getElementById("txtIntBy").value=valIntBy;

	document.getElementById("spnIntDate").innerHTML=htmlIntDate;
	document.getElementById("txtIntDate").value=valIntDate;
	
	document.getElementById("btnPrint").style.display="";

	
}

var innerHTMLcache="";
var innerHTMLOnOff=1;
function showHideThis(e,c) {
	if (innerHTMLOnOff==1)
	{
		innerHTMLcache=e.innerHTML;
		e.innerHTML="<td colspan='"+c+"' style='font-size:1px'>&nbsp;</td>";
		innerHTMLOnOff=0;
		//e.style.display="none";
	}
	else
	{
		//alert(e.innerHTML);
		e.innerHTML=innerHTMLcache;
		innerHTMLOnOff=1;
		//e.style.display="";
	}
}
</script>
<div class="container-fluid contact">	
	<h2>Student Information System</h2>	
	<?php include 'menu.php'; ?>
	
	<?php //echo "~~~".$_POST["studentid"]."~~~".$_POST["classid"]."~~~".$_POST["sectionid"]; ?>
	
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
	<form method="post" id="attnForm" action="My_ReportCard.php">
		<a href="#"><strong><span class="fa fa-dashboard"></span> My Report Card 	</strong></a>
		<!--<input type="button" onclick="printDiv('printableArea')" value="Print" />-->
		<br>
		<br>
		Please select a Class :<select id="selClass" name="selClass" onchange="document.getElementById('sub').innerHTML='';this.form.submit();">
		<option value="0">---please select---</option>
		<?php
		echo $user->getSubjectOptions();
		?>
		<!--
		<option value="0">-</option>
		<option value="26">26</option>
		<option value="27">27</option>
		<option value="29">29</option>
		-->
		</select>
		<div style="display:none">
		<br>
		Number of times resit exam :<select id="selExam" name="selExam" onchange="this.form.submit();">
		<option value="0" selected>0</option>
		<option value="1">1</option>
		<option value="2">2</option>
		</select>
		</div>
		 <hr>	
		<div id="printableArea" style="font-family:'times new roman'">
		<table style"width:1200px; " border="0">
			<tr>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:5px"></th>
				<th style="width:195px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:195px"></th>
				<th style="width:5px"></th>
				<th style="width:100px"></th>
				<th style="width:100px; text-align:right"><a href="#" id="btnPrint" onclick="printDiv('printableArea');">[print]</a></th>
			</tr>
			<tr>
				<td rowspan="2" colspan="2" align="center"><img src="../IMG/logo.png" width="130" height="110"></td><td colspan="4"></td><td colspan="6" style="text-align:right"><h4><b>SBIT Training Academy (L02428)</b></h4>No.18A, Jalan 20/16A, Taman Paramount,<br>46300 Petaling Jaya, Selangor.<br><br></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="2"></td><td rowspan="2" colspan="4" style="text-align:center"><h3><b>Student Result Slip<br>学生成绩单</b></h3></td><td rowspan="2" colspan="2"></td><td colspan="2" align="center" bgcolor="lightgrey" style="border:1px solid black;"><b>Score&nbsp;(成绩)</b></td>
			</tr>
			<tr>
				<td colspan="2"></td><td align="center" style="border:1px solid black;"><b>&nbsp;<?php if (!empty($total[$times])) echo $total[$times].'%';?></b></td><td align="center" style="border:1px solid black;"><b><?php echo $grade[$times];?></b></td>
			</tr>			
			<tr>
				<td colspan="2">Course (课程)</td><td style="width:5px">:</td><td style="width:795px" colspan="7"><?php echo $userReport['programName']; ?></td><td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">Student (学生)</td><td style="width:5px">:</td><td style="width:795px" colspan="7"><?php echo $userReport['fullName']; ?></td><td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">Intake (入学月份)</td><td style="width:5px">:</td><td style="width:495px" colspan="4"><?php echo $userReport['sSectionName']; ?></td><td style="width:295px" colspan="2">Start Date (开始日期)<td style="width:5px">:</td><td colspan="2"><?php echo $userReport['start_date']; ?></td>
			</tr>
			<tr>
				<td colspan="2">Student ID (学号)</td><td style="width:5px">:</td><td style="width:495px" colspan="4"><?php echo $userReport['admission_no']; ?></td><td style="width:295px" colspan="2">End Date (结束日期)<td style="width:5px">:</td><td colspan="2"><?php echo $userReport['end_date']; ?></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="2">Trainer (导师)</td><td rowspan="2" style="width:5px">:</td><td rowspan="2" style="width:495px" colspan="4"><?php echo $userReport['teacherName']; ?></td><td style="width:295px" colspan="2">Subject Code (科目码)<td style="width:5px">:</td><td colspan="2"><?php echo $userReport['className']; ?></td>
			</tr>
			<tr>
				<td colspan="3"></td><td colspan="4">&nbsp;</td>
			</tr>
			<tr style="height:40px">
				<td colspan="2">Subject (科目)</td><td style="width:5px">:</td><td id="sub" name="sub" style="width:895px" colspan="8"><?php echo $userReport['subjectName']; ?></td><td>
			</tr>
		</table>
		<table style"width:1200px; " border="1">
			<tr>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
			</tr>		
			<tr bgcolor="#999999" align="center">
				<td colspan="6"><b>TEST (测验)</b></td><td colspan="6"><b>Score (成绩)</b></td>
			</tr>
			<tr align="center">
				<td bgcolor="#aaaaaa" colspan="4"><b>Final&nbsp;Test<br>(期末考)</b></td><td bgcolor="#aaaaaa" colspan="1"><b>Pass<br>(及格)</b></td><td bgcolor="#aaaaaa" colspan="1"><b>Full&nbsp;Mark<br>(满分)</b></td>
				<td bgcolor="#ffffff" colspan="2"><b>1st&nbsp;Test<br>(第一次测试)</b></td><td bgcolor="#ffffff" colspan="2"><b>First&nbsp;Resit<br>(第一次重考)</b></td><td bgcolor="#ffffff" colspan="2"><b>2nd&nbsp;Resit<br>(第二次重考)</b></td>
			</tr>
			<tr>
				<td rowspan="3" colspan="2">&nbsp;Assessment</td><td colspan="2">&nbsp;Homework</td><td align="center"><?php echo $userGrades['per_homework']/2;?>%</td><td align="center"><?php echo $userGrades['per_homework'];?>%</td><td align="center"><?php echo $userGrades['sc_homework'];?>%</td><td align="center"><?php if ($userGrades['sc_homework']<$userGrades['per_homework']/2) echo 'Fail'; else if(!empty($userGrades['sc_homework'])) echo 'Pass';?></td><td align="center" style="width:100px">-</td><td align="center">-</td><td align="center">-</td><td align="center">-</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;Participation</td><td align="center"><?php echo $userGrades['per_participation']/2;?>%</td><td align="center"><?php echo $userGrades['per_participation'];?>%</td><td align="center"><?php echo $userGrades['sc_participation'];?>%</td><td align="center"><?php if ($userGrades['sc_participation']<$userGrades['per_participation']/2) echo 'Fail'; else if(!empty($userGrades['sc_participation'])) echo 'Pass';?></td><td align="center" style="width:100px">-</td><td align="center">-</td><td align="center">-</td><td align="center">-</td>
			</tr>
			<tr style="display:''" onclick="showHideThis(this,8);">
				<td colspan="2">&nbsp;Exam</td><td align="center"><?php echo $userGrades['per_exam']/2;?>%</td><td align="center"><?php echo $userGrades['per_exam'];?>%</td><td align="center"><?php echo $userGrades['sc_exam'];?>%</td><td align="center"><?php if ($userGrades['sc_exam']<$userGrades['per_exam']/2) echo 'Fail'; else if(!empty($userGrades['sc_exam'])) echo 'Pass';?></td><td align="center" style="width:100px"><?php echo $userGrades['s2_exam'];?></td><td align="center"><?php if (!empty($userGrades['s3_exam'])) if ($userGrades['s2_exam']<$userGrades['per_exam']/2) echo 'Fail'; else if(!empty($userGrades['s2_exam'])) echo 'Pass';?></td><td align="center"><?php echo $userGrades['s3_exam'];?></td><td align="center"><?php if (!empty($userGrades['s3_exam'])) if ($userGrades['s3_exam']<$userGrades['per_exam']/2) echo 'Fail'; else if(!empty($userGrades['s3_exam'])) echo 'Pass';?></td>
			</tr>
			<tr style="display:''" onclick="showHideThis(this,12);">
				<td colspan="4">&nbsp;Project</td><td align="center"><?php echo $userGrades['per_project']/2;?>%</td><td align="center"><?php echo $userGrades['per_project'];?>%</td><td align="center"><?php echo $userGrades['sc_project'];?>%</td><td align="center"><?php if ($userGrades['sc_project']<$userGrades['per_project']/2) echo 'Fail'; else if(!empty($userGrades['sc_project'])) echo 'Pass';?></td><td align="center" style="width:100px">-</td><td align="center">-</td><td align="center">-</td><td align="center">-</td>
			</tr>
			<tr bgcolor="#aaaaaa" style="height:40px">
				<td colspan="6"><b>&nbsp;Total Result (总结果)</b></td><td colspan="2" align="center"><b><?php if ($times>=0 && !empty($total[0])) echo $total[0].'%<br>'.$grade[0];?></b></td><td colspan="2" align="center"><b><?php if ($times>=1 && !empty($total[1])) echo $total[1].'%<br>'.$grade[1];?></b></td><td colspan="2" align="center"><b><?php if ($times>=2 && !empty($total[2])) echo $total[2].'%<br>'.$grade[2];?></b></td>
			</tr>
			<tr style="display:none">
				<td colspan="4"><b>&nbsp;Practical Test (实践测试)</b></td><td colspan="8" align="center"><b></b></td>
			</tr>
		</table>
		<table style"width:1200px; " border="0">
			<tr>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
			</tr>	
			<tr>
				<td>&nbsp;Prepared by</td><td>:</td><td colspan="6"><span id="spnPrepBy"><input id='txtPrepBy' type='text' value=''></span></td><td colspan="4">Signed by</td>
			</tr>			
			<tr>
				<td>&nbsp;Date</td><td>:</td><td><span id="spnPrepDate"><input id='txtPrepDate' type='text' value=''></span><td colspan="6"></td><td rowspan="2" colspan="4"></td>
			</tr>			
			<tr>
				<td colspan="8">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td><td colspan="4" style="border-bottom:4px double black"></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td><td colspan="4"><b>Internal Assessor (内部审核员)</b></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td><td>Name :</td><td colspan="3"><span id="spnIntBy"><input id='txtIntBy' type='text' value=''></span></td>
			</tr>
			<tr>
				<td colspan="8">&nbsp;</td><td>Date :</td><td colspan="3"><span id="spnIntDate"><input id='txtIntDate' type='text' value=''></span></td>
			</tr>
		</table>
		<table style"width:1100px; " border="1">
			<tr>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
				<th style="width:100px"></th>
			</tr>				
			<tr bgcolor="#aaaaaa">
				<td colspan="11" style="text-align:center"><b>Grading Schema</b></td>					
			</tr>
			<tr>
				<td style="text-align:center"><b>Grade</b><td colspan="5" style="text-align:center"><b>Marks</b></td><td colspan="5" style="text-align:center"><b>Evidence</b></td>					
			</tr>
			<tr>
				<td style="text-align:center">A+<td colspan="5">&nbsp;90%-100%</td><td colspan="5">&nbsp;Distinction</td>					
			</tr>
			<tr>
				<td style="text-align:center">A<td colspan="5">&nbsp;85%-89%</td><td rowspan="2" colspan="5">&nbsp;Very Good</td>					
			</tr>
			<tr>
				<td style="text-align:center">A-<td colspan="5">&nbsp;80%-84%</td>				
			</tr>
			<tr>
				<td style="text-align:center">B+<td colspan="5">&nbsp;75%-79%</td><td rowspan="2" colspan="5">&nbsp;Good</td>					
			</tr>
			<tr>
				<td style="text-align:center">B<td colspan="5">&nbsp;70%-74%</td>				
			</tr>
			<tr>
				<td style="text-align:center">B-<td colspan="5">&nbsp;65%-69%</td><td rowspan="2" colspan="5">&nbsp;Satisactory</td>					
			</tr>
			<tr>
				<td style="text-align:center">C+<td colspan="5">&nbsp;60%-64%</td>				
			</tr>
			<tr>
				<td style="text-align:center">C<td colspan="5">&nbsp;55%-59%</td><td colspan="5">&nbsp;Weak</td>					
			</tr>
			<tr>
				<td style="text-align:center">C-<td colspan="5">&nbsp;50%-54%</td><td colspan="5">&nbsp;Pass</td>					
			</tr>
			<tr>
				<td style="text-align:center">F<td colspan="5">&nbsp;0%-49%</td><td colspan="5">&nbsp;Fail</td>					
			</tr>
			<tr>
				<td style="text-align:center">P<td colspan="5">&nbsp;Pass after trying more than one attempt</td><td colspan="5">&nbsp;Pass after trying more than one attempt</td>					
			</tr>
			<tr>
				<td style="text-align:center">*Pass<td colspan="5">&nbsp;Pass after trying more than one attempt</td><td colspan="5">&nbsp;Pass after trying more than one attempt</td>					
			</tr>
			<tr>
				<td style="text-align:center">N/A<td colspan="5">&nbsp;Not Available</td><td colspan="5">&nbsp;Not Available</td>					
			</tr>
		</table>
		</div>
<DIV style="display:none">
		<br><br>
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-6">
					<h3 class="panel-title">The following is the list of classes currently attending:</h3>
				</div>
				<div class="col-md-6" align="right">
					<input type="hidden" name="studentid" id="studentid" value="">
					<input type="hidden" name="classid" id="classid" value="">
					<input type="hidden" name="sectionid" id="sectionid" value="">
					<input type="hidden" name="attendancedate" id="attendancedate" value="">
					<input type="hidden" name="attendancetime" id="attendancetime" value="">
					<input type="hidden" name="attendancestatus" id="attendancestatus" value="">
					<input type="hidden" name="checkedInOut" id="checkedInOut" value="">
					<input type="hidden" name="add" id="addKelas" class="btn btn-success btn-xs" value="Save Attendance">
				</div>
			</div>
		</div>
		<table id="kelasList" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>No.</th>
					<th>ID</th>
					<th>Class</th>
					<th>Session</th>
					<th>Subject</th>
					<th>Section</th>
					<th>Teacher</th>
					<th>Status</th>			
					<th>Start&nbsp;Time</th>
					<th colspan="1">Attendance</th>
				</tr>
			</thead>
		</table>
	</form>
	</div>
	
	<div id="userModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="userForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
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
							<label for="lastname" class="control-label">Email</label>							
							<input type="text" class="form-control"  id="email" name="email" placeholder="Email">							
						</div>	 
						<div class="form-group" id="passwordSection">
							<label for="lastname" class="control-label">Password*</label>							
							<input type="password" class="form-control"  id="password" name="password" placeholder="Password" required>							
						</div>
						<div class="form-group">
							<label for="gender" class="control-label">Gender</label>							
							<label class="radio-inline">
								<input type="radio" name="gender" id="male" value="male">Male
							</label>;
							<label class="radio-inline">
								<input type="radio" name="gender" id="female" value="female">Female
							</label>							
						</div>	
						<div class="form-group">
							<label for="lastname" class="control-label">Mobile</label>							
							<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile">							
						</div>	 
						<div class="form-group">
							<label for="lastname" class="control-label">Designation</label>							
							<input type="text" class="form-control" id="designation" name="designation" placeholder="designation">							
						</div>	
						<div class="form-group">
							<label for="gender" class="control-label">Status</label>							
							<label class="radio-inline">
								<input type="radio" name="status" id="active" value="active" required>Active
							</label>;
							<label class="radio-inline">
								<input type="radio" name="status" id="pending" value="pending" required>Pending
							</label>							
						</div>
						<div class="form-group">
							<label for="user_type" class="control-label">User Type</label>							
							<label class="radio-inline">
								<input type="radio" name="user_type" id="general" value="general" required>General
							</label>;
							<label class="radio-inline">
								<input type="radio" name="user_type" id="administrator" value="administrator" required>Administrator
							</label>							
						</div>	
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="userid" id="userid" />
    					<input type="hidden" name="action" id="action" value="updateUser" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
</DIV>
</div>	
<?php include('include/footer.php');?>