<?php 
	include "connect.php";
	error_reporting(0);
	$last_feed_id = isset($_REQUEST['last_feed_id']) ? mysql_real_escape_string($_REQUEST['last_feed_id']) : "";
	
/*	
	$cur_time_ajax = mysql_query("select now()");
	$row_cur_time_ajax = mysql_fetch_array($cur_time_ajax);
	$newdate_time = $row_cur_time_ajax["0"];
	
	if($last_feed_id == "0")
	{
	$last_date = "0";
	}
*/
	$cur_round_id_getdata = findcurrentround();
	
	function findcurrentround()
	{
		$currentround_pre2 = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
		$ar_roundpre2 = mysql_fetch_array($currentround_pre2);
		$round_id_current = $ar_roundpre2['roundid'];
		
		// check for finishing of previous round
		$currentround_pre3 = mysql_query("SELECT * FROM round WHERE roundid = (select max(roundid) from round where roundid< ".$round_id_current.")");
		$ar_roundpre3 = mysql_fetch_array($currentround_pre3);
		$round_id_previous3 = $ar_roundpre3['roundid'];
		$date_time_previous = $ar_roundpre3['start_date'];
		
		
		$curr_time = time();
				
		
		$differenceInSeconds = $curr_time - $date_time_previous;
		if($differenceInSeconds <= 9)
			$curr_round_id = $round_id_previous3;
		else
			$curr_round_id = $round_id_current;
			
		return $curr_round_id;	
	}
	
	//include("inventory1.php");
	//include("previouswinner.php");
	include("feed.php");
    //include("points_live.php");
	//echo $outputFeed;
	// include("chatfeed.php");
	include("playercount.php");
	//include("youramount.php");
	// include("inventory1.php");
	include("wheeldata.php");
	//include("updateoffer.php");
	include("getOnlineUsers.php");
	

    //user total points
    $x = mysql_fetch_assoc(mysql_query("SELECT * FROM user WHERE usteamid='".$_SESSION['steamid']."'"));
	$total_points=$x['points'];
    $current_time=time()-24*60*60;
    //total rounds in last 24 hours
    $total_rounds= mysql_num_rows(mysql_query("SELECT * FROM `round` WHERE roundid>2800 and  start_date > ".$current_time));
    
    // max pot in last 24 hours in $
    $max_pot=mysql_fetch_assoc(mysql_query("SELECT (select sum(points)/100 from round_points where round_points.roundid=round.roundid) as potv, personname FROM round inner join user on round.winnersteamid = user.usteamid  where round.roundid>2800 and round.start_date > ".$current_time."  ORDER BY potv DESC LIMIT 1"));

    $max_pot=round($max_pot['potv']). "$";


    //previous winner
    $last_round_winner_id=mysql_fetch_assoc(mysql_query("SELECT winnersteamid FROM round where isfinished=1 order by roundid DESC limit 1"));
    $last_winner=$last_round_winner_id['winnersteamid'];
    $prev_winner=mysql_fetch_assoc(mysql_query("SELECT personname,avatarmedium from user where usteamid='".$last_winner."'"));
    $prev_winner_photo=$prev_winner['avatarmedium'];
    $prev_winner_name=$prev_winner['personname'];
	
	$time_now = time();
	$timer_start_res = mysql_fetch_assoc(mysql_query("SELECT time_started FROM timer_start WHERE id=0"));
	$timer_start = ($time_now - $timer_start_res['time_started'] < 60 ? "1" : "0");
    
    $id_of_tr_decline = "0";
    $reason_of_tr_decline = "0";
	
	/*
	$foo_ar = array('outputfeed' => $outputFeed,'pname_pervious'=>$pname_pervious,'pname_avatar'=>$pname_avatar,'pname_winner_percent'=>$pname_winner_percent,'pname_totamt'=>$pname_totamt,'chatfeed' => $chatfeed, 'last_feed_id' => $new_feed_id,'last_chat_id' => $new_chat_id,'pc' => $playercnt,'chance_to_win'=>$chance_to_win,'start_spin'=>$start_spin,'min_percent'=>$min_percent,'max_percent'=>$max_percent,'tmt'=>$totamount_index,'youramt'=>$ctot,'inventory1' => $inventory1, "roundUsers" => $wheeldata1);*/ 
	$foo_ar = array('outputfeed' => $outputFeed, 'last_feed_id' => $new_feed_id, 'pc' => $playercnt,'start_spin'=>$start_spin,'show_winner_popup'=>$show_winner_popup,
					'min_percent'=>$min_percent,'max_percent'=>$max_percent,'tmt'=>$totamount_index,'inventory1' => $inventory1,
					'show_trade_decline'=>$id_of_tr_decline,'reason_trade_decline'=>$reason_of_tr_decline,"roundUsers" => $wheeldata1,
					"offer_check_show" => $trade_offer_popup_show, "offer_check_price" => $trade_offer_popup_price, "offer_check_sum" => $trade_offer_popup_item_sum,
					"offer_check_offerid" => $trade_offer_popup_offerid, "online_users" => $online_users, "total_rounds" => $total_rounds, "max_pot" => $max_pot, 
					"prev_winner_photo" => $prev_winner_photo, "prev_winner_name" => $prev_winner_name, "total_points" => $total_points, "timer_start" => $timer_start);
	$json .= json_encode($foo_ar);
	echo $json;
?>
