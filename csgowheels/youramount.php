<?php
	include "connect.php";
	
	if($_SESSION['steamid'] == "")
		$ctot = "0";
	else
	{
		$q_mytotalcnt = "select TradeOfferId from trade where partnerId64 ='".$_SESSION['steamid']."'";
		$ar_mytotamount = mysql_query($q_mytotalcnt) or die(mysql_error());
		$ctot = 0;
		if(mysql_num_rows($ar_mytotamount)== 0 )
			$ctot = $ctot + 0;
		else {
			while($row_mytotamount = mysql_fetch_assoc($ar_mytotamount)){
				$trid = $row_mytotamount['TradeOfferId'];
				$q_ctotalcnt = "select totamt from tradeitems where tradeid='".$trid."' and roundid='".$round_id_player."'";
				$ar_ctotamount = mysql_query($q_ctotalcnt) or die(mysql_error());
				if(mysql_num_rows($ar_ctotamount)== 0 )
					$ctot = $ctot + 0;
				else {
					while( $row_ctotamount = mysql_fetch_assoc($ar_ctotamount)){
						$camount =  $row_ctotamount['totamt'];
						$ctot = $ctot + $camount;
					}
				}
			}
		}
	}
	
	$ctot = round($ctot,2);
	
	//get your chance to win.
	if($_SESSION['steamid'] != "")
	{
		$q_your_chance = "SELECT sum(tickettot)/(select sum(tickettot) from tradeitems where roundid='".$round_id_player."') as percentage FROM tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid where partnerId64 = '".$_SESSION['steamid']."' and roundid='".$round_id_player."' GROUP BY tradeid"; 
		$r_your_chance = mysql_query($q_your_chance) or die(mysql_error());
		$row_chance_to_win = mysql_fetch_array($r_your_chance);
		$chance_to_win = ($row_chance_to_win["0"]*100)."%";
	}
	else
		$chance_to_win = "0%";
?>