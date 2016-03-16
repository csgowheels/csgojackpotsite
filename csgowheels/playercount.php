<?php
	include "connect.php";
	/*$currentround_query_playercnt = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
	$ar_roundcurrentid_playercnt = mysql_fetch_array($currentround_query_playercnt);
	$round_id_player = $ar_roundcurrentid_playercnt['roundid'];
	*/
	//$q_tradecnt = "select DISTINCT partnerId64 from trade where roundid='".$round_id_player."'";
	/*$q_tradecnt = "SELECT count(DISTINCT partnerId64) FROM tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid
	WHERE roundid ='".$round_id_player."'";
	$ar_gettradecnt = mysql_query($q_tradecnt) or die(mysql_error());
	$row_gettradecnt = mysql_fetch_array($ar_gettradecnt);
	$playercnt = $row_gettradecnt["0"];
	*///echo  mysql_num_rows($ar_gettradecnt);
	
	$q_totalskincnt = "select  sum(points) from round_points where roundid='".$cur_round_id_getdata."'";
	
	$ar_totskincnt = mysql_query($q_totalskincnt) or die(mysql_error());
	$row_gettotskincnt = mysql_fetch_array($ar_totskincnt);
	
	if($row_gettotskincnt["0"] == "")
		$playercnt = "0P";
	else
		$playercnt = $row_gettotskincnt["0"]."P";
	
	$q_totalcnt = "select sum(points)/100 from round_points where roundid='".$cur_round_id_getdata."'";
	
	$ar_totamount = mysql_query($q_totalcnt) or die(mysql_error());
	$row_gettotamount = mysql_fetch_array($ar_totamount);
	
	if($row_gettotamount["0"] == "")
		$totamount_index = "$0";
	else
		$totamount_index = "$".round($row_gettotamount["0"], 2);
?>