<?php
include("connect.php");

$q_trade = "select * from tradeitems where roundid=0 order by id asc";
$re_trade = mysql_query($q_trade) or die(mysql_error());
$g_minrange = 1;

$q_trade_feed = "select tradeid,sum(totamt) as price,partnerId64 from tradeitems inner join trade on trade.TradeOfferId = tradeitems.tradeid where roundid=0 group by tradeid";
$re_trade_feed = mysql_query($q_trade_feed) or die(mysql_error());

	$roundid = getroundid(); 

if( mysql_num_rows($re_trade)== 0 ){

}
else{
	while( $row_trade = mysql_fetch_assoc($re_trade)){
	 // $personname = getuserdetails($row_trade['tradeid']);
		gettrname($row_trade['tradeid'],$row_trade['totamt'],$row_trade['Amount'],$roundid,$row_trade['id'],$row_trade['itname'],$row_trade['itimg']);  
	}
}

while( $row_trade_feed = mysql_fetch_assoc($re_trade_feed)){
		$query = "SELECT sum(Amount) as itemCount FROM tradeitems WHERE tradeid=". $row_trade_feed['tradeid'];
		$res = mysql_query($query) or die("DIE");
		$itemCountFetch = mysql_fetch_assoc($res);
		$itemCount = $itemCountFetch['itemCount'];
		
	 	$cur_time = mysql_query("select now()");
		$row_cur_time = mysql_fetch_array($cur_time);
		$curr_time = $row_cur_time["0"];
		
		$insert_feed = "insert into feed (feedtype,ittotprice,date_time_feed,feed_user,roundid, itemcount) values ('1','".$row_trade_feed['price']."','".$curr_time."','".$row_trade_feed['partnerId64']."','"
						.$roundid."', '". $itemCount . "')";
		mysql_query($insert_feed);
		 
	}



// This function will get partner id from the trade id
function getpartnerid($tradeid)
{
$q_get_partid = "select partnerId64 from trade where TradeOfferId = '".$tradeid."'";
$ar_getpartid = mysql_query($q_get_partid) or die(mysql_error());
$row_getpartid = mysql_fetch_array($ar_getpartid);
return $row_getpartid['partnerId64'];
}

function getroundid()
{
$q_get_roundid = "select min(roundid) as rid from round where isactive='1' and isfinished='0'";
$ar_getroundid = mysql_query($q_get_roundid) or die(mysql_error());
$row_getroundid = mysql_fetch_array($ar_getroundid);
return $row_getroundid['rid'];
}



function getuserdetails($tradeid)
{
$partnerid = getpartnerid($tradeid);
$q_get_userid = "select personname from user where usteamid = '".$partnerid."'";
$ar_getuserid = mysql_query($q_get_userid) or die(mysql_error());
$row_getuserid = mysql_fetch_array($ar_getuserid);
return $row_getuserid['personname'];
}


// This function is to get price from the market name
function getprice($market_hash_name)
{
	/*
	//echo 'http://steamcommunity.com/market/priceoverview/?country=us&currency=1&appid=730&market_hash_name='. $item['market_name'];
	$abc =  $market_hash_name;
	$ln = 'http://steamcommunity.com/market/priceoverview/?country=us&currency=1&appid=730&market_hash_name='.urlencode($abc);
	$link1 = file_get_contents($ln);
	$myarray1 = json_decode($link1, true);
	return $myarray1['median_price'];
	*/
}

//code to get asset item and details
function gettrname($tradeid,$totamt,$amt,$roundid,$tid,$itname,$itimgurl)
{
	
	$partnerid = getpartnerid($tradeid);
	//$ur_link = "http://steamcommunity.com/profiles/".$partnerid."/inventory/json/730/2/?trading=1");
	//$link = file_get_contents($ur_link);
	//$link = file_get_contents('http://steamcommunity.com/profiles/'.$partnerid.'/inventory/json/730/2/?trading=1');
	//$myarray = json_decode($link, true);
	//$count = 0;
	
		
		$totticket = ((floatval($totamt))*100);
		// get range of ticket
		$minrange = getmintr($tid,$roundid);
		$minrange = $minrange + 1;
		$maxrange = $minrange + $totticket - 1;
		
		$up_trade_query = "update tradeitems set tradingstatus='0',tickettot='".$totticket."',roundid='".$roundid."',minrange='".$minrange."',maxrange='".$maxrange."' where id='".$tid."'";
		
		mysql_query($up_trade_query) or die(mysql_error());
		// insert every record in feed table
		/*
		$cur_time = mysql_query("select now()");
		$row_cur_time = mysql_fetch_array($cur_time);
		$curr_time = $row_cur_time["0"];
		
		$insert_feed = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,feed_user) values ('1','".$itname."','".$totamt."','".$itimgurl."','".$curr_time."','".$partnerid."')";
		mysql_query($insert_feed);
		*/
		
	
}
function getmintr($trid,$curroundid)
{
$q_get_minid = "select id from tradeitems where roundid='".$curroundid."' order by id DESC Limit 1";
$ar_get_minid = mysql_query($q_get_minid) or die(mysql_error());
	if(mysql_num_rows($ar_get_minid) == 0)
	{
	return 0;
	}
	else
	{
	$row_get_minid = mysql_fetch_array($ar_get_minid);
	$exid = $row_get_minid['id'];
		if($trid > $exid)
		{
		$q_get_mintr = "select maxrange from tradeitems where id = '".$exid."'";
		$ar_get_mintr = mysql_query($q_get_mintr) or die(mysql_error());
		$row_get_mintr = mysql_fetch_array($ar_get_mintr);
		return $row_get_mintr['maxrange'];
		}
		else
		{
		return 0;
		}
	
	}
}
?>