<link href="../SMS/assets/css/themify-icons.css" rel="stylesheet">
<div id="top-nav" class="navbar navbar-inverse navbar-static-top" style="background:#c4e3f3;color:white;border-color:white;"> <!-- light blue:#c4e3f3, light cornflower blue 2:#a4c2f4, dark cornflower blue 3:#1c4587 -->
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Welcome</a><?php //echo $_SESSION['name']; ?>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#"><i class="fa fa-user-circle"></i> <?php echo $_SESSION["name"]; //$_COOKIE["loginId"]; //echo strtoupper($_SESSION['name']); ?> <span class="caret"></span></a>
                    <ul id="g-account-menu" class="dropdown-menu" role="menu">
                        <li><a href="account.php"><i class="fa fa-user-secret"></i><span class="ti-user"></span>&nbsp;&nbsp;My&nbsp;Profile</a></li>				
                    </ul>
                </li>
                <li><a href="logout.php"><i class="btn btn-primary fa fa-sign-out" style="font-size:10px"><b><span class="ti-power-off"></span>&nbsp;&nbsp;Log&nbsp;Out</b></i> </a></li>
            </ul>
        </div>
    </div>    
</div>
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
    <ul class="nav nav-pills nav-stacked" style="border-right:1px solid black">       
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="My_Info.php"><i class="fa fa-dashboard"></i> My Info</a></li>
        <li><a href="My_Class.php"><i class="fa fa-tags"></i> My Class</a></li>
		<li><a href="My_Attendance.php"><i class="fa fa-tags"></i> My Attendance</a></li>
        <li><a href="My_ReportCard.php"><i class="fa fa-tags"></i> My Report Card</a></li>
        <li><a href="gpa_calculator.php"><i class="fa fa-calculator"></i> GPA Calculator</a></li>
        <li><a href="pomodoro.php"><i class="fa fa-clock-o"></i> Pomodoro Timer</a></li>
		<!-- <li><a href="kai_exam.php"><i class="fa fa-tags"></i> My Exam</a></li> -->        
	</ul>
</div>
