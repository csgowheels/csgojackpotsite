<?php
	include("connect.php");
	
	$delete = $_GET['delete'];
	$delete = htmlspecialchars($delete);
	$delete = mysql_real_escape_string($delete);
	
	if(isset($delete))
		mysql_query("DELETE FROM chat  WHERE chatid='".$delete."'");    
	
	header('Location: index.php'); // not sure if this will work because of echo?
?>