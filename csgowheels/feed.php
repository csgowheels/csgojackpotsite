<?php
	//include("connect.php");
	//SELECT * FROM feed WHERE date_time_feed > DATE_FORMAT('2015-08-10 02:00:00','%Y-%m-%d %H:%i:%s')
	$outputFeed = '';
	
	if($last_feed_id == "0" || $last_feed_id == "")
		$q_trade = "select * from feed order by id DESC LIMIT 0 , 50";
	else
		$q_trade = "select * from feed where id > ".$last_feed_id." order by id DESC";
	
	$new_feed_id = "0";
	$re_trade = mysql_query($q_trade) or die(mysql_error());
	if( mysql_num_rows($re_trade)== 0 ){
		$outputFeed .= '';
	} else {
		while( $row_trade = mysql_fetch_assoc($re_trade)){
			if($new_feed_id == "0")
				$new_feed_id = $row_trade["id"];
			$feedtype = $row_trade['feedtype'];
		  	if($feedtype == '1'){
				$personname = getuserdetails($row_trade['feed_user']);
				$outputFeed .=  "<div class='row'><div class='col-md-2'><a href='".$personname['2']."'><img src='".$personname['1'].
					 				"' class='img-circle feed-profilepicture' /></a></div><div class='col-md-10'><p><span class='label label-bet '>Bet</span> "
					 				.$personname['0']." deposited <span class='label label-default'>" . $row_trade['itemcount'] . " points</span>with the value of <span class='label label-default'>$"
					 				.round($row_trade['ittotprice'],2)."</span>.</p></div>"; //*$row_updated['Amount']
		     	$outputFeed .=  '</div><hr class="feed-divider"><br />';
		  	} else if($feedtype == '2') {
			  	$query = "SELECT sum(tickettot)/(select sum(tickettot) from round_points where roundid='".
							$row_trade['roundid']."') as percentage FROM round_points INNER JOIN user 
							ON user.usteamid = round_points.steamid where roundid='".$row_trade['roundid']."' AND usteamid='".$row_trade['feed_user']
							."' GROUP BY steamid order by round_points.id asc;";
		
				$r = mysql_query($query) or die(mysql_error());
				$winning_percentage = 0;
				while ($p = mysql_fetch_array($r))
					$winning_percentage += $p[0];
			
				// $winning_percentage = '0';
			  	$personname = getuserdetails($row_trade['feed_user']);
		  				
				// $winning_percentage = 
			 
			  	$outputFeed .=  "<div class='row won'><div class='col-md-2'><a href='".$personname['2']."'><img src='".$personname['1'].
				  					"' class='img-circle feed-profilepicture' /></a></div><div class='col-md-10'><p><span class='label label-success'>Won</span> "
				  					.$personname['0']." won the pot valued at <span class='label label-default'>$".round($row_trade['ittotprice'],2)."</span> with  " .
				  					"<span class='label label-default'>".round(100 * $winning_percentage, 2)."% <br></span></p></div>"; //*$row_updated['Amount']
		    	$outputFeed .=  '</div><hr class="feed-divider"><br />';
	          	$tot_ticket=$row_trade['ittotprice'];
			} else if($feedtype == '3') {
			  	$outputFeed .=  "<div class='row newRound'><div class='col-md-2' style='width:1px;' ></div><div class='col-md-10' style='width:100%' >
									<span class='label label-primary'>New round #".$row_trade['roundid']." </br> Hash: ".$row_trade['itname']." </br></span><span class='label label-primary'><a href='provably-fair.php'> What's this?</a> </span></div>"; //*$row_updated['Amount']
								    $outputFeed .=  '</div><br><hr class="feed-divider"><br />';
		  	} else if($feedtype == '4') {				
		  		$outputFeed .=  "<div class='row round'><div class='col-md-2' style='width:1%;'></div><div class='col-md-10' style='width:100%'><span class='label label-primary'>Round #".$row_trade['roundid']." <br> Salt: ".$row_trade['itname'].
								  	"</br>Winning percentage: ".$row_trade['ittotprice']."</span><span class='label label-primary' style='margin-left:1px;'><a href='provably-fair.php?roundid=$row_trade[roundid]&salt=".
								  	$row_trade['itname']."&percentage=".($row_trade['ittotprice'])."'> Verify round</a></span></div>"; //*$row_updated['Amount']
								    $outputFeed .=  '</div><br><hr class="feed-divider"><br />';
		  	} else {}
		}
        
                  
        
	}
	if($new_feed_id == '0')
		$new_feed_id = $last_feed_id;
	
	
	function getuserdetails($partnerid)
	{
		$q_get_userid = "select personname,avatar,profileurl  from user where usteamid = '".$partnerid."'";
		$ar_getuserid = mysql_query($q_get_userid) or die(mysql_error());
		$row_getuserid = mysql_fetch_array($ar_getuserid);
		return $row_getuserid;
	}
?>