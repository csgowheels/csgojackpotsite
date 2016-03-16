<?php
include("connect.php");
include("header.php");

$pg = "sponser";
$act = $_REQUEST['act'];
if($act == "save")
{
	
	
	$spon_name=$_POST["spon_name"];
	$spon_url=$_POST["spon_url"];
	$isactive=$_POST["isactive"];
	
	/* upload file */
	  $allowedExts = array("gif", "jpeg", "jpg", "png");
      $temp = explode(".", $_FILES["spon_img"]["name"]);
      $extension = end($temp);
	  
      if ((($_FILES["spon_img"]["type"] == "image/gif")
      || ($_FILES["spon_img"]["type"] == "image/jpeg")
      || ($_FILES["spon_img"]["type"] == "image/jpg")
      || ($_FILES["spon_img"]["type"] == "image/pjpeg")
      || ($_FILES["spon_img"]["type"] == "image/x-png")
      || ($_FILES["spon_img"]["type"] == "image/png"))
      && in_array($extension, $allowedExts))
      {
	  
        if ($_FILES["spon_img"]["error"] > 0)
          {
          echo "Return Code: " . $_FILES["spon_img"]["error"] . "<br>";
          }
        else 
          {

            $fileName = $temp[0].".".$temp[1];
            $temp[0] = rand(0, 3000); //Set to random number
            $fileName;

          if (file_exists("../upload/" . $_FILES["spon_img"]["name"]))
            {
            echo $_FILES["spon_img"]["name"] . " already exists. ";
            }
          else
            {
				move_uploaded_file($_FILES["spon_img"]["tmp_name"], "../upload/" . $_FILES["spon_img"]["name"]);
				//echo "Stored in: " . "../upload/" . $_FILES["file"]["name"];
				$query = "insert into sponser(spon_name,spon_img,spon_url,isactive) values('$spon_name','".$_FILES["spon_img"]["name"]."','$spon_url','$isactive')";
				$results = mysql_query($query) or die(mysql_error());
				if ($results)
				{
				header("Location: sponser.php?msg=Page Added Successfully.");
				exit;
				}
				else
				{
					$_REQUEST['msg'] = "Sorry, Not able to add sponser. Please try again";
				}
            }
          }
        }
      else
        {
        echo "Invalid file";
        }
	/* upload file end */
	
	
	
	

	
}


if($act == "up")
{

	$id = $_POST['id'];
	$spon_name=$_POST["spon_name"];
	$spon_url=$_POST["spon_url"];
	$isactive=$_POST["isactive"];

	/* upload file */
	  if($_FILES["spon_img"]["name"] != "")
	  {	
		  $allowedExts = array("gif", "jpeg", "jpg", "png");
		  $temp = explode(".", $_FILES["spon_img"]["name"]);
		  $extension = end($temp);
		  
		  if ((($_FILES["spon_img"]["type"] == "image/gif")
		  || ($_FILES["spon_img"]["type"] == "image/jpeg")
		  || ($_FILES["spon_img"]["type"] == "image/jpg")
		  || ($_FILES["spon_img"]["type"] == "image/pjpeg")
		  || ($_FILES["spon_img"]["type"] == "image/x-png")
		  || ($_FILES["spon_img"]["type"] == "image/png"))
		  && in_array($extension, $allowedExts))
		  {
		  
			if ($_FILES["spon_img"]["error"] > 0)
			  {
			  $msg .="Return Code: " . $_FILES["spon_img"]["error"] . "<br>";
			  }
			else 
			  {
	
				$fileName = $temp[0].".".$temp[1];
				$temp[0] = rand(0, 3000); //Set to random number
				$fileName;
	
			  if (file_exists("../upload/" . $_FILES["spon_img"]["name"]))
				{
				echo $_FILES["spon_img"]["name"] . " already exists. ";
				}
			  else
				{
					move_uploaded_file($_FILES["spon_img"]["tmp_name"], "../upload/" . $_FILES["spon_img"]["name"]);
					//echo "Stored in: " . "../upload/" . $_FILES["file"]["name"];
					$update = "update sponser set spon_name='$spon_name',spon_img='".$_FILES["spon_img"]["name"]."',spon_url='$spon_url',isactive='$isactive' where id='$id'";
					$rsUpdate = mysql_query($update);
					if ($rsUpdate)
					{
					header("Location: sponser.php?msg=Updated Successfully.");
					exit;
					}
					else
					{
						$_REQUEST['msg'] = "Sorry, Not able to update. Try again.";
					}
				}
			  }
			}
		  else
			{
			echo "Invalid file";
			}
	}
	else
	{
	$update = "update sponser set spon_name='$spon_name',spon_img='".$_POST['spon_img1']."',spon_url='$spon_url',isactive='$isactive' where id='$id'";
	$rsUpdate = mysql_query($update);
	if ($rsUpdate)
	{
	header("Location: sponser.php?msg=Updated Successfully.");
	exit;
	}
	else
	{
		$_REQUEST['msg'] = "Sorry, Not able to update. Try again.";
	}
	
	}
	/* upload file end */




	
}


$id = $_GET['id'];

$qP = "SELECT * FROM sponser WHERE id = '$id'  ";
$rsP = mysql_query($qP);
$row = mysql_fetch_array($rsP);
extract($row);
$spon_name=trim($spon_name);
$spon_img=trim($spon_img);
$spon_url=trim($spon_url);

$isactive=trim($isactive);


?>
<?php 
if($act == "update")
{
?>

	<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Update Sponser For:  <?=$spon_name;?></h2>
				</div>		<!-- .block_head ends -->
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
						<label>Name</label><br />
						<input id="spon_name" name="spon_name" type="text"  value="<?php echo $spon_name; ?>" class="text small">
				</p>
                <p>
						<label>Image</label><br />
						<input type="file" name="spon_img" id="spon_img" />
                        <input type="hidden" name="spon_img1" id="spon_img1" value="<?php echo $spon_img;?>" />
				</p>
                <p>
						<label>URL</label><br />
						<input id="spon_url" name="spon_url" type="text"  value="<?php echo $spon_url; ?>" class="text small">
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
					
					<h2>Add new sponser</h2>
				</div>		<!-- .block_head ends -->
				
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
						<label>Sponser</label><br />
						<input id="spon_name" name="spon_name" type="text"  value="" class="text small">
				</p>
				<p>
						<label>Image</label><br />
						<input id="spon_img" name="spon_img" type="file"  value="" class="text small">
				</p>
				<p>
						<label>URL</label><br />
						<input id="spon_url" name="spon_url" type="text"  value="" class="text small">
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