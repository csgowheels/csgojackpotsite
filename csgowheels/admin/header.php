<?php
include("connect.php");
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
} else {
    header('Location: login.php');
	exit;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>CSGO Wheels Admin Panel</title>
<style type="text/css" media="all">
		@import url("css/style.css");
		@import url("css/date_input.css");
    </style>
<script type="text/JavaScript" src="js/new-validate.js"></script> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!--[if lt IE 8]><style type="text/css" media="all">@import url("css/ie.css");</style><![endif]-->
</head>
<body>
	
	<div id="hld">
	
		<div class="wrapper">		<!-- wrapper begins -->
	
	
			
			<div id="header">
				<div class="hdrl"></div>
				<div class="hdrr"></div>
				
				<h1><a href="index.php">CSGO Wheels Admin Panel</a></h1>
				
				<ul id="nav">
					<li <?php if($pg == "history") { echo 'class="active"'; } ?>><a href="history.php">History</a></li>
                    <li <?php if($pg == "player") { echo 'class="active"'; } ?>><a href="player.php">Player</a></li>
                    <li <?php if($pg == "player") { echo 'class="active"'; } ?>><a href="items.php">Items</a></li>
                    <li <?php if($pg == "settings") { echo 'class="active"'; } ?>><a href="settings.php">Settings</a></li>
					<li <?php if($pg == "cms") {  echo 'class="active"'; } ?>><a href="cms.php">CMS</a></li>
                    <li <?php if($pg == "sponser") {  echo 'class="active"'; } ?>><a href="sponser.php">Sponser</a></li>
					<li <?php if($pg == "adminup") {  echo 'class="active"'; } ?>><a href="adminsettings.php">Admin Details</a></li>
                    <li <?php if($pg == "adminup") {  echo 'class="active"'; } ?>><a href="add-user.php">Add user</a></li>
				</ul>
				
				<p class="user"> <a href="index.php">Home</a> | <a href="logout.php">Logout</a></p>
			</div>		<!-- #header ends -->
			

















