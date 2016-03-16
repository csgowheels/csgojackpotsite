<?php
include("connect.php");

if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
	
} else {
    header('Location: login.php');
	exit;
}

/*if(!isset($_SESSION['uid']))
{
header("location: login.php");
exit;
}*/
$pg = "index";
$totaluser = gettotaluser();
$totalround = gettotalround();
$totalpotvalueindex = gettotalpotindex();
$totalcommission=gettotalcommission();
?>
<?php include("header.php"); ?>
<div class="block">
    <div class="block_head">
        <div class="bheadl"></div>
        <div class="bheadr"></div>
        <h2>Statistics</h2>
        
    </div>		<!-- .block_head ends -->
	<div class="block_content">
    	<table width="100%" "><tr style="border-bottom:0px !important;"><td width="30%">
    	 <div>
             <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h3>Total User</h3>
            </div>	
            <div class="block_content" style="border-bottom:1px solid #ccc;">
            <?php echo $totaluser;?>
            </div>
        </div></td><td width="30%">
         <div>
             <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h3>Total Round Played</h3>
            </div>	
            <div class="block_content" style="border-bottom:1px solid #ccc;">
            <?php echo $totalround;?>
            </div>
        </div></td>
        </tr><tr>
        <td width="30%">
         <div>
             <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h3>Total Pot Value</h3>
            </div>	
            <div class="block_content" style="border-bottom:1px solid #ccc;">
            $<?php echo round($totalpotvalueindex,2);?>
            </div>
        </div></td>
        <td width="30%">
         <div>
             <div class="block_head">
                <div class="bheadl"></div>
                <div class="bheadr"></div>
                <h3>Total Commission</h3>
            </div>	
            <div class="block_content" style="border-bottom:1px solid #ccc;">
            $<?php echo round($totalcommission,2);?>
            </div>
        </div></td>
        </tr></table>
    </div>
</div>    
<?php include("footer.php"); ?>
<?php 
function gettotaluser()
{
		$q_get_totaluser = mysql_query("select count(id) from user") or die(mysql_error());
		$r_get_totaluser = mysql_fetch_array($q_get_totaluser);
		return $r_get_totaluser["0"];
}
function gettotalround()
{
		$q_get_totalround = mysql_query("select count(roundid) from round where isactive='1' and isfinished='1'") or die(mysql_error());
		$r_get_totalround = mysql_fetch_array($q_get_totalround);
		return $r_get_totalround["0"];
}
function gettotalpotindex()
{
		$q_get_totalpotindex = mysql_query("select sum(tickettot)/100 from round_points where roundid > 1800") or die(mysql_error());
		$r_get_totalpotindex = mysql_fetch_array($q_get_totalpotindex);
		return $r_get_totalpotindex["0"];
}
function gettotalcommission()
{
		$q_get_totalpotindex = mysql_query("select (sum(tickettot)/100)*0.05 from round_points where roundid > 1800 ") or die(mysql_error());
		$r_get_totalpotindex = mysql_fetch_array($q_get_totalpotindex);
		return $r_get_totalpotindex["0"];
}

?>