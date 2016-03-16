<?php 
	include "header2.php";
	//this page is used for trade decline.
?>
<div class="row" style="margin:10px;">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $pagename;?></div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px; font-size:20px;">
				<?php echo $pgecontent;?>
                    <input type="button" class="myButton" value="I Agree" onclick="iagree();" />
                </div>
			</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
					<input type="button" class="myButton" value="I Agree" onclick="iagree();" />
				 </div>
			</div>
		</div>
	</div>
</div> 
 
    