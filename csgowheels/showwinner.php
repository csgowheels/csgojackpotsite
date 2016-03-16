<?php 
	include "header2.php";
	//this page is used for trade decline.
?>

<div class="panel-heading">Winner</div>
<div class="feed-box panel-body"> Congratulations <br><br> You won the jackpot of <?php if(isset($_GET['potvalue']))
	 { $potvalue = $_GET['potvalue']; $potvalue = htmlspecialchars($potvalue); $potvalue = mysql_real_escape_string($potvalue); echo $potvalue; }?>
	 <br> Be careful for people who tries to scam, keep in mind that CS:GO Wheels Staff or Bots will never add you. </div>


<?php include "footer.php"; ?>