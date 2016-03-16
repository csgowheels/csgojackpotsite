<?php    
    //User is sent via POST METHOD(his steamid only)
    //Prices are sorted by the value(DESC)
    //This file outputs the items of  the user. Souvenirs, items that do not have price  in the database, and items lower than min value are not shown.

	include "connect.php";  //file with your connection with the database.
    
    $min_bet=0.001;             // items with the value 1 or higher are displayed only.

	$url=file_get_contents("http://steamcommunity.com/profiles/".$_POST['steamid']."/inventory/json/730/2/?trading=1");
	$player_inv=json_decode($url);
	
	$total_items=0;
	$item_prices=array();
	$item_id=array();
	$new_row=0;
	$total_items=0;
	$items=array();

    // function for price sorting.
    function price_sorting($a,$b)
    {
        if ($a->market_value > $b->market_value) {
        return -1;
    } else if ($a->market_value < $b->market_value) {
        return 1;
    } else {
        return 0; 
    }
        
    }

    //each item is stored in an object (class Item)
    class Item
    {
        
        public $market_name;
        public $class_id;
        public $instance_id;
        public $market_value;
        public $assetid;  
        public $state_obj;
    }

    
    //iteration from the response Steam gave us for the user inventory, and storing them in object
	foreach($player_inv->rgInventory as $obj)
	{
        //instantiating - class Item
        $item_class=new Item;
        
        $class_name=$obj->classid."_".$obj->instanceid;
        if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Souvenir') !== false) continue;
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Minimal Wear') !== false) $state="Minimal Wear";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Field-Tested') !== false) $state="Field-Tested";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Factory New') !== false) $state="Factory New";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Well-Worn') !== false) $state="Minimal Wear";
        else if(strpos($player_inv->rgDescriptions->$class_name->market_name, 'Battle-Scarred') !== false) $state="Battle-Scarred";
        else $state="";
        $statement="SELECT avg_price_7_days from skinprice where market_name='".$player_inv->rgDescriptions->$class_name->market_name."'";
        
        $finish=mysql_query($statement);
        $result=mysql_fetch_assoc($finish);
        if($result['avg_price_7_days']=="" ||  $result['avg_price_7_days'] == "0" || $result['avg_price_7_days'] <0.01)
        {
            
            continue;
        }
        $item_class->market_name=$player_inv->rgDescriptions->$class_name->market_name;
        $item_class->state_obj=$state;
         
        
        
        $valute='P';    
        $item_class->market_value=1000*$result['avg_price_7_days']; //multiplied by 1000, since result is in $, we need to multiply it with 1000
        
        $item_class->class_id=$class_name=$obj->classid;
        $item_class->instance_id=$class_name=$obj->instanceid;
        $item_class->assetid=$obj->id;
       
        $items[]=$item_class;
        
        
    }
        //function call for sorting
        usort($items, "price_sorting");
        
        
        
        foreach($items as $val){
        if($new_row==8){
            $new_row=0;
            echo '<tr>';
        }



            

        $total_items++;
        if($val->state_obj!="")
            
        echo "<td id='items-".$total_items."' style='color:#eeecec;padding:3px;cursor: pointer;' ><div data-toggle='tooltip' title='".$val->market_name. "' style='border-style: solid;border-color:#dbdbdb;border-width: 1px;border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);'><img id='item-img-".$total_items."' src='https://steamcommunity-a.akamaihd.net/economy/image/class/730/".$val->class_id."/150fx125f' width=90 height=60><p id='value-item-".$total_items."' style='font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;text-align:center;font-size: 16px;'>".$val->market_value." ".$valute."</p><p style='text-align:center;font-size:12px;margin-top:-10px;margin-bottom:2px;font-family: \"Arial\";'>".$val->state_obj."</p><p id='market-id-".$total_items."' style='display:none;'>". $val->assetid."</p><p id='market-name-".$total_items."' style='display:none;' '>".$val->market_name."</p><p id='instance-id-".$total_items."' style='display:none;'>".$val->instance_id."</p><p id='class-id-".$total_items."' style='display:none;'>" .$val->class_id."</p></div></td>";
        else
            echo "<td id='items-".$total_items."' style='color:#eeecec;padding:3px;cursor: pointer;' ><div data-toggle='tooltip' title='".$val->market_name."' style='border-style: solid;border-color:#dbdbdb;border-width: 1px;border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);box-shadow:inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(102,175,233,0.6);'><img id='item-img-".$total_items."' src='https://steamcommunity-a.akamaihd.net/economy/image/class/730/".$val->class_id."/150fx125f' width=90 height=60><p id='value-item-".$total_items."' style='font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;font-size: 16px;text-align:center;margin-bottom:30px;'>". $val->market_value." ".$valute."</p><p style='text-align:center;font-size:12px;margin-top:-10px;margin-bottom:2px;'>".$val->state_obj."</p><p id='market-id-".$total_items."' style='display:none;'>". $val->assetid."</p><p id='market-name-".$total_items."' style='display:none;'>".$val->market_name."</p><p id='instance-id-".$total_items."' style='display:none;'>".$val->instance_id."</p><p id='class-id-".$total_items."' style='display:none;'>" .$val->class_id."</p></div></td>";    

     
	       
          $new_row++;

	}
    
 

      
?>