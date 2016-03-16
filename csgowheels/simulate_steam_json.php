<?php

	/*
	 * rgInventory: {
	 * 		descriptionid: string,
	 * 		classid: string,
	 * 		id: assetid (string)
	 * }
	 * 
	 * rgDescriptions: {
	 * 		class_name: {
	 * 			market_name: market_name
	 * 		}
	 * }
	 */
	 header("Content-type: text/html; charset=UTF-8");
	 include "connect.php";
	 
	 $query = "SELECT * FROM item_table";
	 $result = mysql_query($query);
	 
	 $rgInventory = "{";
	 $rgDescription = "{";
	 
	 while ($item = mysql_fetch_assoc($result)) {
		$rgInventory .= "\"" . $item["asset_id"] . "\":{";
		$rgInventory .= "\"descriptionid\":\"" . $item["description_id"];
		$rgInventory .= "\",\"classid\":\"" . $item["class_id"];
        $rgInventory .= "\",\"bot_id\":\"" . $item["owner_steam_id"]; 
		$rgInventory .= "\",\"id\":\"" . $item["asset_id"] . "\"},";
		
		$rgDescription .= "\"" . $item["description_id"] . "\":{";
		$rgDescription .= "\"market_name\":\"" . $item["it_name"];
		$rgDescription .= "\"},";
	 }
	 
	 $rgInventory = rtrim($rgInventory, ",") . "}";
	 $rgDescription = rtrim($rgDescription, ",") . "}";
	 
	 $response = "{\"rgInventory\":" . $rgInventory . ",\"rgDescriptions\":" . $rgDescription . "}";
     //echo $response;
?>