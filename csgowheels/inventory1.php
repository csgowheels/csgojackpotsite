<?php
	//include "connect.php";
	/*$currentround_query_playercnt = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
	$ar_roundcurrentid_playercnt = mysql_fetch_array($currentround_query_playercnt);
	$round_id_player = $ar_roundcurrentid_playercnt['roundid'];
	*/
	$round_id_player = $cur_round_id;
	$inventory1 = "";
	mysql_query("SET NAMES utf8");
	$q_trade = "select * from tradeitems  where roundid = '".$cur_round_id_getdata."' order by cast(totamt AS DECIMAL(10,2)) desc Limit 50";
	 
	//$q_trade = "select * from tradeitems  where roundid = 14 order by cast(totamt AS DECIMAL(10,2)) desc Limit 50";
	$re_trade = mysql_query($q_trade) or die(mysql_error());
	if( mysql_num_rows($re_trade)== 0 ){
		$inventory1 .= 'no skin added';
	}else{
		$inventory1 .= "<div>";
		$inventory_loop_count = 0;
		while( $row_trade = mysql_fetch_assoc($re_trade)){
			if($row_trade['itname'] != "")
		  	{
		        $avatar_url=mysql_fetch_assoc(mysql_query("SELECT user.avatar AS avatar FROM user INNER JOIN trade ON trade.partnerId64 = user.usteamid INNER JOIN tradeitems ON tradeitems.tradeid=trade.TradeOfferID WHERE tradeitems.id=".$row_trade['id']));  
				$inventory_loop_count = $inventory_loop_count + 1;
				$inventory1 .= '<div class="col-md-4-sr" data-toggle="tooltip" title='.$row_trade["itname"].'>';
				$inventory1 .= '<div class="panel-inventory"  >';
		        
		        $inventory1 .="<img src='".$avatar_url['avatar']."' width='25' style='margin-left:70px'></img>";  
				$inventory1 .= '<div class="panel-body" style="padding:0px;">';
				$inventory1 .= "<img src='".$row_trade['itimg']."' width='70'  />";
				$inventory1 .= "</div>";
				$inventory1 .= '<div class="panel-heading-sr" title="'.$row_trade['itname'].'">'.$row_trade['itname']."</div>";
				$inventory1 .= '<div class="panel-price">$'.round($row_trade['itprice'], 2)."</div>";
				$inventory1 .= '</div>';
				$inventory1 .= "</div>";
				if($inventory_loop_count % 5 != 0)
					$inventory1 .= '<div class="vr-sr"></div>';
			
			  	if($inventory_loop_count % 5 == 0)
					$inventory1 .= "<hr class='feed-divider'>";
			}
		}
		
        $inventory1 .= "</div>";
	}
?>