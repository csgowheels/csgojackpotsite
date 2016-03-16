<?php
include("connect.php");
include("header.php");

$pg = "sponser";
$num_rec_per_page=10;  // for paging
$act = $_REQUEST['act'];
if($act == "del")
{
	$id = $_GET['id'];
	$delete = "DELETE FROM sponser WHERE id = '$id' ";
	mysql_query($delete);
	$msg = "Entry deleted";
	header("Location: sponser.php?msg=".$msg);
	exit;
}
?>
<?php
/* for paging */
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 
/* for pagine end */
$q = "SELECT * FROM sponser LIMIT ".$start_from.",".$num_rec_per_page;
$result = mysql_query($q) or die(mysql_error());
$num = mysql_num_rows($result);
?>

<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>sponser</h2>
					
					<ul>
						<li><a href="addupdatesponser.php?act=add">Add New</a></li>
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
									<th>sponser Image</th>
									<th>Name</th>
                                    <th>URL</th>
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
							$spon_name = mysql_result($result,$i,"spon_name");
							$spon_img = mysql_result($result,$i,"spon_img");
							$spon_url = mysql_result($result,$i,"spon_url");
							$isactive = mysql_result($result,$i,"isactive");
							$id = mysql_result($result,$i,"id");
							?>
							
								<tr>
									<td><img src="../upload/<?php echo $spon_img;?>"  width="150px" /></td>
									<td><?php echo $spon_name;?></td>
                                    <td><?php echo $spon_url;?></td>
									<td><?php if($isactive == '1') { echo "Yes"; } else { echo "No"; }?></td>
									<td><?php echo "<a href=\"addupdatesponser.php?act=update&id=$id\">Edit</a>";?></td>
									<td class="delete"><?php echo "<a href=\"sponser.php?act=del&id=$id\" onclick=\"return confirm('Are you sure you want delete');\">Delete</a>";?></td>
								</tr>
							<?php
							++$i;
							 }
							} 
							else 
							{ echo "<tr><td colspan='12' align='center'>There is no Sponser Availalble</td></tr>"; }
							
							?>
	
								
							</tbody>
							
						</table>
					<?php 
					$sql = "SELECT * FROM sponser"; 
					$rs_result = mysql_query($sql); //run the query
					$total_records = mysql_num_rows($rs_result);  //count number of records
					$total_pages = ceil($total_records / $num_rec_per_page); 
					
					echo "<a href='sponser.php?page=1'>".'|<'."</a> "; // Goto 1st page  
					
					for ($i=1; $i<=$total_pages; $i++) { 
								echo "<a href='sponser.php?page=".$i."'>".$i."</a> "; 
					}; 
					echo "<a href='sponser.php?page=$total_pages'>".'>|'."</a> "; // Goto last page
					?>	
						
					
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
			</div>


<?php include("footer.php"); ?>