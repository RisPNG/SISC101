<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Study Planner - Student Information System</title>

<link rel="stylesheet" href="css/style.css">
<?php include('include/container.php'); ?>
<script src="js/Study_Planner.js"></script>

<div class="container-fluid contact">
    <h2>Student Information System</h2>
    <?php include 'menu.php'; ?>

    <div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
        <a href="#"><strong><span class="fa fa-calendar-check-o"></span> Study Planner</strong></a>
        <hr>
        <p>Welcome to your personalized Study Planner! Here you can create, track, and manage your study sessions to achieve academic success.</p>
        <!-- Future UI for planning study sessions will go here -->
    </div>
</div>

<?php include('include/footer.php'); ?>