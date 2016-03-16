
<html>
	<head>
		<title> User registration | CSGOWheels.com </title>
	</head>
	<body>
		<form action="user_registration" method="post">
			<p>Enter your transaction code (5 characters at least)</p>
			<input type="text" id="password" name="password"  />
			<input type="submit" name="submit" value="submit" />
		</form>
	</body>
</html>

<?php

	// if (!isset($_SESSION['steamid']))
	// {
		// header("Location: https://csgowheels.com");
		// die();
	// }
	
	
	if (isset($_POST["password"]))
	{
        echo "Password".$_POST["password"];
		$password = mysql_real_escape_string($_POST['password']);
		
		include "password_hashing.php";
		
		$password = PasswordHasher::new_password($password);
		echo 'UserPassword: ' . $password . '<br />';
	}

?>
<<<<<<< HEAD
=======

<html>
	<head>
		<title> User registration | CSGOWheels.com </title>
	</head>
	<body>
		<form action="user_registration" method="post">
			<p>Enter your transaction code (5 characters at least)</p>
			<input type="password" id="password" name="password" minlength="5" maxlength="20" required />
			<input type="submit" name="submit" value="submit" />
		</form>
	</body>
</html>
>>>>>>> dbbd0208b1cbb391636a9d0600ae627a9f645170
