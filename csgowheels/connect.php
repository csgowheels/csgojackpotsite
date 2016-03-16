<?php 
	@session_start();
	error_reporting(0);
	
	$hostname='localhost'; //// specify host, i.e. 'localhost'
	$user='root'; //// specify username
	$pass=''; //// specify password
	$dbase='csgowhee_csgo'; //// specify database name
	
/*
	$hostname='localhost'; //// specify host, i.e. 'localhost'
	$user='csgowhee_luka'; //// specify username
	$pass='dn12384dn12384'; //// specify password
	$dbase='csgowhee_csgo'; //// specify database name
*/

	/* live end */
	$connection = mysql_connect("$hostname" , "$user" , "$pass") or die ("Can't connect to MySQL");
	mysql_set_charset('utf8',$connection);
	$db = mysql_select_db($dbase , $connection) or die ("Can't select database.");
	mysql_query("SET SQL_BIG_SELECTS=1");
   
?>
