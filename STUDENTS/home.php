<?php 
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Student Management System</title>
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php');?>
<div class="container contact">	
	<h2>Student Management System</h2>	
	<?php include 'menu.php'; ?>
<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">   
		<a href="#"><strong><span class="fa fa-dashboard"></span> Home</strong></a>
		<hr>		
		<div class="row">
			<div class="col-md-12">
				<div class="row">
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body bk-primary text-light">
                            <div class="stat-panel text-center">
                                <div class="stat-panel-number h1 "><?php echo $user->getClassCount(); ?></div>
                                <div class="stat-panel-title text-uppercase">Classes</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body bk-success text-light">
                            <div class="stat-panel text-center">
                                <div class="stat-panel-number h1 "><?php echo $user->getAttendanceCount(); ?></div>
                                <div class="stat-panel-title text-uppercase">Attendance</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body bk-info text-light">
                            <div class="stat-panel text-center">
                                <div class="stat-panel-number h1 "><?php echo $user->getExamCount(); ?></div>
                                <div class="stat-panel-title text-uppercase">Exams</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body bk-danger text-light">
                            <div class="stat-panel text-center">
                                <div class="stat-panel-number h1 "><?php echo $user->getProjectCount(); ?></div>
                                <div class="stat-panel-title text-uppercase">Projects</div>
                            </div>
                        </div>
                    </div>
                </div>
				</div>
			</div>
		</div>		
	</div>
</div>	
<?php include('include/footer.php');?>