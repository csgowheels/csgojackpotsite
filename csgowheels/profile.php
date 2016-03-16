<?php
	include("header1.php");
?>

<style>
	.akLeft{
		width:100px !important;
	}
	.akRight{
		width:100px !important;
	}
	.spacingtd
	{
		padding:10px !important;";
	}
	.bigtd
	{
		font-family:Verdana, Geneva, sans-serif;
		font-size:30px !important;
		padding-left:5px !important;
		padding-right:20px !important;
		padding-top:20px !important;
		padding-bottom:5px;
	}
</style> 

<?php
	//Get steam details for the user.

	$q_profile = "select * from user where usteamid='".$_SESSION['steamid']."'";
    
    
	$rs_profile = mysql_query($q_profile);
    $xx=mysql_fetch_assoc(mysql_query("select * from user where usteamid='".$_SESSION['steamid']."'"));
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
                            	<td valign="top" style="padding:45px; font-size:15px;font-family: 'Verdana';">
                                	<b></b> <?php echo $personname;?><br> Points:  <?php echo $xx['points']; ?><br> Referred: <?php echo $xx['referred']; ?>
		                                <br>
                            		<b></b> <a href='<?php echo $profileurl; ?>' target="_blank"><img src="images/social/steam_normal.png"> </a>
                                </td>
								<td style="padding-left: 12%"><img style="padding:0; opacity: 0.8;filter: alpha(opacity=40);" id ="csgoImg" src="images/csgo3.png" height="238" width="676"></td>
							</tr>
                        </table>
                    </div>
				</div>
			</div>
			
            <div class="panel panel-default">
            <div class="panel-heading">Jackpot History</div>      

			<div style="margin: 0 auto;" class="tableCont">
				<!--<img src="images/ak47left.png"  class="akLeft" style="float:left; width:100%"></img>-->

                <table style="width:80%; margin:0 auto;">
                	<tr>
                    	<td class="bigtd" align="center" style="color:#0F6;">
                    		<?php
                    			$totwon=getwonprice();
								if($totwon== 0 || $totwon == "" ||$totwon == NULL)
								{	
									echo "$0"; 
									$totwon = 0;
								}
								else
								{
									$totwon =  round($totwon,2);
									echo "$".$totwon;
								}
							?>
						</td>
                        <td  class="bigtd"  align="center">-</td>
                        <td  class="bigtd"  align="center" style=" color:#F00;">
                        	<?php
	                            $yourbat_tot=yourbat();
								if($yourbat_tot== 0 || $yourbat_tot == "" ||$yourbat_tot == NULL)
								{	
									echo "$0"; 
									$yourbat_tot = 0;
								}
								else
								{
									$yourbat_tot = round($yourbat_tot,2);
									echo "$".$yourbat_tot;
								}
							?>
                        </td>
                        <td  class="bigtd"  > = </td>
                        <?php 
                        	$tot_won_bat=$totwon-$yourbat_tot;
							if($tot_won_bat > 0)
								$total_color ='#0F6';
							else
								$total_color ='#F00';
							$tot_won_bat = " $".$tot_won_bat;
						?>
                        <td class="bigtd"  align="center" style="color:<?php echo $total_color?>;"><?php echo str_replace("$-","-$",$tot_won_bat);?></td>
                     </tr>
                     <tr>
                         <td class="spacingtd" align="center">Won</td>
                         <td class="spacingtd"></td>
                         <td class="spacingtd" align="center">Deposited</td>
                         <td class="spacingtd" align="center"></td>
                         <td class="spacingtd" align="center" >Profit </td>
                     </tr>
                </table>     
				<!--<img src="images/ak47right.png"  class="akRight" style="float:left; width:100%"></img>-->
				<br><br><br>
            </div>
		</div>
	</div> 
</div>
<div style="height:50px;">&nbsp;</div>
        
<?php                        
	function yourbat()
	{
		$q_get_your_bat_history1 = "SELECT sum(points)/100 FROM round_points WHERE steamid= '".$_SESSION['steamid']."'";
		$r_get_your_bat_history1 = mysql_query($q_get_your_bat_history1) or die(mysql_error());
		$ar_get_your_bat_history1 = mysql_fetch_array($r_get_your_bat_history1);
		return $ar_get_your_bat_history1["0"];
	}  
	
	function getwonprice()
	{
		$q_get_won_price = "SELECT sum(points)/100 AS wonprice FROM round_points WHERE roundid in (select roundid from round where winnersteamid='".$_SESSION['steamid']."')";
		$r_get_won_price = mysql_query($q_get_won_price) or die(mysql_error());
		$ar_get_won_price = mysql_fetch_array($r_get_won_price);
		return $ar_get_won_price["wonprice"];
	}

	include("footer.php");
?>