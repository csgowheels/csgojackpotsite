<?php
	include("header1.php");
?>

<style>
	.spacingtd
	{
		padding:10px !important;";
	}
	.bigtd
	{
		font-family:Verdana, Geneva, sans-serif;
		font-size:36px !important;
		padding-left:20px !important;
		padding-right:20px !important;
		padding-top:20px !important;
		padding-bottom:5px;
	}
</style>

<?php
	//Get steam details for the user.
	$q_profile = "select * from user where usteamid='".$_SESSION['steamid']."'";
	$rs_profile = mysql_query($q_profile);
	$row_profile = mysql_fetch_array($rs_profile);
	extract($row_profile);
	$id=trim($id);
	$personname=trim($personname);
	$avatar=trim($avatar);
	$avatarfull=trim($avatarfull);
	$realname=trim($realname);
	$lastlogoff=trim($lastlogoff);
	$profileurl=trim($profileurl);
?>

<?php
//insert trade url
	if($_POST['urlbox'] != "" && $_POST['flagurladd']== "1")
	{
	    $_POST['urlbox'] = mysql_real_escape_string($_POST['urlbox']);
		$_POST['urlbox'] = htmlspecialchars($_POST['urlbox']);
		$q_addurl_update = "update user set profiletradeurl = '".$_POST['urlbox']."' where usteamid='".$_SESSION['steamid']."'";
		
		if(mysql_query($q_addurl_update))
			header('Location: https://csgowheels.com');
		else
			echo "<style>input[type='text']{box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(255, 0, 0, 0.6);}</style>";
	}
   
       
?>

<div class="row" style="overflow-x:hidden;">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading" style="font-size:15px;" >Profile</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
                    <table>
                    	<tr>
                        	<td rowspan="3" style="padding:10px;">
                            	<img src="<?php echo $avatarfull;?>" />
                            </td>
                        	<td valign="top" style="padding:40px; font-size:15px;font-family: 'Verdana';">
                            	<b></b> <?php echo $personname;?><br>
                            <br>
                        		<b></b> <a href='<?php echo $profileurl; ?>' target="_blank"><img src="images/social/steam_normal.png"></a>
                            </td>
							<td style="padding-left: 12%"><img id="csgoImg" style="padding:0; opacity: 0.8;filter: alpha(opacity=40);" src="images/csgo3.png" height="238" width="676"></td>
                        </tr>
                    </table>
                </div>
			</div>
		</div>
		
        <div class="panel panel-default"></div>
              
        <div class="col-md-3" style="width:100%;">
			<div class="panel panel-default">
				<div class="panel-heading">Settings</div>
				<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
					<div class="row" style="padding:10px;">
                        <?php if($msg != "") { echo $msg; ?><br /><?php } ?>
                        	<strong>Trade URL:</strong> <a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank"> Where can I find my link?</a> <br />
                        <?php 
							$st_add_url=getsteamurl();
							if($st_add_url == "0")
								$st_add_url = "";
						?><br />
                        <form name="addurl" method="post">
	                        <input type="text" name="urlbox" id="urlbox" value="<?php echo $st_add_url;?>" size="100px"><br /><br />
	                        <input type="hidden" name="flagurladd" value="1">
	                        <input type="submit" class="submitButton" name="submit" value="Save">
                        </form>
                    </div>
                    In order to receive winnings you need to set the correct trade URL also dont forget to set your profile and inventory to public.
				</div>
			</div>
		</div>
	</div> 
 </div>
                                   
<?php
	include("footer.php");
?>