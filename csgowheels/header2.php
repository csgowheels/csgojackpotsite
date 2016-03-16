<?php 
	include("connect.php");
	include("generalfunction.php");
	
	$tid = mysql_real_escape_string($_GET['id']);
	if($tid == "")
		$tid = "2";

	$check_tos_fn = checktos();
	$qP = "select * from pages where id=".$tid;
	$rsP = mysql_query($qP);
	$row = mysql_fetch_array($rsP);
	extract($row);
	$pagename=trim($pagename);
	$pgetitle=trim($pagetitle);
	$pgecontent=trim($pagecontent);
?>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-48459897-5', 'auto');
	ga('send', 'pageview');
</script>

<style>
	.panel {
	background-color:#2e3338 !important;
	border-radius : 4px  !important;
	box-shadow:0 1px 1px rgba(0, 0, 0, 0.05)  !important;
	color:#cccccc !important;
	}
	.panel-heading {
	background-color:#3e444c !important;
	border-color:rgba(0, 0, 0, 0.6) !important;
	color:#c8c8c8 !important;
	}
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
<div class="panel" >