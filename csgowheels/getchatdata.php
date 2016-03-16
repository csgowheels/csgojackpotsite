<?php
	include "connect.php";
	error_reporting(0);

	$last_chat_id = isset($_REQUEST['last_chat_id']) ? mysql_real_escape_string($_REQUEST['last_chat_id']) : "";
	
	include "chatfeed.php";
	
	$foo_arr = array('chatfeed' => $chatfeed, 'last_chat_id' => $new_chat_id);
	
	$response = json_encode($foo_arr);
	echo $response;
?>