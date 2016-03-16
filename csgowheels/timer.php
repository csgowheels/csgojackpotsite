<?php
	include "connect.php";
	
	$time=time();
	$x=mysql_fetch_assoc(mysql_query("SELECT * FROM timer_start WHERE id=0"));
	$result=$time-$x['time_started'];
	$result=60-$result;
	
	echo $result;

   
?>

