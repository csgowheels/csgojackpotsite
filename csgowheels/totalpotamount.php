<?php
	include("connect.php");

	$q_totalcnt = "select Amount,itprice from tradeitems";
	$ar_totamount = mysql_query($q_totalcnt) or die(mysql_error());
	
	if(mysql_num_rows($ar_totamount)== 0 )
		echo '0';
	else {
		$tot = 0;
		while( $row_totamount = mysql_fetch_assoc($ar_totamount)){
			$itprice = str_replace("$","",$row_totamount['itprice']);
			$amount = $itprice * $row_totamount['Amount'];
			$tot = $tot + $amount;
		}
		echo $tot;
	}
?>