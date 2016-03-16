<?php
	include "connect.php";
	
	mysql_query("REPLACE INTO online_users (user_id, time, ip) VALUES('" . session_id() . "', Now(), '" .$_SERVER["REMOTE_ADDR"] . "')")
		or die("Unable to write into database");
	mysql_query("DELETE FROM online_users WHERE TIMESTAMPDIFF(MINUTE, time, Now()) > 1") or die("Unable to delete.");
		
	$rows = mysql_query("SELECT * FROM online_users");
    if(mysql_num_rows($rows)<5)
		$online_users = mysql_num_rows($rows)+5;
    else
    	$online_users = mysql_num_rows($rows)+4;    
?>