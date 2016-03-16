<?php
	$url=file_get_contents("https://is.steam.rip/");
	
	if (strpos($url,'Web Inventories</td><td class="status"><span class="delayed">Delayed')!== false)
	    echo '2';
	else if(strpos($url,'Steam appears to be running just fine') !== false)
	    echo '1';
	else    
	    echo '0';
?>