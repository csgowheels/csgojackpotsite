<?php
	/*
	 * One of pages (for new logged users)
	 */

	include("header2.php");
?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"> 
	<!-- Optional theme -->
	<link rel="stylesheet" href="dev_style/bootstrap.min.css">
    <script src="js/jquery.colorbox.js"></script>
	<link rel="stylesheet" href="css/colorbox.css" />
	<!-- Tweaks -->
	<link rel="stylesheet" href="dev_style/mrborking-custom.css">
	<style>
		.myButton {
			-moz-box-shadow:inset -1px -1px 2px -50px #ffffff;
			-webkit-box-shadow:inset -1px -1px 2px -50px #ffffff;
			box-shadow:inset -1px -1px 2px -50px #ffffff;
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f9f9f9), color-stop(1, #e9e9e9));
			background:-moz-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
			background:-webkit-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
			background:-o-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
			background:-ms-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
			background:linear-gradient(to bottom, #f9f9f9 5%, #e9e9e9 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f9f9f9', endColorstr='#e9e9e9',GradientType=0);
			background-color:#f9f9f9;
			-moz-border-radius:42px;
			-webkit-border-radius:42px;
			border-radius:42px;
			border:1px solid #dcdcdc;
			display:inline-block;
			cursor:pointer;
			color:#666666;
			font-family:Arial;
			font-size:15px;
			font-weight:bold;
			padding:7px 21px;
			text-decoration:none;
		}
		.myButton:hover {
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #e9e9e9), color-stop(1, #f9f9f9));
			background:-moz-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
			background:-webkit-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
			background:-o-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
			background:-ms-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
			background:linear-gradient(to bottom, #e9e9e9 5%, #f9f9f9 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e9e9e9', endColorstr='#f9f9f9',GradientType=0);
			background-color:#e9e9e9;
		}
		.myButton:active {
			position:relative;
			top:1px;
		}
	</style>
<?php
	//insert trade url
	if($_POST['urlbox'] != "" && $_POST['flagurladd']== "1")
	{
		$q_addurl_update = "update user set profiletradeurl = '".$_POST['urlbox']."' where usteamid='".$_SESSION['steamid']."'";
		if(mysql_query($q_addurl_update))
			$msg = "Trade url added Successfully";
		else
			$msg = "Please try again";
	}
?>
<div>
	<div class="col-md-3" style="width:100%;">
		
		<div class="panel panel-default">
			<div class="panel-heading">Trade URL</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
                    <?php if($msg != "") {
                        echo '<p style="color:green">'.$msg;'</p>';
	                    echo '<script>window.setTimeout(function() {
    	                        parent.jQuery.colorbox.close();;
        	                            }, 3000);  </script>';  
                    ?><br /><?php } ?>
                    <strong><span style="color:white">Trade URL :</span></strong> <a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank"> Where can I find my link?</a>

                    <?php 
					$st_add_url=getsteamurl();
					if($msg=="Trade url saved")//header('Location: http://www.csgowheels.com/index.php');
						if($st_add_url == "0")
							$st_add_url = "";
					?><br />
					
                    <form name="addurl" method="post">
                        <input type="text" name="urlbox" id="urlbox" value="<?php echo $st_add_url;?>" size="100px"><br /><br />
                        <input type="hidden" name="flagurladd" value="1">
                        <input type="submit" class="myButton" name="submit" value="Save">
                    </form>
                </div>
                    In order to receive winnings you need to set the correct trade URL also dont forget to set your profile and inventory to public.
			</div>
		</div>
	</div>
</div> 

<?php 
include "footer.php";
?>
