<?php
	//include("connect.php");
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('memory_limit', '128M');
	ini_set('upload_max_filesize', '40M');
	ini_set('post_max_size', '40M');
	
	$hostname='localhost'; //// specify host, i.e. 'localhost'
	$user='csgowhe1_axe'; //// specify username
	$pass='Bortezomib109.'; //// specify password
	$dbase='csgowhe1_csgowhee'; //// specify database name
	$connection = mysql_connect("$hostname" , "$user" , "$pass") or die ("Can't connect to MySQL");
	$db = mysql_select_db($dbase , $connection) or die ("Can't select database.");
	
	error_reporting(E_ALL & ~E_NOTICE);
	header("Content-Type: text/html; charset=UTF-8");
	mysql_query("SET NAMES 'utf8'"); 
	mysql_query('SET CHARACTER SET utf8');
	
	
	//$ln = 'http://api.steamanalyst.com/apiV2.php?key=03775470751784889';
	// $ln = 'http://csgowheels.com/response.json';
	//$link1 = file_get_contents($ln);
	
/*
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, 'http://api.steamanalyst.com/apiV2.php?key=03775470751784889');
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	
	$jsonData = json_decode(curl_exec($curlSession));
	curl_close($curlSession);
	print_r($jsonData);
*/
	
	$myarray1 =  getPrice();
	
	foreach($myarray1['results'] as $item){
		//echo $item['market_name'];
		//echo '<br>';
		if (array_key_exists('avg_price_7_days', $item))
			$price_set = $item['avg_price_7_days'];
		else
		{	
			if (array_key_exists('suggested_amount_min', $item))
				$price_set = $item['suggested_amount_min']; 
		}
		
		$market_name = str_replace("'","''",$item['market_name']);
		$price_set = str_replace(",","",$price_set);
		//addUpdatePrice($market_name,$item['avg_price_7_days'],$item['current_price'],$item['suggested_amount_min']);
		addUpdatePrice($market_name,$price_set,'0','0');
	}
	//mysql_query("insert into cronlog (date_time_cron,sendornot) values (now(),'1')") or die(mysql_error());

	echo "successfully updated prices!";	
	function getPrice()
	{
		$ln = 'http://api.steamanalyst.com/apiV2.php?key=R58VeLcSedDl1XwkI';
		//$ln = 'response_new.json';
		$link1 = file_get_contents($ln);
		$myarray1 = json_decode($link1, true);
		return $myarray1; //$myarray1['results'];
	}
	
	//code to get add update price
	function addUpdatePrice($market_name,$avg_price_7_days,$current_price,$suggested_amount_min)
	{
		$q_trade = "select * from skinprice where market_name='".$market_name."'";
		$re_trade = mysql_query($q_trade) or die(mysql_error());
		if( mysql_num_rows($re_trade)== 0 ){
			$insert_price = "insert into skinprice (market_name,avg_price_7_days,current_price,suggested_amount_min) values ('".$market_name."','".$avg_price_7_days."','".$current_price."','".$suggested_amount_min."')";
			mysql_query("SET NAMES utf8");
			mysql_query($insert_price);
		}else{
			$update_price = "update skinprice set market_name='".$market_name."',avg_price_7_days='".$avg_price_7_days."',current_price='".$current_price."',suggested_amount_min='".$suggested_amount_min."' where market_name='".$market_name."'";
			mysql_query("SET NAMES utf8");
			mysql_query($update_price);
		}	
	}

?>