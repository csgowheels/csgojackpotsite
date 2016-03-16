<?php
    //CHECK inventory-api.php for documentation. This module is for displaying items from bots inventory(withdraw)
    include "simulate_steam_json.php";
    $url=$response;
	//echo $url;
	$player_inv=json_decode($url);
   // var_dump($player_inv);
	//print_r($url);
	//print_r ($player_inv->rgInventory);
	$total_items=0;
	$item_prices=array();
	$item_id=array();
	$new_row=0;
	$total_items=0;
	$items=array();

    function price_sorting($a,$b)
    {
        if ($a->market_value > $b->market_value)
	        return -1;
    	else if ($a->market_value < $b->market_value)
	        return 1;
    	else
        	return 0;
    }

    class Item
    {
        public $market_name;
        public $class_id;
        public $instance_id;
        public $market_value;
        public $assetid;  
        public $state_obj;
        public $bot_id;
    }
    $ii=0;
	foreach($player_inv->rgInventory as $obj)
	{
        
        $item_class = new Item;
       
        
        
        $class_name = $obj->descriptionid;
        
        //if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Souvenir') !== false) continue;
        if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Minimal Wear') !== false) $state="Minimal Wear";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Field-Tested') !== false) $state="Field-Tested";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Factory New') !== false) $state="Factory New";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Well-Worn') !== false) $state="Minimal Wear";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Battle-Scarred') !== false) $state="Battle-Scarred";
        else $state="";
        $statement="SELECT avg_price_7_days from skinprice where market_name='".$player_inv->rgDescriptions->$class_name->market_name."'";
       // echo $statement;
        $finish = mysql_query($statement);
        $result = mysql_fetch_assoc($finish);
        if($result['avg_price_7_days'] == "" || $result['avg_price_7_days'] == "0" || $result['avg_price_7_days']<0.1 )
        {
            
            continue;
        }
        $item_class->market_name=$player_inv->rgDescriptions->$class_name->market_name;
        $item_class->state_obj=$state;

        
        $item_class->bot_id=$obj->bot_id;
     
        $item_class->market_value=round(102*$result['avg_price_7_days']); 
        $valute='P';    
       
        
        $item_class->class_id = $obj->classid;
        $item_class->instance_id = strstr($class_name, '_', false);
        $item_class->instance_id = substr($item_class->instance_id,1);
        $item_class->assetid=$obj->id;
       
        $items[]=$item_class;
        
    }
    //print_r($items);
    usort($items, "price_sorting");
    
    foreach($items as $val){
        if($new_row==8){
            $new_row=0;
            echo '<tr>';
        }

        $total_items++;
        if($val->state_obj!="")
        echo "<td id='items-".$total_items."' style='color:#eeecec;padding:3px;cursor: pointer;' ><div data-toggle='tooltip' title='".$val->market_name. "' style='border-style: solid;border-color:#dbdbdb;border-width: 1px;border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);'><img id='item-img-".$total_items."' src='https://steamcommunity-a.akamaihd.net/economy/image/class/730/".$val->class_id."/150fx125f' width=90 height=60><p id='value-item-".$total_items."' style='font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;text-align:center;font-size: 16px;'>".$val->market_value." ".$valute."</p><p style='text-align:center;font-size:12px;margin-top:-10px;margin-bottom:2px;font-family: \"Arial\";'>".$val->state_obj."</p><p id='market-id-".$total_items."' style='display:none;'>". $val->assetid."</p><p id='market-name-".$total_items."' style='display:none;' '>".$val->market_name."</p><p id='instance-id-".$total_items."' style='display:none;'>".$val->instance_id."</p><p id='class-id-".$total_items."' style='display:none;'>" .$val->class_id."</p><p id='bot-id-".$total_items."' style='display:none;'>".$val->bot_id."</p></div></td>";
        else
            echo "<td id='items-".$total_items."' style='color:#eeecec;padding:3px;cursor: pointer;' ><div data-toggle='tooltip' title='".$val->market_name."' style='border-style: solid;border-color:#dbdbdb;border-width: 1px;border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);'><img id='item-img-".$total_items."' src='https://steamcommunity-a.akamaihd.net/economy/image/class/730/".$val->class_id."/150fx125f' width=90 height=60><p id='value-item-".$total_items."' style='font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;font-size: 16px;text-align:center;margin-bottom:30px;'>". $val->market_value." ".$valute."</p><p style='text-align:center;font-size:12px;margin-top:-10px;margin-bottom:2px;'>".$val->state_obj."</p><p id='market-id-".$total_items."' style='display:none;'>". $val->assetid."</p><p id='market-name-".$total_items."' style='display:none;'>".$val->market_name."</p><p id='instance-id-".$total_items."' style='display:none;'>".$val->instance_id."</p><p id='class-id-".$total_items."' style='display:none;'>" .$val->class_id."</p><p id='bot-id-".$total_items."' style='display:none;'>".$val->bot_id."</p></div></td>";    
        $new_row++;
	}

?>