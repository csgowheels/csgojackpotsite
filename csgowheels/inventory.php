<?php
	include("header1.php");
?>
<div class="row">
	<div class="col-md-3" style="width:100%;margin-bottom:200px;">
		<div class="panel panel-default">
			<div class="panel-heading">
                Inventory
			</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;margin-bottom: 200px;
}">
					<div class="spinner">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>

					<div class="bet-text" style="display:none;">Once you have selected items with the total value higher than 100Points you will be able to deposit.<br> Note that you may not bet more than 10 items per trade offer and that each item value should be higher than 100Points. Your items deposited translates to Points on our site.<br> 1$ in item value is equal to 100 Points on CSGOWHEELS<br> Good luck!<br><br><div id="deposit-items"></div><br><br>
						<span class="nice-button orange" style="cursor: pointer;display:none;">DEPOSIT <span><i class="icon-angle-right"></i></span></span><br><br><p id="total-value"></p><p id="security-code" style="display:none;"></p>
						<p id="total-items-selected"></p>
   						<div class="progress" style="width:320px;margin:auto;display:none;">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%;"><p id="progressive-bar"> Creating trade offer ...</p></div>
						</div>                     
					</div>
					
					<table id="item-table" style="margin-bottom:15px">

						<script>
						    $(document).ready(function(){
						        inventory_ajax();
								function inventory_ajax() {
							    	 $.ajax({
											url: "inventory-api",
											type: "post",
											data:{
					                            steamid:'<?php echo $_SESSION['steamid'] ?>'
					                        },
											success: function(response) {
                                                        //console.log(response);
							                            if(response.length!=4 ){
                                                            if(response==''){ 
                                                                $.colorbox({href:"error-inventory.php"});
                                                                $('.spinner').fadeOut();
                                                                setTimeout(function(){
                                                                document.getElementById('item-table').innerHTML="An error occured while trying to display your inventory";      
                                                                 },400);
                                                                return;
                                                                            
                                                                            }
															$('.spinner').fadeOut();
								                            setTimeout(function(){
									                            document.getElementById('item-table').innerHTML=response;
									                            $('.nice-button').fadeIn();
									                            $('.bet-text').fadeIn();
							       			                     },400);
							                	            setTimeout(function(){javascript_ajax();},500);          
							                            }
							                            else return inventory_ajax();
													}
											}); 
							    }  
							    
								function javascript_ajax() {        
							         $.ajax({
											url: "test",
											type: "post",
											data:{
					                            steamid:'<?php echo $_SESSION['steamid'] ?>'
					                        },
											success: function(response) {
							                            if(response.length!=4 && response!=''){
								                            g.text = response;
								                            s.parentNode.insertBefore(g, s);
							                            }
							                            else return javascript_ajax();
													}
											});
								}
							    
								var g = document.createElement('script');
								var s = document.getElementsByTagName('script')[0];
						    });
						</script>
					</table>
                </div>
			</div>
		</div>
	</div>
</div> 

<script>
    
    
	var total_price=0.00;    
	
	function check_bet(){
		var numItems = $('.mini-items').length;
		if(numItems>10 || numItems==0 || total_price<=1){
			$('.nice-button').css('pointer-events', 'none');
		} else
			$('.nice-button').css('pointer-events', 'auto');    
		if(numItems==0  ){
		    $('#total-items-selected').fadeOut();    
	    	$('#total-value').fadeOut();
		} else {
		    $('#total-items-selected').fadeIn();    
		    $('#total-value').fadeIn();
		}
        if(numItems>=10)
            $('#item-table').css('pointer-events', 'none');
        else if ($('.nice-button').css('display')=='none')
            $('#item-table').css('pointer-events', 'none');
        else
            $('#item-table').css('pointer-events', 'auto');
        
        setTimeout(check_bet,100);
	}    
	    
	check_bet();
	    
	function check_confirm(code){
        console.log(code);
	    var trade_url_gathered=false;
	    $.ajax({
			url: "deposit-check",
			type: "post",
			data:{
                security_code: code,
                steamid:<?php echo "'".$_SESSION['steamid']."'" ?>,
                
            },
			success: function(response) {
                 console.log(response);
                   
                if(response.length>3){
                    console.log(response);
                	trade_url_gathered=true;
                    
                    if(response.length<25){
                    $('.progress-bar.active').css('cursor','pointer');
                    $('.progress-bar.active').css('background-color','orange');
                    document.getElementById("progressive-bar").innerHTML="Click here to proceed to offer.";
                    $('.progress-bar.active').click(function(){
                        window.open("https://steamcommunity.com/tradeoffer/"+response, '_blank');
                        window.location.href="https://csgowheels.com";
                        
                    });
                        
                        
                    }
                    
                    else
                    {
                        alert(response);
                        location.reload(); 
                    }
				}
                
                
                if(trade_url_gathered==false)                      
                    setTimeout(check_confirm,3000,code);
			}
		}); 
	}
	//document.getElementByClass('progress')[0].innerHTML = "Accept trade offer to proceed.";   
</script>

<?php
	include("footer.php");
?>
