<?php
	include("connect.php");
	require ('steamauth/steamauth.php');  
	include("generalfunction.php");
	 //$_SESSION['steam_uptodate'] = false;
?>
<?php
	// Refreshing online_users table (for online users count, defined by session_id())
	mysql_query("REPLACE INTO online_users (user_id, time, ip) VALUES('" . session_id() . "', Now(), '" .$_SERVER["REMOTE_ADDR"] . "')")
		or die("Unable to write into database");
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"> 
		<!-- Optional theme -->
		<link rel="stylesheet" href="css/bootstrap.min.css">

		<!-- popup -->
		<script type="text/javascript" src="js/jquery-1.js"></script>
		<script type="text/javascript" src="js/jquery.js"></script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
			ga('create', 'UA-48459897-5', 'auto');
			ga('send', 'pageview');
		</script>

		<script src="js/jquery.colorbox.js"></script>
		<link rel="stylesheet" href="css/colorbox.css" />
	 	<!-- popup end -->
	
		<!-- Tweaks -->
		<link rel="stylesheet" href="css/mrborking-custom.css">
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
    
	    <!-- Latest compiled and minified JavaScript -->
		<!-- <script src="js/jquery.js"></script> -->
  
		<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> -->
		<style type="text/css">
		    #drop-nav ul {list-style: none;padding: 0px;margin: 0px;}
		    #drop-nav ul li {display: block;position: relative;float: left;border:1px solid #000}
		    #drop-nav li ul {display: none;}
		    #drop-nav li:hover ul {display: block; position: absolute; width: 150px;}
		    #drop-nav li:hover li {float: none; background-color: #272b30; padding: 10px;}
            #inventory:hover {box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 3px rgba(255, 0, 0, 0.6);}
            #support:hover {box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 3px rgba(255, 0, 0, 0.6);}
            #AboutUs:hover {box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 3px rgba(255, 0, 0, 0.6);}
            #howtoplay:hover {box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 3px rgba(255, 0, 0, 0.6);}
            #home:hover {box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 3px rgba(255, 0, 0, 0.6);}
		    #drop-nav li ul li {border-top: 0px;}
	    </style> 
    
		<script type="text/javascript">
			window.onload = function() {
				$(".iframe").colorbox({iframe:true, width:"750px", height:"360px"});
			}
		</script>
    
		<title>CSGO Wheels</title>
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<!-- <a href="../" class="navbar-brand"><img src="http://dev.mrborking.com/csgowheels/img/CSGOWheels_logo_55px.png" alt="CSGO Wheels" /></a> -->
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" rel="home" href="index.php" title="">
						<img style="height:59px; margin-top: -20px;width:130px;" src="images/gif.gif"></a>
				
				</div>
				<div class="navbar-collapse collapse" id="navbar-main">
					<ul class="nav navbar-nav">
						<li id="home" >
							<a href="index.php">Home</a>
						</li>
	                    <?php
						if(isset($_SESSION['steamid']) && checktradeurl() == "1")
	                        echo'
						<li id="inventory">
							<a href="inventory.php">Deposit</a>
						</li>';
	                    ?>    
                       
                         <?php
		                    
		                        echo'
							<li id="support">
							<a href="shop">Withdraw</a>
						</li>';
	                    ?>    
	                
						<li id="AboutUs"  >
							<a href="about-us.php">About Us</a>
						</li>
	                    <li id="howtoplay">
							<a href="how-to-play.php">How to Play</a>
						</li>
                         <li id="support">
							<a href="refer-a-friend">Referral System</a>
						</li>
	                    <li id="support" style="display:none;">
							<a href="support">Support</a>
						 </li>
	                </ul>
					<ul id="drop-nav" class="nav navbar-nav navbar-right" style="padding-top:14px;">
							<li style="min-width: 200px;">
							<?php
								if(!isset($_SESSION['steamid'])) {
									steamlogin(); //login button
								}  else {
									include ('steamauth/userInfo.php');
								
									//Protected content
									echo '<img src="'.$steamprofile['avatar'].'" title="" alt="" />&nbsp;'; // Display their avatar!
									echo $steamprofile['personaname'] ;
									echo "<ul>";
									echo "<li><a href='profile.php'>Profile</a></li>";
									echo "<li><a href='setting.php' >Settings</a></li>";
									echo "<li><a href='steamauth/logout.php'>Logout</a></li>";
									// echo "<li>".logoutbutton()."</li>";
									echo "</ul>";
									//logoutbutton();
								}    
							?> 
						</li>
					</ul>
				</div>
			</div>
		</div>
	        
	 <div class="container" style="padding-top: 75px;">
		
        
       