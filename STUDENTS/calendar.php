<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Student Management System - Holiday Calendar</title>
<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php');?>
<div class="container contact">
    <h2>Malaysia Public Holiday Calendar</h2>
    <?php include 'menu.php';?>
    <div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
        <p>This calendar displays Malaysia's public holidays. Data sourced from Google Calendar.</p>
        <div style="text-align:center;">
            <iframe src="https://calendar.google.com/calendar/embed?src=en.malaysia%23holiday%40group.v.calendar.google.com&ctz=Asia/Kuala_Lumpur" style="border: 0" width="90%" height="600" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
</div>
<?php include('include/footer.php');?>