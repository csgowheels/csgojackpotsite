<?php
	include "connect.php";
	
	if(isset($_POST['submit']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$username = htmlspecialchars($username);
		$username = mysql_real_escape_string($username);
		$password = htmlspecialchars($password);
		$password = mysql_real_escape_string($password);
		
		$q = mysql_query("select * from admin where username='".$username."' and password ='".$password."' and isactive='1'");
	    
	    echo $q;
		$num_row = mysql_num_rows($q);
		if($num_row > 0)
		{
			$res = mysql_fetch_assoc($q);
			$myname = $res['name'];
			setcookie('username', $username, time()+60*60*24*365);
	        setcookie('password', $password, time()+60*60*24*365);
			
			//$_SESSION['login'] = 1;
			//$_SESSION['uid'] = $res['id'];
			//$_SESSION['uname'] = $res['name'];
			
			header("Location: index.php");
			exit;
		}
		else
		{
			setcookie('username', $username, false);
	        setcookie('password', md5($password), false);
			header("Location: login.php?msg=Invalid information");
			exit;
		}
	}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<title>CSGO Wheels Admin Panel</title>
		<style type="text/css" media="all">
				@import url("css/style.css");
				@import url("css/jquery.wysiwyg.css");
				@import url("css/facebox.css");
				@import url("css/visualize.css");
				@import url("css/date_input.css");
		    </style>
			
			<!--[if lt IE 8]><style type="text/css" media="all">@import url("css/ie.css");</style><![endif]-->
	</head>
	
	<body>
		<div id="hld">
			<div class="wrapper">		<!-- wrapper begins -->
				<div class="block small center login">
					<div class="block_head">
						<div class="bheadl"></div>
						<div class="bheadr"></div>
						<h2>Login</h2>
					</div>		<!-- .block_head ends -->
					
					<div class="block_content">
						<?php if($_REQUEST['msg'] != "") { ?><div class="message info"><p><?php echo $_REQUEST['msg']; ?></p></div><?php } ?>
						<form name="frm1" method="post" enctype="multipart/form-data">
							<p>
								<label>Username:</label> <br />
								<input name="username" type="text" class="text"  />
							</p>
							<p>
								<label>Password:</label> <br />
								<input name="password" type="password" class="text" />
							</p>
							<p>
								<input name="submit" type="submit" class="submit" value="Login" /> &nbsp; 
								<input name="a" value="login" type="hidden">
							</p>
						</form>
					</div>		<!-- .block_content ends -->
						
					<div class="bendl"></div>
					<div class="bendr"></div>
									
				</div>		<!-- .login ends -->
			</div>
		</div>
	</body>
</html>