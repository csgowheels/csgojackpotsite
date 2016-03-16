<?php
	if($last_chat_id == "0" || $last_chat_id == "")
		$q_chat = "SELECT chatid, muted, chat.usteamid, chattext, personname, avatar,profileurl FROM chat INNER JOIN user ON chat.usteamid = user.usteamid where chatid > ((SELECT MAX(chatid) FROM chat) - 50) order by chatid ASC";
	else
		$q_chat = "SELECT chatid, muted,  chat.usteamid, chattext, personname, avatar,profileurl FROM chat INNER JOIN user ON chat.usteamid = user.usteamid where chatid  > '".$last_chat_id."' order by chatid ASC";

	$chatfeed = "";
	$new_chat_id = $last_chat_id;
	mysql_query("SET NAMES utf8");
	
	$re_chat = mysql_query($q_chat) or die(mysql_error());
	if( mysql_num_rows($re_chat)== 0 ){
		//echo '<div">No chat found</div>';
	} else {
		$chatfeed .= "<div>";
	    if(isset($_SESSION['steamid']))
        {
			if($_SESSION['steamid']==76561198066634588 || $_SESSION['steamid']==76561198281749605 || $_SESSION['steamid']==76561198129829935 || $_SESSION['steamid']==76561197985902715 || $_SESSION['steamid']==76561198161298882 || $_SESSION['steamid']==76561198164849366 || $_SESSION['steamid']==76561197974227275 || $_SESSION['steamid']==76561198066637719  || $_SESSION['steamid']==76561198204343000)
            {
               while( $row_chat = mysql_fetch_assoc($re_chat)){
                   if($new_chat_id == "0")
                       $new_chat_id = $row_chat['chatid'];
                    if($row_chat["muted"]==1)
                        {}
					else {
                        if($row_chat["usteamid"]==76561198129829935 || $row_chat["usteamid"]==76561197985902715 || $row_chat["usteamid"]==76561198161298882 	
								|| $row_chat["usteamid"]==76561198066634588 || $row_chat["usteamid"]==76561198281749605){
							$new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:red;"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
                                // Message Delete
					    } else if ($row_chat["usteamid"]==76561197974227275 || $row_chat["usteamid"]==76561198164849366 || $row_chat["usteamid"]==76561198066637719 || $row_chat["usteamid"]==76561198204343000){
						    $new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:orange"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
					    } else {
							$new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" ><strong>'.cleaner($row_chat["personname"]).'</strong><a href="deletemsg.php?delete='.$row_chat["chatid"].'"> Delete</a></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
						}
					}
               }
        	} else {
	            while( $row_chat = mysql_fetch_assoc($re_chat)){
              		if($new_chat_id == "0")
                       $new_chat_id = $row_chat['chatid'];
                    if($row_chat["muted"]==1)
                    {}
                    else {
                        if($row_chat["usteamid"]==76561198129829935 || $row_chat["usteamid"]==76561197985902715 || $row_chat["usteamid"]==76561198161298882 	
								|| $row_chat["usteamid"]==76561198066634588 || $row_chat["usteamid"]==76561198281749605 ){
							$new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:red;"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
                                // Message Delete
					    } else if ($row_chat["usteamid"]==76561197974227275 || $row_chat["usteamid"]==76561198164849366 || $row_chat["usteamid"]==76561198066637719
								|| $row_chat["usteamid"]==76561198204343000){
						    $new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:orange"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
					    } else {
							$new_chat_id = $row_chat['chatid'];
							$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" ><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
							$chatfeed .= "<hr class='feed-divider'>";
				        }
					}
       			}
        	}
        } else {
			while( $row_chat = mysql_fetch_assoc($re_chat)){
				if($new_chat_id == "0")
	               $new_chat_id = $row_chat['chatid'];
	            if($row_chat["muted"]==1)
	            {}
	            else {
					if($row_chat["usteamid"]==76561198129829935 || $row_chat["usteamid"]==76561197985902715 || $row_chat["usteamid"]==76561198161298882 	
							|| $row_chat["usteamid"]==76561198066634588 || $row_chat["usteamid"]==76561198281749605){
						$new_chat_id = $row_chat['chatid'];
						$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:red;"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
						$chatfeed .= "<hr class='feed-divider'>";
	                    // Message Delete
				    } else if ($row_chat["usteamid"]==76561197974227275 || $row_chat["usteamid"]==76561198164849366 || $row_chat["usteamid"]==76561198066637719
							|| $row_chat["usteamid"]==76561198204343000){
					    $new_chat_id = $row_chat['chatid'];
						$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" style="color:orange"><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
						$chatfeed .= "<hr class='feed-divider'>";
				    } else {
						$new_chat_id = $row_chat['chatid'];
						$chatfeed .=  '<div class="row" style="padding: 0;"><div class="col-sm-12"><a href="'.$row_chat['profileurl'].'"><img src="'.$row_chat['avatar'].'" class="img-circle" style="max-height: 20px; float: left;" /></a><p class="chat-user" ><strong>'.cleaner($row_chat["personname"]).'</strong></p></div><div class="col-sm-12"><p class="chat-message">'.stripslashes($row_chat['chattext']).'</p></div></div>';
						$chatfeed .= "<hr class='feed-divider'>";
			        }
				}
			}
	    }

		$chatfeed .= "</div>";
	}
		
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