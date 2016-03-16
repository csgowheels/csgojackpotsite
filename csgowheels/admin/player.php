<?php
include("connect.php");
header("Content-Type: text/html; charset=UTF-8");
mysql_query("SET NAMES 'utf8'"); 
mysql_query('SET CHARACTER SET utf8');
mysql_query("SET NAMES utf8");
include("header.php");

$pg = "player";
$num_rec_per_page=10;  // for paging
$act = $_REQUEST['act'];

?>
<?php
/* for paging */
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 
/* for pagine end */
$q = "SELECT avatar,personname,profileurl,profiletradeurl FROM user LIMIT ".$start_from.",".$num_rec_per_page;
mysql_query("SET NAMES utf8");
$result = mysql_query($q) or die(mysql_error());
$num = mysql_num_rows($result);
?>

<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Player List</h2>
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
                                	
									<th>Avatar</th>
									<th>Player Name</th>
									<th>Steam Profile URL</th>
									<th>Steam Trade URL</th>
								</tr>
							</thead>
							
							<tbody>
							
							<?

							if ($num > 0 ) {
							$i=0;
							?>
							<?
							while ($i < $num) {
							$avatar = mysql_result($result,$i,"avatar");
							$personname = mysql_result($result,$i,"personname");
							$profileurl = mysql_result($result,$i,"profileurl");
							$profiletradeurl = mysql_result($result,$i,"profiletradeurl");
							?>
							
								<tr>
									<td><img src="<?php echo $avatar;?>"></td>
									<td><?php echo $personname;?></td>
									<td><a href="<?php echo $profileurl;?>" target="_blank"><?php echo $profileurl;?></a></td>
                                    <td><a href="<?php echo $profiletradeurl;?>"><?php echo $profiletradeurl;?></a></td>
                                 </tr>
							<?php
							++$i;
							 }
							} 
							else 
							{ echo "<tr><td colspan='12' align='center'>There is no Player Availalble</td></tr>"; }
							
							?>
	
								
							</tbody>
							
						</table>
					<?php 
					$sql = "SELECT avatar,personname,profileurl,profiletradeurl FROM user"; 
					mysql_query("SET NAMES utf8");
					$rs_result = mysql_query($sql); //run the query
					$total_records = mysql_num_rows($rs_result);  //count number of records
					$total_pages = ceil($total_records / $num_rec_per_page); 
					
					echo "<a href='player.php?page=1'>".'|<'."</a> "; // Goto 1st page  
					
					for ($i=1; $i<=$total_pages; $i++) { 
								echo "<a href='player.php?page=".$i."'>".$i."</a> "; 
					}; 
					echo "<a href='player.php?page=$total_pages'>".'>|'."</a> "; // Goto last page
					?>	
						
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>


<?php include("footer.php"); ?>