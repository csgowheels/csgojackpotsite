<?php
include("connect.php");
include("header.php");
$pg = "settings";
$num_rec_per_page=10;  // for paging
$act = $_REQUEST['act'];
if($act == "del")
{
	$id = $_GET['id'];
	$delete = "DELETE FROM general WHERE id = '$id' ";
	mysql_query($delete);
	$msg = "Entry deleted";
	header("Location: settings.php?msg=".$msg);
	exit;
}
?>
<?php
/* for paging */
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 
/* for pagine end */
$q = "SELECT * FROM general LIMIT ".$start_from.",".$num_rec_per_page;
$result = mysql_query($q) or die(mysql_error());
$num = mysql_num_rows($result);
?>

<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Settings</h2>
					
					<ul>
						<li><a href="addupdatesettings.php?act=add">Add New</a></li>
					</ul>
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
									<th>Settings</th>
									<th>Value</th>
									<th>Active</th>
									<th colspan="2" align="center"><center>Action</center></th>
								</tr>
							</thead>
							
							<tbody>
							
							<?

							if ($num > 0 ) {
							$i=0;
							?>
							<?
							while ($i < $num) {
							$settings_name = mysql_result($result,$i,"settings_name");
							$settings_value = mysql_result($result,$i,"settings_value");
							$isactive = mysql_result($result,$i,"isactive");
							$id = mysql_result($result,$i,"id");
							?>
							
								<tr>
									<td><?php echo $settings_name;?></td>
									<td><?php echo $settings_value;?></td>
									<td><?php if($isactive == '1') { echo "Yes"; } else { echo "No"; }?></td>
									<td><?php echo "<a href=\"addupdatesettings.php?act=update&id=$id\">Edit</a>";?></td>
									<td class="delete"><?php echo "<a href=\"settings.php?act=del&id=$id\" onclick=\"return confirm('Are you sure you want delete');\">Delete</a>";?></td>
								</tr>
							<?php
							++$i;
							 }
							} 
							else 
							{ echo "<tr><td colspan='12' align='center'>There is no Settings Availalble</td></tr>"; }
							
							?>
	
								
							</tbody>
							
						</table>
					<?php 
					$sql = "SELECT * FROM general"; 
					$rs_result = mysql_query($sql); //run the query
					$total_records = mysql_num_rows($rs_result);  //count number of records
					$total_pages = ceil($total_records / $num_rec_per_page); 
					
					echo "<a href='settings.php?page=1'>".'|<'."</a> "; // Goto 1st page  
					
					for ($i=1; $i<=$total_pages; $i++) { 
								echo "<a href='settings.php?page=".$i."'>".$i."</a> "; 
					}; 
					echo "<a href='settings.php?page=$total_pages'>".'>|'."</a> "; // Goto last page
					?>	
						
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>


<?php include("footer.php"); ?>