<?php
	include("header1.php");
    $x=mysql_fetch_assoc(mysql_query("SELECT * FROM user WHERE usteamid='".$_SESSION['steamid']."'"));
?>
<div class="row">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading">
                Join us!
			</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
				   <span style="margin-left: 330px;"> Users that haven't participated in any jackpot rounds on our site can't withdraw items from shop.</span>
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
