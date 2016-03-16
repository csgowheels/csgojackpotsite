<?php
	include "connect.php";
	$currentround_query_wheel = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '1' and start_spin='1' and start_date>DATE_ADD(NOW(), INTERVAL -5 SECOND)  ORDER BY roundid ASC LIMIT 1 ");
	$ar_roundcurrentid_wheel = mysql_fetch_array($currentround_query_wheel);
	
	if( mysql_num_rows($currentround_query_wheel)> 0 ){
		$round_id = $ar_roundcurrentid_wheel['roundid'];
		$winner_id_popup = $ar_roundcurrentid_wheel['winnersteamid'];
		$min_percent = $ar_roundcurrentid_wheel['min_percent'];
		$max_percent = $ar_roundcurrentid_wheel['max_percent'];
		$start_spin = 1;
		if($winner_id_popup == $_SESSION['steamid'])
			$show_winner_popup = '1';
		else
			$show_winner_popup = '0';
	} else {
		$currentround_query_wheel = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
		$ar_roundcurrentid_wheel = mysql_fetch_array($currentround_query_wheel);
		$round_id = $ar_roundcurrentid_wheel['roundid'];
		$min_percent = 0;
		$max_percent = 0;
		$start_spin = 0;
		$show_winner_popup = 0;
	}
	
	$q_wheel = "SELECT personname as name, trade.partnerId64 as steamid, avatarfull as thumb, sum(tickettot)/(select sum(tickettot) from tradeitems where roundid='".$round_id."') as percentage FROM tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid INNER JOIN user ON user.usteamid = trade.partnerId64 where roundid='".$round_id."' GROUP BY tradeid order by trade.id asc"; 
	
	//echo $q_wheel;
	$r_wheel = mysql_query($q_wheel) or die(mysql_error());
	if( mysql_num_rows($r_wheel)== 0 ){
	} else {
		$side_arr = array();
		$used = array();
		$final_arr = array();
		$jsonWheel = '{"users": [';
		while( $row_wheel = mysql_fetch_assoc($r_wheel)){
			array_push($side_arr, $row_wheel);
	 	}

		foreach($side_arr as $row_value) {
			if (in_array($row_value['steamid'], $used))
		   		$final_arr[$row_value['steamid']]['percentage'] += $row_value['percentage'];
		  	else {
		   		$final_arr[$row_value['steamid']] = $row_value;
		   		array_push($used, $row_value['steamid']);
		  	}
		 }
		 
		foreach($final_arr as $value)
			$jsonWheel .= json_encode($value) . ",";
		
		$jsonWheel = rtrim($jsonWheel, ',');
		$jsonWheel .= ']}';
		$wheeldata1 = $jsonWheel;
	}
?>