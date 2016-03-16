<?php

	include "connect.php";
	
	$x = mysql_fetch_assoc(mysql_query("SELECT * FROM user WHERE usteamid='".$_SESSION['steamid']."'"));
	$total_points=$x['points'];

    echo $total_points;
?>