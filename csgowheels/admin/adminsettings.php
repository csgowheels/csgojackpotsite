<?php
include("connect.php");
include("header.php");
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    if($_COOKIE['password']=='HeejMahoo!123.' || $_COOKIE['password']=='Dn12384dn12384!'){}
        else{
    header('Location: login.php');
	exit;
}
}
  
    
    else{
    header('Location: login.php');
	exit;
}
$pg = "admin";
$act = $_REQUEST['act'];


if($act == "up")
{
	$id = $_POST['id'];
	$name=$_POST["name"];
	$username=$_POST["username"];
	$password=$_POST["password"];
	$emailid=$_POST["emailid"];
	$isactive = $_POST['isactive'];

	$update = "update admin set name='$name',username='$username',password='$password',emailid='$emailid',isactive='$isactive' where id='$id'";
	$rsUpdate = mysql_query($update);
	if ($rsUpdate)
	{
	header("Location: index.php?msg=Updated Successfully.");
	exit;
	}
	else
	{
		$_REQUEST['msg'] = "Sorry, Not able to update. Try again.";
	}
}


$id = "1";

$qP = "SELECT * FROM admin WHERE id = '$id'  ";
$rsP = mysql_query($qP);
$row = mysql_fetch_array($rsP);
extract($row);
$name=trim($name);
$username=trim($username);
$password=trim($password);
$emailid = trim($emailid);
$isactive=trim($isactive);

?>

	<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Update Admin Settings</h2>
				</div>		<!-- .block_head ends -->
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form id="topic" action="" method="post" name="topic"  enctype="multipart/form-data">
				<p>
						<label>Name</label><br />
						<input id="name" name="name" type="text"  value="<?php echo $name; ?>" class="text small">
				</p>
                <p>
						<label>UserName</label><br />
						<input id="username" name="username" type="text"  value="<?php echo $username; ?>" class="text small">
				</p>
                <p>
						<label>Password</label><br />
						<input id="password" name="password" type="password"  value="<?php echo $password; ?>" class="text small">
				</p>
                <p>
						<label>Email ID</label><br />
						<input id="emailid" name="emailid" type="text"  value="<?php echo $emailid; ?>" class="text small">
				</p>
             
              <p>
													
                        <input type="hidden" name="act" value="up" />
				        <input type="hidden" name="isactive" value="1	" />
				   		<input type="submit" name="submit" value="Update" class="submit small">
                        <input type="hidden" name="id" value="<?=$id?>">&nbsp;
						<input type="button" name="cancel" value="Back" onclick="history.back()" class="submit small" />
							
				</p>
				</form>
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>



<?php include("footer.php");?>