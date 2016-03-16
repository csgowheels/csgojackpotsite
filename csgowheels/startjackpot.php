<?php
	include "connect.php";
	// findout the current round id
    
    $locked = mysql_fetch_assoc(mysql_query("SELECT * FROM locked WHERE id=1"));
    $locked = $locked['locked'];
	
    if(isset($_GET['timer']) && $locked == 0) {
    	// check if it was ajax request
    	// if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
				// && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'xmlhttprequest') {
			$time=time();
			$x=mysql_fetch_assoc(mysql_query("SELECT * FROM timer_start WHERE id=0"));
			if ($time - $x['time_started'] >= 56 && $time - $x['time_started'] <= 65) {
		        mysql_query("UPDATE locked SET locked=1 WHERE id=1");
				$locked = 1;
			}
	}
    
    if ($locked == 0) die(); // do some error handling here (starts again or whatever..)
   
	$currentround_query = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
	//$currentround_query = mysql_query("SELECT * FROM round WHERE roundid=6");
	$is_timer = $_GET['timer'];
	$ar_roundcurrentid = mysql_fetch_array($currentround_query);
	$current_roundid = $ar_roundcurrentid['roundid'];
	$winningpercentage = $ar_roundcurrentid['winningpercentage'];
	$winningsecreat = $ar_roundcurrentid['roundsecret'];
	// count total amount of skins in round
	$totalamtround_query = mysql_query("select sum(Amount)from tradeitems where roundid='".$current_roundid."'") or die(mysql_error());
	$ar_totamtcurround = mysql_fetch_array($totalamtround_query);
	$cnt_amt_round = $ar_totamtcurround['0'];
	$total_users=mysql_query("select distinct  tradeid FROM tradeitems WHERE roundid='".$current_roundid."'");
	$num_rows = mysql_num_rows($total_users);
	
	if($cnt_amt_round >= 50 || ( $num_rows>=2 && isset($is_timer) ) )
	{
		//echo $num_rows;
		//start jackpot 
		mysql_query("update round set isfinished ='1' where roundid='".$current_roundid."'");
		
		$totalticketround_query = mysql_query("select sum(tickettot) from tradeitems where roundid='".$current_roundid."'");
		$ar_totticketcurround = mysql_fetch_array($totalticketround_query);
		$cnt_ticket_round = $ar_totticketcurround['0'];
		$winningticket = (floatval($cnt_ticket_round) * floatval($winningpercentage)) /100;
		$winningticket = round($winningticket);
		
		// select winner id 
		$q_select_winner = "select id,tradeid,minrange,maxrange from tradeitems where ( ".$winningticket." BETWEEN minrange and maxrange)  and roundid='".$current_roundid."'";
		$ar_select_winner = mysql_query($q_select_winner) or die(mysql_error());
		$ar_select_winner_arr = mysql_fetch_array($ar_select_winner);
		$winner_id = $ar_select_winner_arr["0"];
		$winner_tradeid = $ar_select_winner_arr["1"];
		$winner_minrange = $ar_select_winner_arr["minrange"]/$cnt_ticket_round;
		$winner_maxrange = $ar_select_winner_arr["maxrange"]/$cnt_ticket_round;
		
		$winner_steamid = getpartnerid($winner_tradeid);
		//insert winning in feed
	    
		$cur_time = mysql_query("select now()");
		$row_cur_time = mysql_fetch_array($cur_time);
		$curr_time = $row_cur_time["0"];
		
		mysql_query("update round set winnersteamid = '".$winner_steamid."',start_date = '".$curr_time."',min_percent='".$winner_minrange."',max_percent='".$winner_maxrange."',start_spin='1' where roundid='".$current_roundid."'"); 
		
		$insert_feed = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,feed_user,roundid) values ('2','','".$cnt_ticket_round*0.01."','','".$curr_time."','".$winner_steamid."','".$current_roundid."')";
		mysql_query($insert_feed);
		
		$insert_feed2 = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,feed_user,roundid) values ('4','".$winningsecreat."','".$winningpercentage."','','".$curr_time."','".$winner_steamid."','".$current_roundid."')";
		mysql_query($insert_feed2);	
		
		include "createround.php";
		
		// insert record in send item for winner user
		mysql_query("SET NAMES utf8");
		$q_send_item  = "select tradeitems.id as id,tradeid,AssetId,ContextId,ClassId,InstanceId,botId64,itname,roundid from tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid  where roundid='".$current_roundid."'";
		
		$re_send_item = mysql_query($q_send_item) or die(mysql_error());
		
		if( mysql_num_rows($re_send_item)== 0 ){
		 //echo "hi";
		} else {
			$pot_value_query = mysql_query("select sum(totamt) from tradeitems where roundid='".$current_roundid."'");
			$ar_potValue = mysql_fetch_array($pot_value_query);
			$pot_value = $ar_potValue['0'];
			
			$pot_value_4per = $pot_value * 4 /100;
			$pot_value_6per = $pot_value * 8 /100;
			$commi_value = 0;
	
			// first check selected item
			mysql_query("SET NAMES utf8");
			$q_commiSkin = 'select * from commiskin order by id asc';
			$re_commiSkin = mysql_query($q_commiSkin) or die(mysql_error());
			$commi_skin_ids = array();
			
			/*
			while($row_commi_skin = mysql_fetch_assoc($re_commiSkin)){	
				mysql_query("SET NAMES utf8");
				$q_check_item  = "select tradeitems.id as id,totamt from tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid  
				where roundid='".$current_roundid."' and trade.partnerId64 <> '".$winner_steamid."' and itname like '".$row_commi_skin['name']."'"
				. " order by tradeitems.totamt desc";
				
				$re_check_item = mysql_query($q_check_item) or die(mysql_error());
				while($row_check_skin = mysql_fetch_assoc($re_check_item)){	
					if(($row_check_skin['totamt'] + $commi_value) < $pot_value_6per)
					{
						$commi_value = $commi_value + $row_check_skin['totamt'];
						$commi_skin_ids[] = $row_check_skin['id'];
					}
				}
			}
			
			mysql_query("SET NAMES utf8");
			$q_check_item  = "select tradeitems.id as id,totamt from tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid  
				where roundid='".$current_roundid."' and trade.partnerId64 <> '".$winner_steamid."' and itname like '%Knife%' and itname not like '%StatTrak%' and totamt>=50 and totamt<=400";
				
			$re_check_item = mysql_query($q_check_item) or die(mysql_error());
				while($row_check_skin = mysql_fetch_assoc($re_check_item)){	
					if(($row_check_skin['totamt'] + $commi_value) < $pot_value_6per)
					{
						$commi_value = $commi_value + $row_check_skin['totamt'];
						$commi_skin_ids[] = $row_check_skin['id'];
					}
				}
			 */

			// check other items for commision
			mysql_query("SET NAMES utf8");
			$q_check_item  = "select tradeitems.id as id,totamt from tradeitems INNER JOIN trade ON trade.TradeOfferId = tradeitems.tradeid  
				where roundid='".$current_roundid."' and trade.partnerId64 <> '".$winner_steamid."'  order by totamt desc";
			
			$re_check_item = mysql_query($q_check_item) or die(mysql_error());
			while($row_check_skin = mysql_fetch_assoc($re_check_item)){	
				if(($row_check_skin['totamt'] + $commi_value) < $pot_value_6per)
				{
					$commi_value = $commi_value + $row_check_skin['totamt'];
					$commi_skin_ids[] = $row_check_skin['id'];
				}
			}

			while($row_send_item = mysql_fetch_assoc($re_send_item)){
				$senderSteamId = 0;
				if (in_array($row_send_item['id'], $commi_skin_ids)) {
					$senderSteamId = '76561198235135036';	// botId1 (commision bot)
				} else {
					$senderSteamId = $winner_steamid;	
				}
				$itname = str_replace("'","''",$row_send_item['itname']);
				mysql_query("SET NAMES utf8");
				$q_insert_send_items = "replace into senditem (tradeid,AssetId,ContextId,ClassId,InstanceId,botid,partnerid,issend,isactive,itname,roundid) 
				values ('".$current_roundid."','".$row_send_item['AssetId']."','".$row_send_item['ContextId']."','".$row_send_item['ClassId']."','".$row_send_item['InstanceId']."','".$row_send_item['botId64']."','".$senderSteamId."','0','1','".$itname."','".$row_send_item['roundid']."')"; 
				
				//echo $q_insert_send_items;
				//echo "<br>";
				//echo "<br>";
				
				mysql_query('SET CHARACTER SET utf8');
				mysql_query("SET NAMES utf8");
				mysql_query($q_insert_send_items) or die(mysql_error());
			}
		}
		
		// release lock
		mysql_query("UPDATE locked SET locked=0 WHERE id=1");
	} else {
		// release lock
		mysql_query("UPDATE locked SET locked=0 WHERE id=1");
	}
	
	echo "N9TT-9G0A-B7FQ-RANC";
	
	function getpartnerid($tradeid)
	{
		$q_get_partid = "select partnerId64 from trade where TradeOfferId = '".$tradeid."'";
		$ar_getpartid = mysql_query($q_get_partid) or die(mysql_error());
		$row_getpartid = mysql_fetch_array($ar_getpartid);
		return $row_getpartid['partnerId64'];
	}
?>