<?php
include("connect.php");
header("Content-Type: text/html; charset=UTF-8");
mysql_query("SET NAMES 'utf8'"); 
mysql_query('SET CHARACTER SET utf8');
mysql_query("SET NAMES utf8");

include("header.php");

$pg = "history";
$num_rec_per_page=10;  // for paging
$act = $_REQUEST['act'];
?>
<?php

/* for paging */
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 
/* for pagine end */
$q = "SELECT roundid,start_date,(select sum(totamt) from tradeitems where tradeitems.roundid=round.roundid) as potv, personname FROM round inner join user on round.winnersteamid = user.usteamid order by roundid DESC LIMIT ".$start_from.",".$num_rec_per_page;
	mysql_query("SET NAMES utf8");
$result = mysql_query($q) or die(mysql_error());
$num = mysql_num_rows($result);
?>

<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>History</h2>
					
				</div>		<!-- .block_head ends -->
				
				
				
				
				<div class="block_content">
				<?php if($_REQUEST['msg'] != "") { ?>
					<div class="message info" style="display: block;">
				<p><?=$_REQUEST['msg'];?></p>
				<span class="close" title="Dismiss"></span>
				</div>
				<?php } ?>
						<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
						
							<thead>
								<tr>
									<th>Round#</th>
									<th>Date of Play</th>
									<th>Pot Value</th>
									<th>Winner</th>
                                    <th>Commission</th>
								</tr>
							</thead>
							
							<tbody>
							
							<?

							if ($num > 0 ) {
							$i=0;
							
							while ($i < $num) {
							$roundid = mysql_result($result,$i,"roundid");
							$start_date = mysql_result($result,$i,"start_date");
							$potv = mysql_result($result,$i,"potv");
							$personname = mysql_result($result,$i,"personname");
							?>
							
								<tr>
									<td><?php echo $roundid;?></td>
									<td><?php echo $start_date;?></td>
									<td>$<?php echo round($potv,2);?></td>
									<td><?php echo $personname;?></td>
                                    <td>$<?php echo round(getcommision($roundid),2);?></td>
								</tr>
							<?php
							++$i;
							 }
							} 
							else 
							{ echo "<tr><td colspan='12' align='center'>There is no History Available</td></tr>"; }
							
							?>
	
								
							</tbody>
							
						</table>
					<?php 
					$sql = "SELECT roundid,start_date,(select sum(totamt) from tradeitems where tradeitems.roundid=round.roundid) as potv, personname FROM round inner join user on round.winnersteamid = user.usteamid  order by roundid DESC "; 
						mysql_query("SET NAMES utf8");
					$rs_result = mysql_query($sql); //run the query
					$total_records = mysql_num_rows($rs_result);  //count number of records
					$total_pages = ceil($total_records / $num_rec_per_page); 
					
					echo "<a href='history.php?page=1'>".'|<'."</a> "; // Goto 1st page  
					
					for ($i=1; $i<=$total_pages; $i++) { 
								echo "<a href='history.php?page=".$i."'>".$i."</a> "; 
					}; 
					echo "<a href='history.php?page=$total_pages'>".'>|'."</a> "; // Goto last page
					?>	
						
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>

<?php
function getcommision($roundid)
{
$q_get_won_price = "SELECT sum(totamt) AS wonprice FROM tradeitems WHERE roundid='".$roundid."' and AssetId in (select AssetId from senditem where partnerid in (select steamid64 from bot where type='commision'))";
	$r_get_won_price = mysql_query($q_get_won_price) or die(mysql_error());
	$ar_get_won_price = mysql_fetch_array($r_get_won_price);
	return $ar_get_won_price["wonprice"];

}
?>

<?php include("footer.php"); ?>