<?php
	include("header1.php");
    $x=mysql_fetch_assoc(mysql_query("SELECT * FROM user WHERE usteamid='".$_SESSION['steamid']."'"));
?>
<div class="row">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading">
                Refer a Friend
			</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
					
					Earn items by referring players to our site !
                    <br>
                    <br>
                    From now on, you can advertise for us and recieve an item for every new user that logs in on our site using your referal link below ! 
					<br>
                    <br>
                    <br>
                    <br>
                    <b>Your Referral Link :</b> https://csgowheels.com/?ref=<?php echo $x['referal'] ?>
                    <br>
                    <br>
                    <b>FAQ</b>
					<br>
                    <br>
                    <b>1. How will I receive the item when someone joins the site with my link?</b>
					<br>
                    
                      <span style="margin-left:10px;"> As soon as someone you referred logs in on the site, you will receive 5 Points, which then you can use to redeem item..</span>
                    <br>
                    <br>
                    <b>2. What kind of item do I get as a reward?</b>
                    <br>
                    
                      <span style="margin-left:10px;"> All rewards are CSGO items of course. Items you reedeem will have diffrent values, the more expensive items will cost more points logically. </span>
                    
                     <br>
                    <br>
                    <b>3. Is there a limit for maximum referred users?</b>
                    <br>
                    
                      <span style="margin-left:10px;"> There isn't. You may refer as much players as you like, and each time you will receive 5 Points for it no matter how many people you recruit. </span>
                     <br>
                    <br>
                    <b>4. I recruited someone, but I haven't received reward yet, what should I do?</b>
                    <br>
                    
                      <span style="margin-left:10px;"> Several reasons may have caused this problem. The first thing to do is to check if your referred friend has CS:GO on his Steam account (this is the measure we take against spammers).</span> <br><span style="margin-left:10px;"> If he has a game on his account, make sure to wait one hour and then check again. If none of the above helped, you can contact us on our "LIVE SUPPORT" or on email. </span>
                     <br>
                    <br>
                    <b>5. I'm stuck on "Creating trade offer", what's the problem?</b>
                    <br>
                    <span style="margin-left:10px;">Our bots send tradeoffers to users that have mobile authentication enabled for at least 7 days. Also make sure that you have added Steam trade URL on our site. </span>
                    <br>
                    
                    <img src="images/csgo_bigger_gif.gif" width=450 style="margin-left:30%;">
                    
                    
					</table>
                </div>
			</div>
		</div>
	</div>
</div> 


    
    

	
<?php
	include("footer.php");
?>
