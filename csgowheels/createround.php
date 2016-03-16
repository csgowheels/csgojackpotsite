<?php

	// create winning percentage
	$max = 100.00;
	$min = 0.100000000;
	$round = 9;
	if ($min>$max) { $min=$max; $max=$min; }
    else { $min=$min; $max=$max; }

    $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    if($round>0)
        $randomfloat = round($randomfloat,$round);
	
	$winningpercentage_create =$randomfloat;
	
	// create round secreatkey
	
	function GeraHash($qtd) {
		//Under the string $Caracteres you write all the characters you want to be used to randomly generate the code.
		$Caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$QuantidadeCaracteres = strlen($Caracteres);
		$QuantidadeCaracteres--;
		
		$Hash=NULL;
	    for($x=1;$x<=$qtd;$x++){
	        $Posicao = rand(0,$QuantidadeCaracteres);
	        $Hash .= substr($Caracteres,$Posicao,1);
	    }
		
		return $Hash;
	}
	
	//Here you specify how many characters the returning string must have
	$roundsecreatkey=GeraHash(40);
	
	//create md5 round hash key
	$newhash = $roundsecreatkey."-".$winningpercentage_create;
	$hashkey = md5($newhash);
	
	// some database stuff
	include "connect.php";

	mysql_query("insert into round (roundhash,roundsecret,winningpercentage,winnersteamid,isactive,isfinished,start_date) values ('".$hashkey."','".$roundsecreatkey."','".$winningpercentage_create."','0','1','0', NOW())") or die(mysql_error());
	$r_id_for_feed = mysql_insert_id();
	// insert every record in feed table
	$cur_time = mysql_query("select now()");
	$row_cur_time = mysql_fetch_array($cur_time);
	$curr_time = $row_cur_time["0"];
	$insert_feed_round = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,roundid) values ('3','".$hashkey."','','','".$curr_time."','".$r_id_for_feed."')";
	mysql_query($insert_feed_round);

    

?>