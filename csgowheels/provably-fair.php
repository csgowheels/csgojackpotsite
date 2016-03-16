<?php
	include("header1.php");
	
	$hash = '';
	$tickettot = '';
	
	if (isset($_GET['roundid']))
	{
		$roundid = $_GET['roundid'];
		$roundid = htmlspecialchars($roundid);
		$roundid = mysql_real_escape_string($roundid);
		
		$query = "SELECT feed.itname, sum(tradeitems.tickettot) as tickettot FROM feed INNER JOIN tradeitems ON feed.roundId = tradeitems.roundid WHERE feed.roundId=$roundid AND feedtype=3";
		
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result))
		{
			$hash = $row['itname'];
			$tickettot = $row['tickettot'];
		}
	}
?>

 <style>
	.spacingtd
	{
		padding:10px !important;";
	}
</style>

<div class="row">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading">Provably Fair</div>
			
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
					We use provably fair system to ensure our players that we have an uncheatable system and the winner is truely randomly selected. There are three elements that will define the selected winner.
					<br><br>
					<strong>HASH</strong>
					<br><br>
					Is a MD5 encryption of the round salt with the winnings percentage accordingly to salt:winningPercentage. When the round has ended this information will be accessible in the feed section.
					<br>
					<br>
					<strong>SALT</strong>
					<br>
					<br>
					The salt is a random case-sensitive string. It is generated when the round starts and revealed when it ends.
					<br>
					<br>
					<strong>WINNING PERCENTAGE</strong>
					<br><br>
					A system where all winnings ticket are organisaid  in chronological order. First deposit recives first tickets and the last deposit has the last tickets. Each 1 ticket Is translated into $0.01 skin value. Example. Player deposit $40 he will recive 4000 ticket number.Formula to find the winning ticket round: (numberOfTickets * (winningPercentage / 100))where the round function returns the largest integer less than or equal to a given number.<br><br><br>
					                            <p id="mg" style="color:green"></p><p id="nmg" style="color:red"></p>
                </div>
			</div>
			
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;">
					<div id="divAnswer"></div>
					<script src="https://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>
					
					<table  cellspacing="10">
						<tr>
							<td class="spacingtd">Round Hash :</td>
							<td class="spacingtd"><input type="text" id="txtHash" size="70" value="<?php echo $hash; ?>" /></td>
						</tr>
						<tr>
							<td  class="spacingtd">Round Salt :</td>
							<td class="spacingtd"><input type="text" id="txtSalt" size="70" value="<?php if(isset($_GET['salt'])) { $salt = $_GET['salt']; $salt = htmlspecialchars($salt); $salt = mysql_real_escape_string($salt); echo $salt; } ?>"/></td>
						</tr>
						<tr>
							<td  class="spacingtd">Round Winner Percentage :</td>
							<td class="spacingtd"><input type="text" id="txtPercentage" size="70" value="<?php if (isset($_GET['percentage'])) {$percentage = $_GET['percentage']; $percentage = htmlspecialchars($percentage); $percentage = mysql_real_escape_string($percentage); echo $percentage; }?>" /></td>
						</tr>
						<tr>
							<td class="spacingtd">Round Total Tickets :</td>
							<td class="spacingtd"><input type="text" id="txtTotalTicket" size="70" value="<?php echo $tickettot; ?>" /></td>
						</tr>
						<tr>
							<td class="spacingtd"> </td>
						</tr>
						<tr>
							<td class="spacingtd"></td>
							<td class="spacingtd"><input type="button" value="Verify"  onclick="verifyRound()" /></td>
						</tr>
					</table>

					<script>
						function verifyRound()
						{
							var hash = CryptoJS.MD5( document.getElementById('txtSalt').value + '-' + document.getElementById('txtPercentage').value);
							if(hash == document.getElementById('txtHash').value)
							{
								var winTicket = Math.round(document.getElementById('txtTotalTicket').value * document.getElementById('txtPercentage').value / 100);
								document.getElementById('mg').innerHTML="The hash matches with the salt and the winning percentage!. \n\nWinning ticket was found at " +winTicket ;
							}
							else
								document.getElementById('nmg').innerHTML='The salt and the winning percentage does not matches with the hash.';
						}
					</script>
					
                </div>
			</div>
		</div>
	</div>
</div> 
    
<script>    
    if(typeof window.history.pushState == 'function') {
        window.history.pushState({}, "Hide", "http://127.0.0.1:8080/provably-fair");
    }
</script>
	
<?php
	include("footer.php");
?>