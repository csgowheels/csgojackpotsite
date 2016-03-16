<?php
	include("connect.php");
	
	// for referal
	if (isset($_GET['ref']))
    {
        
		$_SESSION['ref'] = $_GET['ref'];
    }
	require ('steamauth/steamauth.php');  
	include("generalfunction.php");
	// $_SESSION['steam_uptodate'] = false;
?>

<?php
    
	// Refreshing online_users table (for online users count, defined by session_id())
	mysql_query("REPLACE INTO online_users (user_id, time, ip) VALUES('" . session_id() . "', Now(), '" .$_SERVER["REMOTE_ADDR"] . "')")
		or die("Unable to write into database");
?>

<?php
	$tid = mysql_real_escape_string($_GET['id']);
	if($tid == "")
		$tid = "2";
	
	$check_tos_fn = checktos();
	$qP = "select * from pages where id=".$tid;
	$rsP = mysql_query($qP);
	$row = mysql_fetch_array($rsP);
	extract($row);
	$pagename=trim($pagename);
	$pgetitle=trim($pagetitle);
	$pgecontent=trim($pagecontent);
	
	function getpagename($pid)
	{
		$qPd = "select * from pages where id=".$pid;
		$rsPd = mysql_query($qPd);
		$rowd = mysql_fetch_array($rsPd);
		extract($rowd);
		$pagename_menu=trim($pagename);
		
		return $pagename_menu;
	}
?> 

<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"> 
		<!-- Optional theme -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
	
		<!-- Tweaks -->
		<link rel="stylesheet" href="css/mrborking-custom.css">
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
	    
		<!-- Latest compiled and minified JavaScript -->
		<!--<script src="js/jquery.min.js"></script> -->
		<!-- <script src="js/jquery.js"></script> -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
			ga('create', 'UA-48459897-5', 'auto');
			ga('send', 'pageview');
		</script>
		
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
	    
		<!-- for chat rules popup -->
		<style>
		    .black_overlay{
		        display: none;
		        position: absolute;
		        top: 0%;
		        left: 0%;
		        width: 100%;
		        height: 100%;
		        background-color: black;
		        z-index:1001;
		        -moz-opacity: 0.8;
		        opacity:.80;
		        filter: alpha(opacity=80);
		    }
		    .white_content {
		        display: none;
		        position: absolute;
		        top: 25%;
		        left: 25%;
		        width: 50%;
		        height: auto;
		        /*padding: 16px;*/
		        border: 10px solid white;
		        background-color: #3e444c;
				color:#ffffff;
		        z-index:1002;
		        overflow: auto;
		    }
		</style><!-- for chat rules popup end -->
		
	    <!-- for chat form submit -->
		<script type="text/javascript" src="js/jquery-1.js"></script>
		<script type="text/javascript" src="js/jquery.js"></script>
	    <script src="js/jquery.colorbox.js"></script>
	    <meta name="viewport" content="width=device-width,  user-scalable=no">
		<script src="https://cdn.rawgit.com/kimmobrunfeldt/progressbar.js/0.5.6/dist/progressbar.js"></script>
		<link rel="stylesheet" href="css/colorbox.css" />
		
	    <script type="text/javascript">
			$(function(){
	            	$(".iframe").colorbox({iframe:true, width:"750px", height:"360px"});
				$('#ff').form({
					success:function(data){
						$.messager.alert('Info', data, 'info');
					}
				});
			});
		</script>
	    <!-- for chat form submit end -->
	   
	    <!-- for wheel  -->
		<script type="text/javascript">
			$(document).ready(function() {
                
				<?php
                    /*
					if($check_tos_fn==0){
						echo '$.colorbox({href:"tos_popup?id=6", overlayClose:false, closeButton:false, width:"750px", height:"360px" });';
                        echo 'alert("asfasdf");';
                        }
                    else
                        echo 'console.log("asfasdf");';
                        */
				?>
			});    
		</script>
		<!-- <script type="text/javascript" src="js/wheel.js"></script> -->
		<script type="text/javascript" src="js/new_wheel/wheel.js"></script>
		<script type="text/javascript" src="js/new_wheel/wheelHandler.js"></script>
	    <!-- for wheel end -->

		<style type="text/css">
			ul.ppt {
				position: relative;
				padding-left:0 !important;
			}
            .progress {
                height: 7px;
                width:90%;
                margin-left:30px;
                
            }
            .progress > svg {
                
                display: block;
            }
			  .background{
                height: 19px;
				width:100%;
				background-color:#F5F5F5;
            }
			.ppt li {
				list-style-type: none;
				/*position: absolute;*/
				top: 0;
				left: 0;
				margin-top:9px;
				text-align:center;
			}
			.ppt img {
				border: 1px solid #e7e7e7;
				padding: 5px;
				background-color: #ececec;
			}
		</style>
	    
		<title>CSGO Wheels</title>
	</head>
	
	<body id='snow'>
        <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
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
						<img style="height:59px; margin-top: -20px;width:130px;" src="images/gif.gif">
					</a>
				
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
	                <ul id="drop-nav" class="nav navbar-nav navbar-right" style="padding-top:10px;padding-left:20px;">
	                <input type="image" id="mute" src="images/mute.png" onclick="soundChange()" width="40" style="display:none;margin-top:-4px;">
	                                 <input type="image" id="unmute"  src="images/unmute-128.png" onclick="soundChange()" width="33" style="display:none">
                    </ul>
	                
					<ul id="drop-nav" class="nav navbar-nav navbar-right" style="margin-right:50px;">
							<?php
								if(!isset($_SESSION['steamid'])) {
									echo "<li>";
									steamlogin(); //login button
									echo "</li>";	
								}  else {
									echo "<li >";
									include ('steamauth/userInfo.php');

									//Protected content
									echo '<img src="'.$steamprofile['avatar'].'" title="" alt="" />&nbsp;'; // Display their avatar!
									// echo '<div style="display: inline; padding-bottom: 22px; padding-left: 10px; vertical-align: middle;">'.$steamprofile['personaname'].'</div>';
									echo $steamprofile['personaname'];
                                    echo "<span id='PointContainer' style='margin-left:50px;'>Your points: 0P</span>";
									echo "<ul>";
									echo "<li><a href='profile.php'>Profile</a></li>";
								    echo "<li><a href='setting.php' >Settings</a></li>";
									echo "<li><a href='steamauth/logout.php'>Logout</a></li>";
									//echo "<li>".logoutbutton()."</li>";
									echo "</ul>";
									echo "</li>";
									//logoutbutton();
								}    
							?> 
					</ul>
				</div>
			</div>
		</div>
        
       
        
        
	 <div class="container" style="padding-top: 75px;">