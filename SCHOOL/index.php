																																																													<?php 
ob_start();
include('class/School.php');
if(!empty($_SESSION["adminUserid"])) {	
	header("location: dashboard.php"); 	
}
$school = new School();
$errorMessage =  $school->adminLogin();
include('inc/header.php');
?>
<title>School Management System</title>
<?php include('include_files.php');?>
<?php include('inc/container.php');?>
<div class="container">
	<h2 style="color:white;">&nbsp;&nbsp;School Information System</h2>	
	<div class="col-md-6">                    
		<div class="panel panel-info">
			<div class="panel-heading" style="background:#351c75; color:white;"> <!-- dark purple 1:#674ea7, dark purple 2:#351c75, dark purple 2:#351c75 --> 
				<div class="panel-title">Login</div>                        
			</div> 
			<div style="padding-top:30px" class="panel-body" >
				<?php if ($errorMessage != '') { ?>
					<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $errorMessage; ?></div>                            
				<?php } ?>
				<form id="loginform" class="form-horizontal" role="form" method="POST" action="">                                    
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
						<input type="text" class="form-control" id="email" name="email" placeholder="email" required>                                        
					</div>                                
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						<input type="password" class="form-control" id="password" name="password" placeholder="password" required>
					</div>
					<div style="margin-top:10px" class="form-group">                               
						<div class="col-sm-12 controls">
						  <input type="submit" name="login" value="Login" class="btn btn-success">						  
						</div>						
					</div>	
					<div style="margin-top:10px" class="form-group">                               
						<!--<div class="col-sm-12 controls">
						Admin: admin@webdamn.com<br>
						password:123	<br><br>									
						</div>-->						
					</div>	
				</form>   
			</div>                     
		</div>  
	</div>
</div>	
<?php include('inc/footer.php');?>
