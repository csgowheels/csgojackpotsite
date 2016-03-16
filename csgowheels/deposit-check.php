<?php
	$hostname='localhost'; //// specify host, i.e. 'localhost'
	$user='root'; //// specify username
	$pass=''; //// specify password
	$dbase='csgowhee_csgo'; //// specify database name
    $connection = mysql_connect("$hostname" , "$user" , "$pass") or die ("Can't connect to MySQL");
	$db = mysql_select_db($dbase , $connection) or die ("Can't select database.");
    header("Content-Type: text/html; charset=UTF-8");
	mysql_query("SET NAMES 'utf8'"); 
	mysql_query('SET CHARACTER SET utf8');
 	$steamid = isset($_POST['steamid']) ? $_POST['steamid'] : NULL;
	$steamids = isset($_POST['steamids']) ? $_POST['steamids'] : NULL;
	$market_name = isset($_POST['market_name']) ? $_POST['market_name'] : NULL;
	$security_code = isset($_POST['code']) ? $_POST['code'] : NULL;
    $security = isset($_POST['security_code']) ? $_POST['security_code'] : NULL;
	$isNew = isset($_POST['isNew']) ? $_POST['isNew'] : NULL;
	$value = isset($_POST['value']) ? $_POST['value'] : NULL;
	$market_id = isset($_POST['market_id']) ? $_POST['market_id'] : NULL;
	$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : NULL;
	$instance_id= isset($_POST['instance']) ? $_POST['instance'] : NULL;
	$t=time();
	$bots = isset($_POST['bot']) ? $_POST['bot'] : NULL;
	// $bot = ($_POST['bot'] == 1 ? '76561198235134508' : '76561198203046427');

	if ($steamid !== NULL)
	{
		$steamid = htmlspecialchars($steamid);
		$steamid = mysql_real_escape_string($steamid);
	}

    if ($security !== NULL)
	{
		$security = htmlspecialchars($security);
		$security = mysql_real_escape_string($security);
	}
	
	$x_time=$t-(60*30);
	$count_ids = count($steamids);
	if ($steamids !== null && ($count_ids == count($market_name)
								&& $count_ids == count($security_code)
								&& $count_ids == count($isNew)
								&& $count_ids == count($value)
								&& $count_ids == count($market_id)
								&& $count_ids == count($class_id)
								&& $count_ids == count($instance_id)
								&& $count_ids == count($bots))) {
									
		$query = "INSERT INTO receive_table (partner_steam_id, bot_id, asset_id, it_name,
					class_id, security_code, time_created, description_id) VALUES ";
		for ($i = 0; $i < count($steamids); $i++)
		{
			$steamid = htmlspecialchars($steamids[$i]);
			$steamid = mysql_real_escape_string($steamid);
			
            if($bots[$i]==1)
                $bot="76561198235134508";
            else if($bots[$i]==2)
                $bot="76561198203046427";
            else if($bots[$i]==3)
                $bot="76561198235135036";
            else if($bots[$i]==4)
                $bot="76561198203224241";
            else 
                $bot="76561198203309841";
            
			
			
			$market_idd = htmlspecialchars($market_id[$i]);
			$market_idd = mysql_real_escape_string($market_idd);
			
			$market_named = mysql_real_escape_string($market_name[$i]);
			
			$valued = htmlspecialchars($value[$i]);
			$valued = mysql_real_escape_string($valued);
			
			$class_idd = htmlspecialchars($class_id[$i]);
			$class_idd = mysql_real_escape_string($class_idd);
			
			$instance_idd = htmlspecialchars($instance_id[$i]);
			$instance_idd = mysql_real_escape_string($instance_idd);
			
			$security_coded = htmlspecialchars($security_code[$i]);
			$security_coded = mysql_real_escape_string($security_coded);
			
			$isNewd = htmlspecialchars($isNew[$i]);
			$isNewd = mysql_real_escape_string($isNewd);
			
			$query .= "('" . $steamid . "', '" . $bot . "', '" 
							. $market_idd . "', '" . $market_named . "', '" 
							. $class_idd . "','" . $security_coded 
							. "', '" . $t . "', '" . $class_idd."_".$instance_idd. "' )" . ($i == $count_ids - 1 ? ";" : ", ");
		}
		mysql_query("SET NAMES utf8");
		mysql_query($query);
        //echo $query;
	}
   if(isset($security))
     {
        $security = htmlspecialchars($security);
        $security = mysql_real_escape_string($security);
        $offerid = mysql_query("SELECT offer_id FROM receive_table where partner_steam_id='".$steamid."' and offer_id<>0 and security_code='".$security."'  ORDER BY time_created DESC LIMIT 1");
        $x=mysql_fetch_assoc($offerid);
        if(mysql_num_rows($offerid)!=0)
            $result=$x["offer_id"];
     
        else
        {
            
            $query_error=mysql_query("select error_message FROM error_table where steam_id='".$steamid."' and security_code='".$security."' ORDER BY id DESC LIMIT 1");
            if(mysql_num_rows($query_error)!=0)
            {
                $res=mysql_fetch_assoc($query_error);
                $result=$res['error_message'];
            }
            
            else
                $result=0;
            
        }
         
        echo $result;
    }
?>