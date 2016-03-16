

<?php

include "connect.php";
// check in database if there are sufficient points in the database,if not, throw error about it so it can get displayed on the popup page
$x = mysql_fetch_assoc(mysql_query("SELECT * FROM user WHERE usteamid='".$_SESSION['steamid']."'"));
$steamid=$_SESSION['steamid'];
$points=$_POST['points'];
$points=mysql_real_escape_string($_POST['points']);
//gets current time, and stores it in $curr_time (useless as fuck) - koristi se za upisivanje u feed
$cur_time = mysql_query("select now()");
$row_cur_time = mysql_fetch_array($cur_time);
$curr_time = $row_cur_time["0"];
// end of useless time gathering


$time = time(); // vreme za timer


$roundid=getroundid();

if($points<=$x['points'] && is_numeric($points) && $points>=10)

{
    $prev_points=$x['points'];
	
    gettrname($steamid,($points/10),getroundid(),$tid);
    
	//ackov kod za timer start
	$users_in_round = mysql_num_rows(mysql_query("SELECT steamid FROM round_points WHERE roundid=" . $roundid . " GROUP BY steamid"));
	if ($users_in_round >= 2)
	{
		$time_now = time();
		$timer_start_res = mysql_fetch_assoc(mysql_query("SELECT time_started FROM timer_start WHERE id=0"));
		if ($time_now - $timer_start_res['time_started'] > 65)
			mysql_query("UPDATE timer_start SET time_started=".$time." WHERE id=0");
	}
    // kraj ackovog koda 
   
    mysql_query("UPDATE user SET points=".($prev_points-$points)." WHERE usteamid=".$_SESSION['steamid']);
    
    
    $insert_feed = "insert into feed (feedtype,ittotprice,date_time_feed,feed_user,roundid, itemcount) values ('1','".($points/100)."','".$curr_time."','".$steamid."','"
							.$roundid."', '". $points . "')";
    mysql_query($insert_feed);
    
    mysql_query("insert into point_transfer (steam_id,value,added,previous_points,current_points,executer) values ('".$_SESSION['steamid']."',".(intval($points)).",0,".$prev_points.",".($prev_points-$points). ", 'php') "); 
    
   echo $points;
}

else
{
    if($points<10)
        $echos = "Less10";
    else
        $echos = "error";
    echo trim($echos);
}
    











// funkcije




function getroundid()
	{
		$q_get_roundid = "select min(roundid) as rid from round where isactive='1' and isfinished='0'";
		$ar_getroundid = mysql_query($q_get_roundid);
		$row_getroundid = mysql_fetch_array($ar_getroundid);
		return $row_getroundid['rid'];
	}

function getmintr($curroundid)
	{
		$q_get_minid = "SELECT maxrange FROM round_points WHERE roundid='".$curroundid."'";
		$ar_get_minid = mysql_query($q_get_minid) ;
        $max = 0;
        while ($row = mysql_fetch_assoc($ar_get_minid))
		  if ($row['maxrange'] > $max)
		    $max = $row['maxrange'];
		                return $max;
	}
	
	function update_all_tradeitems($max, $total, $roundid)
	{
		$ids_for_change = array();
		
		$query = "SELECT id, minrange, maxrange FROM round_points WHERE minrange > " . $max." AND roundid=".$roundid;
		$result = mysql_query($query);
		 
		while ($row = mysql_fetch_assoc($result))
			array_push($ids_for_change,$row);
		
		foreach ($ids_for_change as $row) {
			$query = "UPDATE round_points SET minrange='" . ($row['minrange'] + $total) . 
					"', maxrange='" . ($row['maxrange'] + $total) . "' WHERE id='" . $row['id']."'";
	        
			mysql_query($query) ;
		}
	}

function gettrname($steamid,$totamt,$roundid,$tid)
	{
		
		
		$totticket = ((floatval($totamt))*10); // u poenima
		$minrange = 0;
		$maxrange = 0;
		
		$query = "SELECT maxrange FROM  round_points  WHERE roundid='".$roundid."' AND steamid='".$steamid."'";
		$res = mysql_query($query);
		
		if (mysql_num_rows($res) == 0) {
			// get range of ticket
	        
			$minrange = getmintr($roundid);
            if($minrange==0)
                $minrange=$minrange;
            else
                $minrange = $minrange + 1;
			$maxrange = $minrange + $totticket - 1;
		} else {
			$max = 0;
			while ($row = mysql_fetch_assoc($res)) {
				if ($row['maxrange'] > $max)
					$max = $row['maxrange'];
			}
			
			$minrange = $max + 1;
			$maxrange = $minrange + $totticket - 1;
			
			update_all_tradeitems($max, $totticket, $roundid);
		}
		
        mysql_query("INSERT INTO round_points (steamid,points,tickettot,roundid,minrange,maxrange) VALUES (".$steamid.",".$totticket.",".$totticket.",".$roundid.",".$minrange.",".$maxrange.")");
		mysql_query($up_trade_query) ;
		
    
	}
?>

