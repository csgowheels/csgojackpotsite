<?php    
	include "connect.php";
    
    include "simulate_steam_json.php";
    $url=$response;
	
    $player_inv=json_decode($url);
    //print_r($url);
    //print_r ($player_inv->rgInventory);
    $total_items=0;
    $item_prices=array();
    $item_id=array();
    $new_row=0;
    $total_items=0;

    foreach($player_inv->rgInventory as $obj)
    {
        $class_name = $obj->descriptionid;
        
        $total_items++;
        $statement="SELECT avg_price_7_days from skinprice where market_name='".$player_inv->rgDescriptions->$class_name->market_name."'";    
        $finish=mysql_query($statement);
        $result=mysql_fetch_assoc($finish);
        $item_prices[] =$result['avg_price_7_days']; 
    }
	
    rsort($item_prices);
    for($i=1;$i<=$total_items;$i++){

        echo "var total_price=0.00; 
            $('#items-".$i."').mouseover(function(){
                $('#items-".$i."').css('background-color', '#272B30');
            }); 
            $('#items-".$i."').mouseout(function(){
                $('#items-".$i."').css('background-color', '#2e3338');
            }); 
            $('#items-".$i."').click(function(){
                var success=0;
                var totItems = $('.mini-items').length+1;
                total_price=total_price + parseFloat(".$item_prices[$i-1].")*102;
                $('#items-".$i."').css('pointer-events', 'none');
                $('#value-item-".$i."').css('color', '#525151');
                document.getElementById('total-value').innerHTML = \"Total value: \"+total_price.toFixed(0)+\" P\";
                document.getElementById('total-items-selected').innerHTML = \"Total items selected:\"+totItems;
                var img_content=$('#item-img-".$i."').attr('src');
                var img_link='<img class=\"mini-items\" id=\"mini-items-img-".$i."\" src=\"'+img_content+' \" width=70 height=60 style=\"cursor:pointer\">';
                $('#deposit-items').append(img_link);
                $('#mini-items-img-".$i."').click(function(){
                    $('#mini-items-img-".$i."').remove();
                    totItems= $('.mini-items').length;
                    document.getElementById('total-items-selected').innerHTML = \"Total items selected:\"+totItems;
                    $('#value-item-".$i."').css('color', 'white');
                    total_price=total_price - parseFloat(".$item_prices[$i-1].")*102;
                    $('#items-".$i."').css('pointer-events', 'auto');
                    document.getElementById('total-value').innerHTML = \"Total value: \" +total_price.toFixed(0)+\" P\";
                });
            }); 
            ";
    }

    echo "
    var code=Math.floor((Math.random() * 89999) + 10000);
    var bot=Math.random() > 0.5 ? 1 : 0;
    var prevent_multiple=false;
    $('.nice-button').unbind().click(function(){
        if(prevent_multiple==true)
            return;
        prevent_multiple=true;
        $('#item-table').css('pointer-events', 'none');
        var counter=false;
        var steam_ids = [];
        var market_ids = [];
        var market_names = [];
        var codes = [];
        var instance_ids = [];
        var class_ids = [];
        var values = [];
        var bots = [];
        var isNews = [];

        $('.mini-items').each(function(){
            var id = $(this).attr('id');
            id=id.substring(15,18);

            var vid=\"market-id-\"+id;
            var did=\"market-name-\"+id;
            var price_id=\"value-item-\"+id;
            var classid = \"class-id-\"+id;
            var instance = \"instance-id-\"+id;
            var bot = \"bot-id-\"+id;
            
            
            
            var market_id = document.getElementById(vid).innerHTML;
            var instance_id = document.getElementById(instance).innerHTML;
            var bot_id = document.getElementById(bot).innerHTML;
            var class_id = document.getElementById(classid).innerHTML;
            var market_name = document.getElementById(did).innerHTML;
            var value_item = document.getElementById(price_id).innerHTML;
            value_item = value_item.substring(0, value_item.length - 1);

            steam_ids.push(\"$_POST[steamid]\");
            market_ids.push(market_id);
            market_names.push(market_name);
            codes.push(code);
            instance_ids.push(instance_id);
            class_ids.push(class_id);
            values.push(value_item);
            bots.push(bot_id);
            isNews.push(counter ? 0 : 1);

            if(counter==false)
                counter=true;
        });			" /* end of foreach */ . "


        " /* call deposit-check */ . "
        setTimeout(function () {
                        $.ajax({
                            url: \"deposit-check-shop\",
                            type: \"post\",
                            data:{
                                steamids: steam_ids,
                                market_id: market_ids,
                                market_name: market_names,
                                code: codes,
                                bot: bots,
                                instance: instance_ids,
                                class_id: class_ids,
                                value: values,
                                isNew: isNews
                            }, success: function(response) {
                                    console.log(response);
                                    document.getElementById('security-code').innerHTML = ' Security code : ' + code;
                                    $('#security-code').fadeIn();
                                    $('.nice-button').fadeOut();
                                    $('.progress').fadeIn();
                                    $('#deposit-items').fadeOut();
                                    //$('.nice-button').fadeOut();
                                    //$('#total-items-selected').fadeOut();    
                                    //$('#total-value').fadeOut();
                                    success=true;
                                }
                        }); }, 500);

        check_confirm(codes[0]);
        }); "; // end of javascript
     
?>
