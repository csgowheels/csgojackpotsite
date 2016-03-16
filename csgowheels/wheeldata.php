<?php
	include "connect.php";
    $curr_time=time()-5;
	$currentround_query_wheel = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '1' and start_spin='1' and  start_date > ".$curr_time."   ORDER BY roundid DESC LIMIT 1 ");
    
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


    $q_wheel = "SELECT personname as name, round_points.steamid as steamid, avatarfull as thumb, sum(tickettot)/(select sum(tickettot) from round_points where roundid='".$round_id."') as percentage FROM round_points  INNER JOIN user ON user.usteamid = round_points.steamid where roundid='".$round_id."' GROUP BY steamid order by round_points.id asc"; 
	
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
        //echo $wheeldata1;
	}
?>