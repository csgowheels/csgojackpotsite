<?php 
	include "connect.php";
	
	$steam_id_agree = mysql_real_escape_string($_REQUEST['steamid_agree']);
	$q_iagree = "update user set check_tos='1' where usteamid='".$steam_id_agree."'";
	
	if(mysql_query($q_iagree))
		return true;
	else
		return false;
?>