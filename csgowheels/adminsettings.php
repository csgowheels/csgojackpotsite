<?php
	/*
	 * Page manipulation for admins
	 */

	include("connect.php");
	if((!isset($_SESSION['uid'])) && $_SESSION['login'] != '1')
	{
		header("location: login.php");
		exit;
	}
	
	$pg = "adminup";
	$act = $_REQUEST['act'];
	if($act == "save")
	{
		$pagename=$_POST["pagename"];
		$parentid=$_POST["parentid"];
		$pagetitle=$_POST["pagetitle"];
		$pagecontent=$_POST["pagecontent"];
		$isactive=$_POST["isactive"];
		
		// security update
		$pagename = mysql_real_escape_string($pagename);
		$parentid = mysql_real_escape_string($parentid);
		$pagetitle = mysql_real_escape_string($pagetitle);
		$pagecontent = mysql_real_escape_string($pagecontent);
		$isactive = mysql_real_escape_string($isactive);
	
		echo $query = "insert into pages(parentid,pagename,pagetitle,pagecontent,isactive) values('$parentid','$pagename','$pagetitle','$pagecontent','$isactive')";
		$results = mysql_query($query) or die(mysql_error());
	
		if ($results)
		{
			header("Location: cms.php?msg=Page Added Successfully.");
			exit;
		}
		else
			$_REQUEST['msg'] = "Sorry, Not able to add page. Please try again";
	}
	
	if($act == "up")
	{
		$id = $_POST['id'];
		$pagename=$_POST["pagename"];
		$parentid=$_POST["parentid"];
		$pagetitle=$_POST["pagetitle"];
		$pagecontent=$_POST["pagecontent"];
		$isactive=$_POST["isactive"];
		
		// security updates
		$id = mysql_real_escape_string($id);
		$pagename = mysql_real_escape_string($pagename);
		$parentid = mysql_real_escape_string($parentid);
		$pagetitle = mysql_real_escape_string($pagetitle);
		$pagecontent = mysql_real_escape_string($pagecontent);
		$isactive = mysql_real_escape_string($isactive);
	
		$update = "update pages set parentid='$parentid',pagename='$pagename',pagetitle='$pagetitle',pagecontent='$pagecontent',isactive='$isactive' where id='$id'";
		$rsUpdate = mysql_query($update);
		if ($rsUpdate)
		{
			header("Location: cms.php?msg=Updated Successfully.");
			exit;
		}
		else
			$_REQUEST['msg'] = "Sorry, Not able to update. Try again.";
	}
	
	$id = $_GET['id'];
	$id = mysql_real_escape_string($id);
	
	$qP = "SELECT * FROM pages WHERE id = '$id'  ";
	$rsP = mysql_query($qP);
	$row = mysql_fetch_array($rsP);
	extract($row);
	$pagename=trim($pagename);
	$parentid=trim($parentid);
	$pagetitle=trim($pagetitle);
	$pagecontent=trim($pagecontent);
	$isactive=trim($isactive);
	
	include("header.php");
	include("editor.php");
	if($act == "update")
	{
?>

	<div class="block">
		<div class="block_head">
			<div class="bheadl"></div>
			<div class="bheadr"></div>
			<h2>Update Page:  <?=$cms_title;?></h2>
		</div>		<!-- .block_head ends -->
		<div class="block_content">	
			<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
			<?php  } ?>
			<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
					<label>Page Name</label><br />
					<input id="pagename" name="pagename" type="text"  value="<?php echo $pagename; ?>" class="text small">
				</p>
				<p>
					<label>Parent Page</label><br />
                    <select name="parentid" id="parentid"  class="styled">
                        <option value="0" <?php if($isactive == '0') { echo "selected"; } ?>>-No Parent-</option>
                        <option value="1" <?php if($isactive == '1') { echo "selected"; } ?>>1</option>
                    </select>
				</p>
	            <p>
					<label>Page Title</label><br />
					<input id="pagetitle" name="pagetitle" type="text"  value="<?php echo $pagetitle; ?>" class="text small">
				</p>
                <p>
					<label>Page Content</label><br />
					<textarea id="pagecontent" name="pagecontent" class="mceEditor"> <?php echo $pagecontent; ?></textarea>
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
					<label>Page Name</label><br />
					<input id="pagename" name="pagename" type="text"  value="" class="text small">
				</p>
				<p>
					<label>Parent Page</label><br />
                    <select name="parentid" id="parentid"  class="styled">
                        <option value="0" selected="selected">-No Parent-</option>
                        <option value="1">1</option>
                    </select>
				</p>
	            <p>
					<label>Page Title</label><br />
					<input id="pagetitle" name="pagetitle" type="text"  value="" class="text small">
				</p>
        	    <p>
					<label>Page Content</label><br />
					<textarea id="pagecontent" name="pagecontent"  class="mceEditor"></textarea>
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