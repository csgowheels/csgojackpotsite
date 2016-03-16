<?php
	include "connect.php";
	
	if($_GET['agree']==1)
        $_GET['offerid'] = htmlspecialchars($_GET['offerid']);
        $_GET['offerid'] = mysql_real_escape_string($_GET['offerid']);
	    if(isset($_GET['offerid']))
			mysql_query("UPDATE offercheck SET isChecked=1 WHERE offerid=".$_GET['offerid']);
		
	else
	    mysql_query("UPDATE offercheck SET forDelete=1 WHERE offerid=".$_GET['offerid']);
?>