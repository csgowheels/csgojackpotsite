<?php
	include "connect.php";

	$t=time();    
	mysql_query("DELETE FROM mute WHERE time_entered<".$t);
	echo "DELETE FROM mute WHERE time_entered<".$t;
?>