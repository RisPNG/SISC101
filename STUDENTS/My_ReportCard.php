<?php
	include('class/User.php');
	$user = new User();
	$user->loginStatus();
	//$userStudent = $user->getUser_Student();	//formally userDetails
	$userReport = $user->getUser_Report();
	$userGrades = $user->getUser_Grades();
	if (!$userGrades) {
	    $userGrades = array(
	         'sc_homework'       => 0,
	         'sc_participation'  => 0,
	         'sc_exam'           => 0,
	         'sc_project'        => 0,
	         's2_exam'           => 0,
	         's3_exam'           => 0,
	    );
	}
	include('include/header.php');
?>
<title>Student Information System</title>

<script src="admin/js/jquery.dataTables.min.js"></script>
<script src="admin/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="admin/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php'); ?>
<?php
	//echo $_POST("selExam");
	$times = 0;
	
	$total = array(($userGrades['sc_homework'] ?? 0) + ($userGrades['sc_participation'] ?? 0) + ($userGrades['sc_exam'] ?? 0) + ($userGrades['sc_project'] ?? 0), ($userGrades['sc_homework'] ?? 0) + ($userGrades['sc_participation'] ?? 0) + ($userGrades['s2_exam'] ?? 0) + ($userGrades['sc_project'] ?? 0), ($userGrades['sc_homework'] ?? 0) + ($userGrades['sc_participation'] ?? 0) + ($userGrades['s3_exam'] ?? 0) + ($userGrades['sc_project'] ?? 0));
	$grade = array(" ", " ", " ");
	
	for ($i = 0; $i <= 2; $i++) {
		if (floatval($total[$i]) > 90) $grade[$i] = 'A+';
		else if (floatval($total[$i]) > 85) $grade[$i] = 'A';
		else if (floatval($total[$i]) > 80) $grade[$i] = 'A-';
		else if (floatval($total[$i]) > 75) $grade[$i] = 'B+';
		else if (floatval($total[$i]) > 70) $grade[$i] = 'B';
		else if (floatval($total[$i]) > 65) $grade[$i] = 'B-';
		else if (floatval($total[$i]) > 60) $grade[$i] = 'C+';
		else if (floatval($total[$i]) > 55) $grade[$i] = 'C';
		else if (floatval($total[$i]) > 50) $grade[$i] = 'C-';
		else if (floatval($total[$i]) > 0) $grade[$i] = 'F';
	}
	?>
<script src="js/My_Attendance.js"></script>
<script language="javascript">
	function printDiv(divName) {

		document.getElementById("btnPrint").style.display = "none";

		var htmlPrepBy = document.getElementById("spnPrepBy").innerHTML;
		var valPrepBy = document.getElementById("txtPrepBy").value;
		document.getElementById("spnPrepBy").innerHTML = valPrepBy;

		var htmlPrepDate = document.getElementById("spnPrepDate").innerHTML;
		var valPrepDate = document.getElementById("txtPrepDate").value;
		document.getElementById("spnPrepDate").innerHTML = valPrepDate;

		var htmlIntBy = document.getElementById("spnIntBy").innerHTML;
		var valIntBy = document.getElementById("txtIntBy").value;
		document.getElementById("spnIntBy").innerHTML = valIntBy;

		var htmlIntDate = document.getElementById("spnIntDate").innerHTML;
		var valIntDate = document.getElementById("txtIntDate").value;
		document.getElementById("spnIntDate").innerHTML = valIntDate;

		var printContents = document.getElementById(divName).innerHTML;
		var originalContents = document.body.innerHTML;

		document.body.innerHTML = printContents;

		window.print();

		document.body.innerHTML = originalContents;

		
document.getElementById("spnPrepBy").innerHTML = htmlPrepBy;
		document.getElementById("txtPrepBy").value = valPrepBy;

		document.getElementById("spnPrepDate").innerHTML = htmlPrepDate;
		document.getElementById("txtPrepDate").value = valPrepDate;

		document.getElementById("spnIntBy").innerHTML = htmlIntBy;
		document.getElementById("txtIntBy").value = valIntBy;

		document.getElementById("spnIntDate").innerHTML = htmlIntDate;
		document.getElementById("txtIntDate").value = valIntDate;

		document.getElementById("btnPrint").style.display = "";
	}

	var innerHTMLcache = "";
	var innerHTMLOnOff = 1;

	function showHideThis(e, c) {
		if (innerHTMLOnOff == 1) {
			innerHTMLcache = e.innerHTML;
			e.innerHTML = "<td colspan='" + c + "' style='font-size:1px'>&nbsp;</td>";
			innerHTMLOnOff = 0;
		} else {
			e.innerHTML = innerHTMLcache;
			innerHTMLOnOff = 1;
		}
	}
