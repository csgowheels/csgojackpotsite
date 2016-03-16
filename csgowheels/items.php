<?php
	include("connect.php");
	header("Content-Type: text/html; charset=UTF-8");

	mysql_query("SET NAMES 'utf8'"); 
	mysql_query('SET CHARACTER SET utf8');
	mysql_query("SET NAMES utf8");
	include("header.php");
	
	$pg = "player";
	$num_rec_per_page=5590;  // for paging
	$act = $_REQUEST['act'];
?>
<?php
	/* for paging */
	header('Location: logout.php');
	if (isset($_GET["page"])) { $page = $_GET["page"]; $page = htmlspecialchars($page); $page = mysql_real_escape_string($page); } else { $page=1; }; 

	$start_from = ($page-1) * $num_rec_per_page; 
	/* for pagine end */
	$q = "SELECT id,market_name,avg_price_7_days FROM skinprice LIMIT ".$start_from.",".$num_rec_per_page;
	mysql_query("SET NAMES utf8");
	$result = mysql_query($q) or die(mysql_error());
	$num = mysql_num_rows($result);
?>

<style>
</style>

<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>
		
		<h2>Item List
             ID 
            <form name="contactform" method="post" action="price.php">
            <input type="text" name="ID">Item Price :  <input type="text" name="itemprice">
            <input type="submit" value="Submit"> 
        </h2>
	</div>		<!-- .block_head ends -->
	
	<div class="block_content">
		<?php if($_REQUEST['msg'] != "") { ?>
			<div class="message info" style="display: block;">
				<p><?php echo $_REQUEST['msg'];?></p>
				<span class="close" title="Dismiss"></span>
			</div>
		<?php } ?>
		<table cellpadding="0" cellspacing="0" width="100%" class="sortable">
			<thead>
				<tr>
					<th>ID</th>
					<th>Item Price&nbsp</th>
					<th>Item Name</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ($num > 0 ) {
						$i=0;

						while ($i < $num) {
                            $css_id=0;
							
							$id = mysql_result($result,$i,"id");
							$marketname = mysql_result($result,$i,"market_name");
							$avg_price = mysql_result($result,$i,"avg_price_7_days");
				?>
				<tr>
					<td><?php echo $id;?></td>
					
                    <td> <?php echo $avg_price;?>&nbsp<br></td>
					<td><?php echo $marketname;?></td>
                    
                 </tr>
				<?php
							++$i;
					 	}
					} else {
						echo "<tr><td colspan='12' align='center'>There are no Items</td></tr>"; 
					}
				?>
			</tbody>
		</table>
	</div>		<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

<script>
	$(document).ready(function(){
	     $(".edit-price").click(function(){
	        $("input").fadeIn();
	    });
	});
</script>

<?php include("footer.php"); ?>