<?php
include("connect.php");
include("header.php");

$pg = "settings";
$act = $_REQUEST['act'];
if($act == "save")
{
	$settings_name=$_POST["settings_name"];
	$settings_value=$_POST["settings_value"];
	$isactive=$_POST["isactive"];
	

	echo $query = "insert into general(settings_name,settings_value,isactive) values('$settings_name','$settings_value','$isactive')";
	$results = mysql_query($query) or die(mysql_error());

	if ($results)
	{

	header("Location: settings.php?msg=Page Added Successfully.");
	exit;
	}
	else
	{
		$_REQUEST['msg'] = "Sorry, Not able to add Settings. Please try again";
	}
}


if($act == "up")
{
	$id = $_POST['id'];
	$settings_name=$_POST["settings_name"];
	$settings_value=$_POST["settings_value"];
	$isactive=$_POST["isactive"];

	$update = "update general set settings_name='$settings_name',settings_value='$settings_value',isactive='$isactive' where id='$id'";
	$rsUpdate = mysql_query($update);
	if ($rsUpdate)
	{
	header("Location: settings.php?msg=Updated Successfully.");
	exit;
	}
	else
	{
		$_REQUEST['msg'] = "Sorry, Not able to update. Try again.";
	}
}


$id = $_GET['id'];

$qP = "SELECT * FROM general WHERE id = '$id'  ";
$rsP = mysql_query($qP);
$row = mysql_fetch_array($rsP);
extract($row);
$settings_name=trim($settings_name);
$settings_value=trim($settings_value);
$isactive=trim($isactive);


?>
<?php
include("editor.php");
?>
<?php 
if($act == "update")
{
?>

	<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Update Settings For:  <?=$settings_name;?></h2>
				</div>		<!-- .block_head ends -->
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
						<label>Settings</label><br />
						<input id="settings_name" name="settings_name" type="text"  value="<?php echo $settings_name; ?>" class="text small">
				</p>
                <p>
						<label>Value</label><br />
						<input id="settings_value" name="settings_value" type="text"  value="<?php echo $settings_value; ?>" class="text small">
				</p>
                <p>
						<label>Status</label><br />
						<select name="isactive" id="isactive"  class="styled">
						<option value="0"<?php if($isactive == '0') { echo "selected"; } ?>>InActive</option>
						<option value="1" <?php if($isactive == '1') { echo "selected"; } ?>>Active</option>
						</select>
				</p>
				<p>
													
				        <input type="hidden" name="act" value="up" />
						<input type="submit" name="submit" value="Update" class="submit small">
                        <input type="hidden" name="id" value="<?=$id?>">&nbsp;
						<input type="button" name="cancel" value="Back" onclick="history.back()" class="submit small" />
							
				</p>
				</form>
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>

<?php 
}
?>
<?php 
if($act == "add")
{
?>

<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Add new Page</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
						<label>Settings</label><br />
						<input id="settings_name" name="settings_name" type="text"  value="" class="text small">
				</p>
				<p>
						<label>Value</label><br />
						<input id="settings_value" name="settings_value" type="text"  value="" class="text small">
				</p>
				
                <p>
						<label>Status</label><br />
						<select name="isactive" id="isactive"  class="styled">
						<option value="0">InActive</option>
						<option value="1">Active</option>
						</select>
				</p>
				<p>
													
				       	<input type="hidden" name="act" value="save" />
						<input type="submit" name="submit" value="Submit" class="submit small">
						<input type="button" name="cancel" value="Back" onclick="history.back()" class="submit small" />
							
				</p>
				</form>
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>



<?php
} 
?>

<?php include("footer.php");?>