</script>
<div class="container-fluid contact">
	<h2>Student Information System</h2>
	<?php include 'menu.php'; ?>
	
	<?php //echo "~~~".($_POST["studentid"] ?? '')."~~~".($_POST["classid"] ?? '')."~~~".($_POST["sectionid"] ?? ''); 
	?>
	
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
		<form method="post" id="attnForm" action="My_ReportCard.php">
			<a href="#"><strong><span class="fa fa-dashboard"></span> My Report Card </strong></a>
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
				<table style"width:1200px; " border=" 0">
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
						<td rowspan="2" colspan="2" align="center"><img src="../IMG/logo.png" width="130" height="110"></td>
						<td colspan="4"></td>
						<td colspan="6" style="text-align:right">
							<h4><b>SBIT Training Academy (L02428)</b></h4>No.18A, Jalan 20/16A, Taman Paramount,<br>46300 Petaling Jaya, Selangor.<br><br>
						</td>
					</tr>
					<tr>
						<td rowspan="2" colspan="2"></td>
						<td rowspan="2" colspan="4" style="text-align:center">
							<h3><b>Student Result Slip<br>学生成绩单</b></h3>
						</td>
						<td rowspan="2" colspan="2"></td>
						<td colspan="2" align="center" bgcolor="lightgrey" style="border:1px solid black;"><b>Score&nbsp;(成绩)</b></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="center" style="border:1px solid black;"><b>&nbsp;<?php echo (!empty($total[$times]) ? $total[$times] : 0) . '%'; ?></b></td>
						<td align="center" style="border:1px solid black;"><b><?php echo $grade[$times]; ?></b></td>
					</tr>
					<tr>
						<td colspan="2">Course (课程)</td>
						<td style="width:5px">:</td>
						<td style="width:795px" colspan="7"><?php echo $userReport['programName'] ?? ''; ?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td colspan="2">Student (学生)</td>
						<td style="width:5px">:</td>
						<td style="width:795px" colspan="7"><?php echo $userReport['fullName'] ?? ''; ?></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td colspan="2">Intake (入学月份)</td>
						<td style="width:5px">:</td>
						<td style="width:495px" colspan="4"><?php echo $userReport['sSectionName'] ?? ''; ?></td>
						<td style="width:295px" colspan="2">Start Date (开始日期)
						<td style="width:5px">:</td>
						<td colspan="2"><?php echo $userReport['start_date'] ?? ''; ?></td>
					</tr>
					<tr>
						<td colspan="2">Student ID (学号)</td>
						<td style="width:5px">:</td>
						<td style="width:495px" colspan="4"><?php echo $userReport['admission_no'] ?? ''; ?></td>
						<td style="width:295px" colspan="2">End Date (结束日期)
						<td style="width:5px">:</td>
						<td colspan="2"><?php echo $userReport['end_date'] ?? ''; ?></td>
					</tr>
					<tr>
						<td rowspan="2" colspan="2">Trainer (导师)</td>
						<td rowspan="2" style="width:5px">:</td>
						<td rowspan="2" style="width:495px" colspan="4"><?php echo $userReport['teacherName'] ?? ''; ?></td>
						<td style="width:295px" colspan="2">Subject Code (科目码)
						<td style="width:5px">:</td>
						<td colspan="2"><?php echo $userReport['className'] ?? ''; ?></td>
					</tr>
					<tr>
						<td colspan="3"></td>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr style="height:40px">
						<td colspan="2">Subject (科目)</td>
						<td style="width:5px">:</td>
						<td id="sub" name="sub" style="width:895px" colspan="8"><?php echo $userReport['subjectName'] ?? ''; ?></td>
						<td>
					</tr>
				</table>
				<table style"width:1200px; " border=" 1">
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
						<td colspan="6"><b>TEST (测验)</b></td>
						<td colspan="6"><b>Score (成绩)</b></td>
					</tr>
					<tr align="center">
						<td bgcolor="#aaaaaa" colspan="4"><b>Final&nbsp;Test<br>(期末考)</b></td>
						<td bgcolor="#aaaaaa" colspan="1"><b>Pass<br>(及格)</b></td>
						<td bgcolor="#aaaaaa" colspan="1"><b>Full&nbsp;Mark<br>(满分)</b></td>
						<td bgcolor="#ffffff" colspan="2"><b>1st&nbsp;Test<br>(第一次测试)</b></td>
						<td bgcolor="#ffffff" colspan="2"><b>First&nbsp;Resit<br>(第一次重考)</b></td>
						<td bgcolor="#ffffff" colspan="2"><b>2nd&nbsp;Resit<br>(第二次重考)</b></td>
					</tr>
					<tr>
						<td rowspan="3" colspan="2">&nbsp;Assessment</td>
						<td colspan="2">&nbsp;Homework</td>
						<td align="center"><?php echo (( $userGrades['per_homework'] ?? 0) / 2); ?>%</td>
						<td align="center"><?php echo $userGrades['per_homework'] ?? 0; ?>%</td>
						<td align="center"><?php echo $userGrades['sc_homework'] ?? 0; ?>%</td>
						<td align="center"><?php if((($userGrades['sc_homework'] ?? 0)) < (($userGrades['per_homework'] ?? 0) / 2)) echo 'Fail';
									else if(!empty($userGrades['sc_homework'])) echo 'Pass'; ?></td>
						<td align="center" style="width:100px">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;Participation</td>
						<td align="center"><?php echo (($userGrades['per_participation'] ?? 0) / 2); ?>%</td>
						<td align="center"><?php echo $userGrades['per_participation'] ?? 0; ?>%</td>
						<td align="center"><?php echo $userGrades['sc_participation'] ?? 0; ?>%</td>
						<td align="center"><?php if((($userGrades['sc_participation'] ?? 0)) < (($userGrades['per_participation'] ?? 0) / 2)) echo 'Fail';
									else if(!empty($userGrades['sc_participation'])) echo 'Pass'; ?></td>
						<td align="center" style="width:100px">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
					</tr>
					<tr style="display:''" onclick="showHideThis(this,8);">
						<td colspan="2">&nbsp;Exam</td>
						<td align="center"><?php echo (($userGrades['per_exam'] ?? 0) / 2); ?>%</td>
						<td align="center"><?php echo $userGrades['per_exam'] ?? 0; ?>%</td>
						<td align="center"><?php echo $userGrades['sc_exam'] ?? 0; ?>%</td>
						<td align="center"><?php if((($userGrades['sc_exam'] ?? 0)) < (($userGrades['per_exam'] ?? 0) / 2)) echo 'Fail';
									else if(!empty($userGrades['sc_exam'])) echo 'Pass'; ?></td>
						<td align="center" style="width:100px"><?php echo $userGrades['s2_exam'] ?? 0; ?></td>
						<td align="center"><?php if(!empty($userGrades['s3_exam'])) { if(($userGrades['s2_exam'] ?? 0) < (($userGrades['per_exam'] ?? 0) / 2)) echo 'Fail'; else echo 'Pass'; } ?></td>
						<td align="center"><?php echo $userGrades['s3_exam'] ?? 0; ?></td>
						<td align="center"><?php if(!empty($userGrades['s3_exam'])) { if(($userGrades['s3_exam'] ?? 0) < (($userGrades['per_exam'] ?? 0) / 2)) echo 'Fail'; else echo 'Pass'; } ?></td>
					</tr>
					<tr style="display:''" onclick="showHideThis(this,12);">
						<td colspan="4">&nbsp;Project</td>
						<td align="center"><?php echo (($userGrades['per_project'] ?? 0) / 2); ?>%</td>
						<td align="center"><?php echo $userGrades['per_project'] ?? 0; ?>%</td>
						<td align="center"><?php echo $userGrades['sc_project'] ?? 0; ?>%</td>
						<td align="center"><?php if((($userGrades['sc_project'] ?? 0)) < (($userGrades['per_project'] ?? 0) / 2)) echo 'Fail';
									else if(!empty($userGrades['sc_project'])) echo 'Pass'; ?></td>
						<td align="center" style="width:100px">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
						<td align="center">-</td>
					</tr>
					<tr bgcolor="#aaaaaa" style="height:40px">
						<td colspan="6"><b>&nbsp;Total Result (总结果)</b></td>
						<td colspan="2" align="center"><b><?php echo (($times >= 0 && !empty($total[0])) ? ($total[0] . '%<br>' . $grade[0]) : ''); ?></b></td>
						<td colspan="2" align="center"><b><?php echo (($times >= 1 && !empty($total[1])) ? ($total[1] . '%<br>' . $grade[1]) : ''); ?></b></td>
						<td colspan="2" align="center"><b><?php echo (($times >= 2 && !empty($total[2])) ? ($total[2] . '%<br>' . $grade[2]) : ''); ?></b></td>
					</tr>
					<tr style="display:none">
						<td colspan="4"><b>&nbsp;Practical Test (实践测试)</b></td>
						<td colspan="8" align="center"><b></b></td>
					</tr>
				</table>
				<table style"width:1200px; " border=" 0">
					<tr>
						<th style="width:100px"></th>
						<th style="width:100px"></th>
						<th style="width:100px"></th>
						<th style="width:100px"></th>
						<th style="width:100px"></th>
						<th style="width:100px"></th>
					</tr>
