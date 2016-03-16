<?php 
	include "header2.php";
	
	//this page is used for trade decline.
	$reason_trade_decline = $_REQUEST['reason'];
	if($reason_trade_decline == "souvenir")
		$msg_decline = "You cannot deposit souvenir skin"; 
	else if($reason_trade_decline == "price5")
		$msg_decline = "Your items value was below $2"; 
	else if($reason_trade_decline == "item10")
		$msg_decline = "You cannot deposit more than 10 items.."; 
	else if($reason_trade_decline == "csgoitem")
		$msg_decline = "You can only deposit CS:GO items."; 
	else
		$msg_decline = "Due to some reason your offer was not accepted, please try depositing again."; 
?>  

<div class="panel-heading style="color: #ff0000;">TRADE OFFER DECLINED</div>
<div class="feed-box panel-body"> Your trade offer was declined because: <br><br> <?php echo $msg_decline ?> </div>

<?php 
	include "footer.php";
?>
