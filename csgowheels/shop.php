<?php
	include("header1.php");
    //header('Location: https://csgowheels.com');
    
?>


<script>
	var total_price = 0;
    var total_points= 0;
    var timeout= 0;
    function check_points() {
    	$.ajax({
			url: "points_live.php",
			type: "post",
			success: function(response) {
                total_points = parseFloat(response);
                if(isNaN(total_points)) total_points=0;
                document.getElementById('PointContainer').innerHTML="Your points: " + total_points + " P";
				setTimeout(check_points, 500);
			},
			error: function(response) {
				setTimeout(check_points, 500);
			}}); 
    }
              
	function check_bet(){
       
        
		var numItems = $('.mini-items').length;
		if(total_price>total_points || numItems==0 || total_price<100){
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
        
        timeout=setTimeout(check_bet,100);
	}
	    
	check_bet();
	check_points();
	    
	function check_confirm(code){
        console.log(code);
	    var trade_url_gathered=false;
	    $.ajax({
			url: "deposit-check-shop",
			type: "post",
			data:{
                security_code: code,
                steamid:<?php echo "'".$_SESSION['steamid']."'" ?>,
                
            },
			success: function(response) {
                   
                if(response.length>3){
                    console.log(response);
                	trade_url_gathered=true;
                    
                    if(response.length<25){
                    
                    $('.progress-bar.active').css('background-color','orange');
                    document.getElementById("progressive-bar").innerHTML="Offers created. Check your Steam account.";
                    
                        
                        
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

<div class="row">
	<div class="col-md-3" style="width:100%;">
		<div class="panel panel-default">
			<div class="panel-heading">
                Inventory
			</div>
			<div style="max-height:100%; overflow-y:auto; overflow-x: visible;" class="chat-box panel-body">
				<div class="row" style="padding:10px;margin-bottom:200px;">
					<div class="spinner">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>

					<div class="bet-text" style="display:none;"><br><br><span id="PointContainer" ></span><br><div id="deposit-items"></div>Minimum withdraw is 1$<br><br>
						<span class="nice-button orange" style="cursor: pointer;display:none;">RETRIEVE <span><i class="icon-angle-right"></i></span></span><br><br><p id="total-value"></p><p id="security-code" style="display:none;"></p>
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
											url: "inventory-api2",
											type: "post",
											data:{
					                            steamid:'33'
					                        },
											success: function(response) {
							                            if(response.length!=4){
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
											url: "test-shop",
											type: "post",
											data:{
					                            steamid:'<?php echo $_SESSION['steamid'] ?>'
					                        },
											success: function(response) {
							                            if(response.length!=4 && response!='1' ){
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

<?php
    if(checkdbifuserhasbetted($_SESSION['steamid'])!="1") echo("<script>clearTimeout(timeout);$('.nice-button').css('pointer-events','none');</script>");
	include("footer.php");
?>
