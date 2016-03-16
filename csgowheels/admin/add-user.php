<?php
include("connect.php");
include("header.php");



?>

	<div class="block">
			
				<div class="block_head">
					<div class="bheadl"></div>
					<div class="bheadr"></div>
					
					<h2>Add user to our Database</h2>
				</div>		<!-- .block_head ends -->
				<div class="block_content">	
				<?php if($_REQUEST['msg'] != "") { ?>
				<div class="message info"><p><?=$_REQUEST['msg'];?></p></div>
				<?php  } ?>
				<form name="contactform" method="post" action="user.php">
				<p>
						<label>Username</label><br />
						<input id="name" name="username" type="text"  value="" class="text small">
				</p>
                <p>
						<label>SteamID</label><br />
						<input id="name" name="steamid" type="text"  value="" class="text small">
				</p>
                <p>
						<label>TradeURL</label><br />
						<input id="name" name="tradeurl" type="text"  value="" class="text small">
				</p>
                    <p>
                    <label>ProfileURL</label><br />
						<input id="name" name="profileurl" type="text"  value="" class="text small">
				</p>
                
                    
                <p>
						<label>Avatar</label><br />
						<input id="emailid" name="avatar" type="text"  value="" class="text small">
				</p>
             
              <p>
													
                        <input type="hidden" name="act" value="up" />
				        <input type="hidden" name="isactive" value="1	" />
				   		<input type="submit" name="submit" value="add" class="submit small">
                        <input type="hidden" name="id" value="<?=$id?>">&nbsp;
						<input type="button" name="cancel" value="Back" onclick="history.back()" class="submit small" />
							
				</p>
				</form>
				</div>		<!-- .block_content ends -->
				
				<div class="bendl"></div>
				<div class="bendr"></div>
					
			</div>



<?php include("footer.php");?>