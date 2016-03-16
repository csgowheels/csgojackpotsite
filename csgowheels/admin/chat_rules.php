<?php 
include "header2.php";
$qP_rules = "select * from pages where id='8'";
$rsP_rules = mysql_query($qP_rules);
$row_rules = mysql_fetch_array($rsP_rules);
?>
		
		
		<div class="row" style="margin:10px;">
			<div class="col-md-3" style="width:100%;">
				
				<div class="panel panel-default">
					<div class="panel-heading"><?php echo $row_rules['pagename'];?></div>
					<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
						<div class="row" style="padding:10px;">
						<?php echo $row_rules['pagecontent'];?>
                        </div>
					</div>
				</div>
			</div>
		</div> 
 
    