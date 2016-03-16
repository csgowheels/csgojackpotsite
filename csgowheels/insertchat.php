<?php
	include "connect.php";
	
	header("Content-Type: text/html; charset=UTF-8");
	mysql_query("SET NAMES 'utf8'"); 
	mysql_query('SET CHARACTER SET utf8');
	mysql_query("SET NAMES utf8");
	
	$charttext = $_REQUEST['chattext'];
	$chatsteamid = $_REQUEST['chatsteamid']; 
	
	$charttext = addslashes($charttext);
	$charttext = htmlspecialchars($charttext);
	$charttext = mysql_real_escape_string($charttext);
	$chatsteamid = htmlspecialchars($chatsteamid);
	$chatsteamid=mysql_real_escape_string($chatsteamid);
	$chat_cur_time = mysql_query("select now()");
	$row_chat_cur_time = mysql_fetch_array($chat_cur_time);
	$chat_curr_time = $row_chat_cur_time["0"];

    //SMILES
	$charttext=str_replace('RIP', '<img src="images/emotes/rip.png" title=";)"/>', $charttext);
	$charttext=str_replace('SNIPE', '<img src="images/emotes/Crosshair.png" style=\"width:30px;"\)"/>', $charttext);
    $charttext=str_replace('EZ', '<img src="images/emotes/EZ.png" style=\"width:30px;"\)"/>', $charttext);
    $charttext=str_replace('putin', '<img src="images/emotes/putin.png" style=\"width:30px;"\)"/>', $charttext);
    $charttext=str_replace('rekt', '<img src="images/emotes/giphy.gif" style=\"width:30px;"\)"/>', $charttext);











	$is_muted=mysql_fetch_assoc(mysql_query("SELECT * FROM mute WHERE steamid='".$chatsteamid."'"));
    
    //check if user has CSGO, if he doesn't, dont let him write
    $has_csgo=false;
    $url=file_get_contents("http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=9C9870D24C1A54325CFAAA11A50E4288&steamid=".$_SESSION['steamid']."&format=json");
    $games_owned=json_decode($url);

    foreach( $games_owned->response->games as $obj)
    {
        if(intval($obj->appid)==730)
        {
            $has_csgo=true;
            break;
        }
    }



	if(!is_null($is_muted["steamid"]))
        echo "Muted";
    else if($has_csgo==false && $chatsteamid!='76561198066634588' && $chatsteamid!='76561198281749605')
	    echo "CSGO";  
	else {
		if($chatsteamid=='76561198066634588' || $chatsteamid=='76561198281749605' || $chatsteamid=='76561197985902715' || $chatsteamid=='76561198129829935'){
			if (strpos($charttext,'/mute10') !== false){
				$name=substr($charttext,8);
				$steamid_query=mysql_query("SELECT usteamid FROM user WHERE personname='".$name."'" );
				$steamid_muted=mysql_fetch_assoc($steamid_query);    
				$unix_time=time()+600;
				if($steamid_muted['usteamid']!=0)
					$x=mysql_query("INSERT INTO  mute (steamid,time_entered) VALUES  ('".$steamid_muted['usteamid']."','".$unix_time."')");
				if($x)
					$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','<span style=\"color:red\">User ".$name." is muted for 10 minutes.','".$chat_curr_time."')";
			} else if(strpos($charttext,'/mute30') !== false){
				$name=substr($charttext,8);
				$steamid_query=mysql_query("SELECT usteamid FROM user WHERE personname='".$name."'" );
				$steamid_muted=mysql_fetch_assoc($steamid_query);
				$unix_time=time()+1800;
				if($steamid_muted['usteamid']!=0)
					$x=mysql_query("INSERT INTO  mute (steamid,time_entered) VALUES  ('".$steamid_muted['usteamid']."','".$unix_time."')");
				if($x)
					$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','<span style=\"color:red\">User ".$name." is muted for 30 minutes.','".$chat_curr_time."')";
			} else if(strpos($charttext,'/mute60') !== false){
				$name=substr($charttext,8);
				$steamid_query=mysql_query("SELECT usteamid FROM user WHERE personname='".$name."'" );
				$steamid_muted=mysql_fetch_assoc($steamid_query);
				$unix_time=time()+3600;
				if($steamid_muted['usteamid']!=0)
					$x=mysql_query("INSERT INTO  mute (steamid,time_entered) VALUES  ('".$steamid_muted['usteamid']."','".$unix_time."')");
				if($x)
					$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','<span style=\"color:red\">User ".$name." is muted for 60 minutes.','".$chat_curr_time."')";
			} else if(strpos($charttext,'/mutef') !== false){
				$name=substr($charttext,7);
				$steamid_query=mysql_query("SELECT usteamid FROM user WHERE personname='".$name."'" );
				$steamid_muted=mysql_fetch_assoc($steamid_query);
				$unix_time=time()+99999999;
				if($steamid_muted['usteamid']!=0)
					$x=mysql_query("INSERT INTO  mute (steamid,time_entered) VALUES  ('".$steamid_muted['usteamid']."','".$unix_time."')");
				if($x)
					$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','<span style=\"color:red\">User ".$name." is muted for 2 years.<br>See you in 2018!','".$chat_curr_time."')";
			} else
				$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','".$charttext."','".$chat_curr_time."')";
		} else {
			$charttext = cleaner($charttext);
			$insert_chat_feed = "insert into chat (usteamid,chattext,date_time_chat) values ('".$chatsteamid."','".$charttext."','".$chat_curr_time."')";
		}
	}
	mysql_query($insert_chat_feed) ;
	
	/*function clean($string) {
	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}*/
	
	function containsTLD($string) {
		preg_match("/(AC($|\/)|\.AD($|\/)|\.AE($|\/)|\.AERO($|\/)|\.AF($|\/)|\.AG($|\/)|\.AI($|\/)|\.AL($|\/)|\.AM($|\/)|\.AN($|\/)|\.AO($|\/)|\.AQ($|\/)|\.AR($|\/)|\.ARPA($|\/)|\.AS($|\/)|\.ASIA($|\/)|\.AT($|\/)|\.AU($|\/)|\.AW($|\/)|\.AX($|\/)|\.AZ($|\/)|\.BA($|\/)|\.BB($|\/)|\.BD($|\/)|\.BE($|\/)|\.BF($|\/)|\.BG($|\/)|\.BH($|\/)|\.BI($|\/)|\.BIZ($|\/)|\.BJ($|\/)|\.BM($|\/)|\.BN($|\/)|\.BO($|\/)|\.BR($|\/)|\.BS($|\/)|\.BT($|\/)|\.BV($|\/)|\.BW($|\/)|\.BY($|\/)|\.BZ($|\/)|\.CA($|\/)|\.CAT($|\/)|\.CC($|\/)|\.CD($|\/)|\.CF($|\/)|\.CG($|\/)|\.CH($|\/)|\.CI($|\/)|\.CK($|\/)|\.CL($|\/)|\.CM($|\/)|\.CN($|\/)|\.CO($|\/)|\.COM($|\/)|\.COOP($|\/)|\.CR($|\/)|\.CU($|\/)|\.CV($|\/)|\.CX($|\/)|\.CY($|\/)|\.CZ($|\/)|\.DE($|\/)|\.DJ($|\/)|\.DK($|\/)|\.DM($|\/)|\.DO($|\/)|\.DZ($|\/)|\.EC($|\/)|\.EDU($|\/)|\.EE($|\/)|\.EG($|\/)|\.ER($|\/)|\.ES($|\/)|\.ET($|\/)|\.EU($|\/)|\.FI($|\/)|\.FJ($|\/)|\.FK($|\/)|\.FM($|\/)|\.FO($|\/)|\.FR($|\/)|\.GA($|\/)|\.GB($|\/)|\.GD($|\/)|\.GE($|\/)|\.GF($|\/)|\.GG($|\/)|\.GH($|\/)|\.GI($|\/)|\.GL($|\/)|\.GM($|\/)|\.GN($|\/)|\.GOV($|\/)|\.GP($|\/)|\.GQ($|\/)|\.GR($|\/)|\.GS($|\/)|\.GT($|\/)|\.GU($|\/)|\.GW($|\/)|\.GY($|\/)|\.HK($|\/)|\.HM($|\/)|\.HN($|\/)|\.HR($|\/)|\.HT($|\/)|\.HU($|\/)|\.ID($|\/)|\.IE($|\/)|\.IL($|\/)|\.IM($|\/)|\.IN($|\/)|\.INFO($|\/)|\.INT($|\/)|\.IO($|\/)|\.IQ($|\/)|\.IR($|\/)|\.IS($|\/)|\.IT($|\/)|\.JE($|\/)|\.JM($|\/)|\.JO($|\/)|\.JOBS($|\/)|\.JP($|\/)|\.KE($|\/)|\.KG($|\/)|\.KH($|\/)|\.KI($|\/)|\.KM($|\/)|\.KN($|\/)|\.KP($|\/)|\.KR($|\/)|\.KW($|\/)|\.KY($|\/)|\.KZ($|\/)|\.LA($|\/)|\.LB($|\/)|\.LC($|\/)|\.LI($|\/)|\.LK($|\/)|\.LR($|\/)|\.LS($|\/)|\.LT($|\/)|\.LU($|\/)|\.LV($|\/)|\.LY($|\/)|\.MA($|\/)|\.MC($|\/)|\.MD($|\/)|\.ME($|\/)|\.MG($|\/)|\.MH($|\/)|\.MIL($|\/)|\.MK($|\/)|\.ML($|\/)|\.MM($|\/)|\.MN($|\/)|\.MO($|\/)|\.MOBI($|\/)|\.MP($|\/)|\.MQ($|\/)|\.MR($|\/)|\.MS($|\/)|\.MT($|\/)|\.MU($|\/)|\.MUSEUM($|\/)|\.MV($|\/)|\.MW($|\/)|\.MX($|\/)|\.MY($|\/)|\.MZ($|\/)|\.NA($|\/)|\.NAME($|\/)|\.NC($|\/)|\.NE($|\/)|\.NET($|\/)|\.NF($|\/)|\.NG($|\/)|\.NI($|\/)|\.NL($|\/)|\.NO($|\/)|\.NP($|\/)|\.NR($|\/)|\.NU($|\/)|\.NZ($|\/)|\.OM($|\/)|\.ORG($|\/)|\.PA($|\/)|\.PE($|\/)|\.PF($|\/)|\.PG($|\/)|\.PH($|\/)|\.PK($|\/)|\.PL($|\/)|\.PM($|\/)|\.PN($|\/)|\.PR($|\/)|\.PRO($|\/)|\.PS($|\/)|\.PT($|\/)|\.PW($|\/)|\.PY($|\/)|\.QA($|\/)|\.RE($|\/)|\.RO($|\/)|\.RS($|\/)|\.RU($|\/)|\.RW($|\/)|\.SA($|\/)|\.SB($|\/)|\.SC($|\/)|\.SD($|\/)|\.SE($|\/)|\.SG($|\/)|\.SH($|\/)|\.SI($|\/)|\.SJ($|\/)|\.SK($|\/)|\.SL($|\/)|\.SM($|\/)|\.SN($|\/)|\.SO($|\/)|\.SR($|\/)|\.ST($|\/)|\.SU($|\/)|\.SV($|\/)|\.SY($|\/)|\.SZ($|\/)|\.TC($|\/)|\.TD($|\/)|\.TEL($|\/)|\.TF($|\/)|\.TG($|\/)|\.TH($|\/)|\.TJ($|\/)|\.TK($|\/)|\.TL($|\/)|\.TM($|\/)|\.TN($|\/)|\.TO($|\/)|\.TP($|\/)|\.TR($|\/)|\.TRAVEL($|\/)|\.TT($|\/)|\.TV($|\/)|\.TW($|\/)|\.TZ($|\/)|\.UA($|\/)|\.UG($|\/)|\.UK($|\/)|\.US($|\/)|\.UY($|\/)|\.UZ($|\/)|\.VA($|\/)|\.VC($|\/)|\.VE($|\/)|\.VG($|\/)|\.VI($|\/)|\.VN($|\/)|\.VU($|\/)|\.WF($|\/)|\.WS($|\/)|\.XN--0ZWM56D($|\/)|\.XN--11B5BS3A9AJ6G($|\/)|\.XN--80AKHBYKNJ4F($|\/)|\.XN--9T4B11YI5A($|\/)|\.XN--DEBA0AD($|\/)|\.XN--G6W251D($|\/)|\.XN--HGBK6AJ7F53BBA($|\/)|\.XN--HLCJ6AYA9ESC7A($|\/)|\.XN--JXALPDLP($|\/)|\.XN--KGBECHTV($|\/)|\.XN--ZCKZAH($|\/)|\.YE($|\/)|\.YT($|\/)|\.YU($|\/)|\.ZA($|\/)|\.ZM($|\/)|\.ZW)/i",
	    				$string, $M);
		$has_tld = (count($M) > 0) ? true : false;
		return $has_tld;
	}
	
	function cleaner($url) {
		$U = explode(' ',$url);

		$W =array();
		foreach ($U as $k => $u) {
			if (stristr($u,".")) //only preg_match if there is a dot    
				if (containsTLD($u) === true) {
					unset($U[$k]);
	      			return cleaner( implode(' ',$U));
	    		}      
	  	}
	  	return implode(' ',$U);
	}
?>