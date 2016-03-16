<?php
include "connect.php";
mysql_query("insert into general (settings_name) VALUES (NOW())");
if(isset($_GET['timer'])) {
    $time=time();
    $x=mysql_fetch_assoc(mysql_query("SELECT * FROM timer_start WHERE id=0"));

    if ($time - $x['time_started'] >= 56 && $time - $x['time_started'] <= 65) {

        $locked = mysql_fetch_assoc(mysql_query("SELECT * FROM locked WHERE id=1"));
        $locked = $locked['locked'];
    if ($locked == 1) die("end");
    mysql_query("UPDATE locked SET locked=1 WHERE id=1");
    $locked = 1;
    }
    else
        die("end");
    }
else 
    die("end");

if ($locked != 1) die("end"); // do some error handling here (starts again or whatever..)


$time=time();
$currentround_query = mysql_query("SELECT * FROM round WHERE isactive = '1' AND isfinished = '0' ORDER BY roundid ASC LIMIT 1 ");
if(mysql_num_rows($currentround_query)==0) die();
$is_timer = $_GET['timer'];
$ar_roundcurrentid = mysql_fetch_array($currentround_query);
$current_roundid = $ar_roundcurrentid['roundid'];
$winningpercentage = $ar_roundcurrentid['winningpercentage'];
$winningsecreat = $ar_roundcurrentid['roundsecret'];
$round_start_time=intval($ar_roundcurrentid['start_date']);
//echo $round_start_time;
$total_users=mysql_query("select distinct  steamid FROM round_points WHERE roundid='".$current_roundid."'");
$num_rows = mysql_num_rows($total_users);
$get_curr_time=time();


 if($num_rows>=2 && isset($is_timer) && ( ($get_curr_time-45) > $round_start_time ) )
	{
  
		
		//start jackpot 
		mysql_query("update round set isfinished ='1' where roundid='".$current_roundid."'");
		
		$totalticketround_query = mysql_query("select sum(tickettot) from round_points where roundid='".$current_roundid."'");
		$ar_totticketcurround = mysql_fetch_array($totalticketround_query);
		$cnt_ticket_round = $ar_totticketcurround['0'];
		$winningticket = (floatval($cnt_ticket_round) * floatval($winningpercentage)) /100;
		$winningticket = $winningticket;
        echo $winningticket;
    
        // select winner id 
		$q_select_winner = "select id,steamid,minrange,maxrange from round_points where ( ".$winningticket." BETWEEN minrange and maxrange)  and roundid='".$current_roundid."'";
		$ar_select_winner = mysql_query($q_select_winner) or die(mysql_error());
        if(mysql_num_rows($ar_select_winner)==0){
            
            $winningticket=$winningticket-1;
            $q_select_winner = "select id,steamid,minrange,maxrange from round_points where ( ".$winningticket." BETWEEN minrange and maxrange)  and roundid='".$current_roundid."'";
		$ar_select_winner = mysql_query($q_select_winner) or die(mysql_error());
            
            
        }
		$ar_select_winner_arr = mysql_fetch_array($ar_select_winner);
		$winner_id = $ar_select_winner_arr["0"];
		$winner_steamid = $ar_select_winner_arr["1"];
		$winner_minrange = $ar_select_winner_arr["minrange"]/$cnt_ticket_round;
		$winner_maxrange = $ar_select_winner_arr["maxrange"]/$cnt_ticket_round;
		
		
        

    
    
        // add points to the winner
        $winnings=mysql_fetch_assoc(mysql_query("SELECT * FROM user where usteamid='".$winner_steamid."'"));
        $winning=$winnings['points']+ intval($ar_totticketcurround['0'])*0.95;
        $prev_points=$winnings['points'];
        mysql_query("insert into point_transfer (steam_id,value,added,previous_points,current_points,executer) values ('".$winner_steamid."',".(intval($ar_totticketcurround['0'])*0.95).",1,".$prev_points.",".round($winning). ", 'php') ");
        
        
       
        
        mysql_query("UPDATE user SET points=".round($winning)." WHERE usteamid='".$winner_steamid."'");
        
        
    
        //insert winning in feed
		
		$curr_time = time();
    
		mysql_query("update round set winnersteamid = '".$winner_steamid."',start_date = '".$curr_time."',min_percent='".$winner_minrange."',max_percent='".$winner_maxrange."',start_spin='1' where roundid='".$current_roundid."'"); 
		
		$insert_feed = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,feed_user,roundid) values ('2','','".$cnt_ticket_round*0.01*0.95."','','".$curr_time."','".$winner_steamid."','".$current_roundid."')";
		mysql_query($insert_feed);
		
		$insert_feed2 = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,feed_user,roundid) values ('4','".$winningsecreat."','".$winningpercentage."','','".$curr_time."','".$winner_steamid."','".$current_roundid."')";
		mysql_query($insert_feed2);	
        

        //creates new round
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
       
        $cur_time = time();
        mysql_query("insert into round (roundhash,roundsecret,winningpercentage,winnersteamid,isactive,isfinished,start_date) values ('".$hashkey."','".$roundsecreatkey."','".$winningpercentage_create."','0','1','0', ".$cur_time.")") or die(mysql_error());
        $r_id_for_feed = mysql_insert_id();
        // insert every record in feed table
        
        
        $insert_feed_round = "insert into feed (feedtype,itname,ittotprice,iturl,date_time_feed,roundid) values ('3','".$hashkey."','','','".$cur_time."','".$r_id_for_feed."')";
        mysql_query($insert_feed_round);
    
   
    
        mysql_query("UPDATE locked SET locked=0 WHERE id=1");
 
   }
    else {
		// release lock
		  mysql_query("UPDATE locked SET locked=0 WHERE id=1");
	}
    
  

   
    
    ?>
    