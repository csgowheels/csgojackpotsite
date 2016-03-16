<?php
	/*include "connect.php";
	$currentround_previouswinner = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '1' and start_spin='1' and start_date>DATE_ADD(NOW(), INTERVAL -5 SECOND)  ORDER BY roundid ASC LIMIT 1 ");
	$ar_round_previouswinner = mysql_fetch_array($currentround_previouswinner);
	
	
	if( mysql_num_rows($currentround_previouswinner)> 0 ){
		$round_id_previous = $ar_round_previouswinner['roundid'];
		$round_id_previous = $round_id_previous -1;
		
	}
	else
	{
		$currentround_pre2 = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
		$ar_roundpre2 = mysql_fetch_array($currentround_pre2);
		$round_id_previous = $ar_roundpre2['roundid'] -1;
	}
	*/
	
	//$round_id_previous = $cur_round_id_getdata;
	$q_pre_win_query="SELECT * FROM round WHERE roundid = (select max(roundid) from round where roundid< ".$cur_round_id_getdata.")";
	
	$currentround_pre2 = mysql_query($q_pre_win_query) or die(mysql_error());
	$ar_roundpre2 = mysql_fetch_array($currentround_pre2);
	$round_id_previous = $ar_roundpre2['roundid'];
	if($round_id_previous <= 0)
	{
		$pname_pervious = "No Winner Record";
		$pname_avatar = "noimg.png";
		$pname_winner_percent = "0%";
		$pname_totamt = "$0.00";
		// echo "no data";
	}
	else
	{
		$q_pervi_win = "SELECT winnersteamid,personname,avatar,min_percent,max_percent FROM round inner join user on round.winnersteamid = user.usteamid  WHERE round.isactive = '1' AND round.isfinished = '1' and round.roundid='".$round_id_previous."'";
		$currentround_previous = mysql_query($q_pervi_win) or die(mysql_error());
		$ar_roundprevious = mysql_fetch_array($currentround_previous);
		$pname_pervious = $ar_roundprevious['personname'];
		$pname_avatar = $ar_roundprevious['avatar'];
		$pname_winner_percent = ($ar_roundprevious['max_percent']-$ar_roundprevious['min_percent'])*100;
		$q_pervi_totamt = mysql_query("SELECT sum(totamt) FROM tradeitems WHERE roundid='".$round_id_previous."'") or die(mysql_error());
		$ar_roundpre_totamt = mysql_fetch_array($q_pervi_totamt);
		$pname_totamt = "$".round($ar_roundpre_totamt["0"],3);
		//	$q_wheel = "SELECT sum(tickettot)/(select sum(tickettot) from tradeitems where roundid='".$round_id_previous."') as percentage FROM tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid INNER JOIN user ON user.usteamid = trade.partnerId64 where roundid='".$round_id_previous."' GROUP BY tradeid"; 
		// $r_wheel = mysql_query($q_wheel) or die(mysql_error());
	}
?>