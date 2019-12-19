<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel='shortcut icon' type='image/x-icon' href='/favicon.png' />
	<link rel="stylesheet" type="text/css" href="theme.css">
	<link rel="stylesheet" type="text/css" href="login.css">
	<link href="jquery-ui.css" rel="stylesheet">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<!--script src="js/dialog.js" type="text/javascript"></script-->
	<script src="js/functions.js"></script>
	<script src="js/RadarChart.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	
	<title>CUPS 1.0</title>
</head>

<body>

<?php
	$sname = $_GET['sname'];
?>

<input id="hiddenvariable" style="display: none;" value="<?php echo $sname; ?>">

<div class="sidebar-container">
  <div class="sidebar-logo">
  </div>
  <ul class="sidebar-navigation">
    <li class="header"></li>
    <li>
      <a class="navmenu" href="dashboard.php">
        <i class="" aria-hidden="true"></i>
		<img src="images/dashico_white.png" alt="dashboard" height="20" width="20"><span class="tooltiptext"></span>
      </a>
    </li>
    <li>
      <a class="navmenu" href="form.php">
        <i class="" aria-hidden="true"></i><img src="images/settingsico_white.png" alt="dashboard" height="20" width="20">
      </a>
    </li>
  </ul>
	<div class="logout">
		<ul class="sidebar-navigation">
			<li>
				<a class="navmenu" href="">
					<i class="" aria-hidden="true"></i><img src="images/logout_ico.png" alt="dashboard" height="20" width="20">
				</a>
			</li>
		</ul> 		
	</div>
</div>

<div class="content-container">

	<div class="content-header">
		<div id="titleVersion">CUPS v1.0-beta.1</div>
		<div class="dropdown">
			<button class="dropbtn">
			<?php 
				if ($sname <> "") {
					echo $sname;
				} else {
					echo "Site:";
				}
			?>
			  <i class="fa fa-caret-down"></i>
			</button>
			<div class="dropdown-content" id="droplist">
			</div>
		</div>
	</div> 