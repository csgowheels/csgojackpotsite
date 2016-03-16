<?php
	include "header2.php";
	//this page is used for trade decline.
?>

<div class="panel-heading" style="color:green;">TRADE OFFER RECEIVED !</div>
<div class="feed-box panel-body"><?php echo $_GET['sum']?> items deposited. This trade offer value is <?php echo round($_GET['price'],2); ?>$ <br>
	By clicking on 'Accept' button you agree with our Terms of Service (<a href="http://csgowheels.com/page.php?id=6">ToS</a>) and declaring that you are at least 18 years old.<br><br>
    <button type="buttonz" class="accept">Accept</button>
    <button type="buttonz" class="decline">Decline</button>
</div>

<script type="text/javascript">
	$(".accept").click(function(){
        $.ajax({
            type: 'POST',
            url: 'tos_agree.php?agree=1&offerid=<?php echo $_GET['offerid']; ?>',
            success: function(data) {
                jQuery('#cboxClose').click();
			}
		});
	});

	$(".decline").click(function(){
        $.ajax({
            type: 'POST',
            url: 'tos_agree.php?agree=0&offerid=<?php echo $_GET['offerid']; ?>',
            success: function(data) {
                jQuery('#cboxClose').click();
            }
        });
	});
</script>

<?php 
	include "footer.php";
?>