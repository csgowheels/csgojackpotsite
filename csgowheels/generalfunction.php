<?php

    function checkdbifuserhasbetted($userid)
	{
		$qPd = "select * from round_points where steamid='".$userid."' LIMIT 3";
		
		$rsPd = mysql_query($qPd);
		$rowd = mysql_fetch_array($rsPd);
		extract($rowd);
		$id=trim($id);
		if($id != "")
			return "1";
		else
			return "0";
	
	}



	function checkdbforuser($userid)
	{
		$qPd = "select * from user where usteamid='".$userid."'";
		
		$rsPd = mysql_query($qPd);
		$rowd = mysql_fetch_array($rsPd);
		extract($rowd);
		$id=trim($id);
		if($id != "")
			return "1";
		else
			return "0";
	}
	
	function gettradeurl()
	{
		$qPd = "select url from bot where type='receive' ORDER BY RAND()";
		
		$rsPd = mysql_query($qPd) or die(mysql_error());
		$rowd = mysql_fetch_array($rsPd);
		$profiletradeurl = $rowd["url"];
		return $profiletradeurl;
	}
	
	function checktradeurl()
	{
		$qPd = "select id,profiletradeurl from user where usteamid='".$_SESSION['steamid']."'";
		
		$rsPd = mysql_query($qPd);
		$rowd = mysql_fetch_array($rsPd);
		extract($rowd);
		$id=trim($id);
		$profiletradeurl = trim($profiletradeurl);
		if($profiletradeurl != "")
			return "1";
		else
			return "0";
	}
	
	function getsteamurl()
	{
		$qget_steam_url_ge = "select id,profiletradeurl from user where usteamid='".$_SESSION['steamid']."'";
		
		$rsPd_get_steam_url = mysql_query($qget_steam_url_ge);
		$rowd_get_steam_url = mysql_fetch_array($rsPd_get_steam_url);
		extract($rowd_get_steam_url);
		//$steam_urlid=trim($id);
		$profiletradeurl_steam = trim($profiletradeurl);
		if($profiletradeurl_steam != "")
			return $profiletradeurl_steam;
		else
			return "0";
	}
	
	function checktos()
	{
		if($_SESSION['steamid'] != "")
		{
			$qget_checktos = "select check_tos from user where usteamid='".$_SESSION['steamid']."'";
			$rs_get_checktos = mysql_query($qget_checktos);
			$rowd_get_checktos = mysql_fetch_array($rs_get_checktos);
			//$steam_urlid=trim($id);
			$check_tos = trim($rowd_get_checktos['check_tos']);
			
			return $check_tos;
		}
		else
			return 1;
	}

?>