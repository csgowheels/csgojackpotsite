<?php
	include "connect.php";
	
	if($_SESSION['steamid'] == "")
		echo "You are not logged in. PLease login first";
	else
	{
		$q_mytotalcnt = "SELECT roundid,start_date,winnersteamid,(select sum(totamt) from tradeitems where tradeitems.roundid=round.roundid) as potv FROM round where round.roundid in (select roundid from tradeitems where tradeitems.tradeid in ( select TradeOfferId from trade where trade.partnerId64 = '".$_SESSION['steamid']."'))";
		$ar_mytotamount = mysql_query($q_mytotalcnt) or die(mysql_error());
		if(mysql_num_rows($ar_mytotamount)== 0 )
			echo "You don't have any jackpot history";
		else
		{
			echo "<table>";
			echo "<tr><td>Round ID</td><td>Date</td><td>Pot Value</td><td>Your bet</td><td>Status</td></tr>";
			while($row_mytotamount = mysql_fetch_assoc($ar_mytotamount)){
			echo "<tr><td>".$row_mytotamount['roundid']."</td>";
			echo "<td>".$row_mytotamount['start_date']."</td>";
			echo "<td>".$row_mytotamount['potv']."</td>";	
			echo "<td>".getyourpotvalue($row_mytotamount['roundid'])."</td>";				
			echo "<td>".$row_mytotamount['winnersteamid']."</td></tr>";
			}
			echo "</table>";
		}
	}

	function getyourpotvalue($round_id_user_history)
	{
		$q_get_your_bat_history = "SELECT sum(totamt) FROM tradeitems WHERE roundid ='".$round_id_user_history."' AND tradeid IN (SELECT TradeOfferId FROM trade WHERE partnerId64 = '".$_SESSION['steamid']."')";
		$r_get_your_bat_history = mysql_query($q_get_your_bat_history) or die(mysql_error());
		$ar_get_your_bat_history = mysql_fetch_array($r_get_your_bat_history);
		return $ar_get_your_bat_history["0"];
	}
?>