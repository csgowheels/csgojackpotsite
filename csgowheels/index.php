<?php
	include("header.php");
?>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default" id="feed-devider" style="max-height:789px;">
			<div class="panel-heading"><span class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span> Feed</div>
			<div id="divFeedBox" style="max-height:710px; width:99%;margin-bottom:42px; overflow-y:auto; overflow-x: hidden; padding: 5px;" class="feed-box panel-body">
				<div id="divFeed"></div>
				<?php //include("feed.php");?>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		function resizeDiv() {
			//vpw = $(window).width();
			vph = $(document).height();
			// alert(vph);
			
            <?php
            if(!isset($_SESSION['steamid'])){
                echo "$('#divChatBox').css({'max-height': (vph-350) + 'px'});";
                echo "$('#skins-round').css({'margin-top': -47 + 'px'});";
                echo "$('#time').css({'margin-top': -22 + 'px'});";
                echo "$('#divFeedBox').css({'max-height': (vph-250) + 'px'})";
            }    
            else {
                echo "$('#divChatBox').css({'max-height': (vph-428) + 'px'});";
                echo "$('#skins-round').css({'margin-top': -47 + 'px'});";
                echo "$('#time').css({'margin-top': -10 + 'px'});";
                echo "$('#divFeedBox').css({'max-height': (vph-232) + 'px'})";
            }
            ?>    
			
		}
		//window.onload = function() {resizeDiv();}
	</script>
           
	<div class="col-md-6" style="padding-left:25px;">
		<div class="row">
			<div class="row">
			
                <div id="wheel" style="height:600px; overflow:hidden;">
	                <canvas id="canvas" width="600" height="550"></canvas>
                </div>
                
               
                
                <div style="text-align: center; width: 100%;" id="in-button">
                    <div style="width:300px; display:inline-block; text-align:center; z-index:-100;margin-bottom:5px;" >
	                    <?php 
	                	    $trurl = gettradeurl();
	                    	$ava_trurl = checktradeurl();
	                    ?>
	                    <?php if((isset($_SESSION['steamid'])) && $ava_trurl == "1") { ?>
	                    </br>
                    	<a href='#' id="enter-the-round"  class="btn btn-default btn-lg btn-block"  style="margin-bottom:10px">Enter the round</a>
	                    <?php } else if((isset($_SESSION['steamid'])) && $ava_trurl == "0") { ?>
	                    <a href="setting.php"  class="btn btn-default btn-lg btn-block">Add Steam URL</a>
	                    <?php } else { ?>
            				<br />
							<form method="post" action="?login">
								<input type="submit"  class="btn btn-default btn-lg btn-block" value="Login to deposit"  />
								<br />
							</form>	
	                    <?php } ?>
                    </div>
	                  <br />
	                <div id="time" style="margin-top:-12px;">1:00</div>
	                <div  class="progress" id="progress"></div>
                </div>
                 <div class="panel panel-warning" style="margin-top: 29px;margin-left: 302px; height: 104px;">
					<div class="panel-heading"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Largest jackpot in 24 hours</div>
					<div class="panel-body">
						<p id="text-center-2" style="font-size: 24px; margin-bottom: -4px;text-align:center;">0$</p>
					</div>
				    </div>
            	<div class="panel panel-default" style="width: 49%;margin-top: -123px;">
					<div class="panel-heading"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Spins in last 24 hours</div>
					<div class="panel-body">
						<p id="text-center" style="font-size: 24px; margin-bottom: -4px;text-align:center;">0</p>
					</div>
				</div>
                  <div class="panel panel-success" style="height:130px;">
					<div class="panel-heading" >
						<h3 class="panel-title"><span class="glyphicon glyphicon-star" aria-hidden="true"></span> Previous winner</h3>
					</div>
					<div class="panel-body" >
						<div class="row" style="padding-bottom: 2px;">
							<div class="col-md-3 vcenter" style="width:100%;">
								<img id="last-winner-img" src="images/Empty.png" class="img-circle" style="max-width: 64px;float:left;margin-left: 168px;" />
                                <p id="winner-name" style="padding-top: 20px; padding-left: 260px;">
									<strong> </strong>
								</p>
							</div>
						</div>
						
					</div>
				</div>
            
                <div style="height:50px;">&nbsp;</div>		
            	
				<script type="text/javascript">
					var wheelHandler = new WheelHandler(300, 300, 200, "canvas");
					isWheelSpinning = false;
					function spinTheWheel()
					{
						//alert(wheel.finished);
						if (isWheelSpinning == false)
						{
							isWheelSpinning = true;
							wheel.spin();
						}
					}
				</script>
                 <script>
        </script>
           
			</div>
		</div>
	</div>
	
	<div class="col-md-3" <?php echo'id="left-col-3"'; ?>> 
		<div  class="panel panel-default" style="width:100%">
            <div id="online-users" class="panel-heading"></div>
        </div>
        
		<div class="panel panel-default">
			<div class="panel-heading"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Chat <span style="float: right;"><a href='#' class="rules" style="color:red"><strong>RULES</strong></a></span>
			</div>
			<div id="divChatBox" style="max-height:550px;width:99%;  overflow-x: hidden;" class="chat-box panel-body">
				<div id="divchat"></div>
				<?php //include "chatfeed.php";?>
			</div>
			
            <?php
				if(isset($_SESSION['steamid']))
				{
			?>	
			
			<div class="form-group panel-body" style="margin: 5px; padding: 0;">
				<div class="form-group" style="padding: 2px; margin: 0;">
                    <input class="form-control input-sm" name="chattext" id="chattext" type="text" onkeypress="return ChatEnterEvent(event)" >
                    <input type="hidden" name="chatsteamid" value="<?php echo $_SESSION['steamid'];?>">
					<div style="padding-top: 2px;"><a href="javascript:;" class="btn btn-primary btn-xs" onClick="submitchat();" id="chatsendButton">Send</a>
                    </div>
				</div>
			</div>
			
			
            <script type="application/javascript">
				function ChatEnterEvent(e) {
					if (e.keyCode == 13)
						submitchat();
				}
				
				var lastchat1 = new Date();
				lastchat1.setSeconds(lastchat1.getSeconds() - 10);
				var lastchat2;
				var g = document.createElement('script');
	            var s = document.getElementsByTagName('script')[0];
				function enableChatObjects()
				{
					document.getElementById("chattext").disabled = false;
					document.getElementById("chatsendButton").disabled = false;
					isChatdisabled = false;
				}
				
				var isChatdisabled = false;		
				function submitchat()
				{
					var tmpChatValue = document.getElementById('chattext').value;
	                 document.getElementById('chattext').value = '';
					if (tmpChatValue =='')
						return;
						
					lastchat2 = lastchat1;
					lastchat1 = new Date();
					
					var seconds1 = (new Date().getTime() - lastchat1)/1000;
					var seconds2 = (new Date().getTime() - lastchat2)/1000;
					
					if(seconds1<10 && seconds2<10)
					{
						document.getElementById("chattext").disabled = true;
						document.getElementById("chatsendButton").disabled = true;
						isChatdisabled = true;
						setTimeout(enableChatObjects, (10-seconds2)*1000);
					}
					
					$.ajax({
						url: "insertchat",
						data: { 
							"chattext": tmpChatValue ,
							"chatsteamid" : "<?php echo $_SESSION['steamid'];?>"
						},
						cache: false,
						type: "POST",
						success: function(response) {
							response=response.trim();
                            
                            if(response=="Muted")
	                           document.getElementById('chattext').value = 'You are muted for bad behaviour';
                            else if(response=="CSGO")
                                document.getElementById('chattext').value = 'You need CS:GO on Steam account in order to chat.';
                            else
                                document.getElementById('chattext').value = '';
						},
						error: function(xhr) {
							$.colorbox.close();	
						}
					});
				}

				function iagree()
				{
					$.ajax({
						url: "iagree",
						data: { 
							"steamid_agree" : "<?php echo $_SESSION['steamid'];?>"
						},
						cache: false,
						type: "POST",
						success: function(response) {
							$.colorbox.close();
						},
						error: function(xhr) {
							$.colorbox.close();
						}
					});
				}
			</script>

            <?php
				}
			?>
		</div>
		<!--
		<div id="sponser" class="panel panel-default" style="width:100%">
			<div class="panel-heading"><span aria-hidden="true" class="glyphicon glyphicon-star"></span> Partners</div>
			<ul class="ppt">
				
			</ul>
		</div>
        -->
        <div id="steam-stats" class="panel panel-default" style="width:100%">
            <div class="panel-heading"><span aria-hidden="true" class="fa fa-steam fa-gold"></span> Steam Status:
                <span class="steam-running" style="color:green;display:none;">Steam is running fine.</span>
                <span class="emergency" style="display:none;">Steam is down.</span> 
                <span class="steam-delayed" style="display:none;color:orange;">Inventory Delayed</span>  
            </div>
		</div>
	</div>  
    
     <script type="application/javascript">
		var last_date_time = '0';
		var last_feed_id = '0';
		var last_chat_id = '0';
		var previous_last_chat_id = '1';
		var start_spin = '0';
		var player_count = ''
		var jackpot_price = '';
		var showWinner_popup = '0';
        var audio = new Audio('click.mp3');
        var audio_start = new Audio('gun_fire2.mp3');
		var firstTimePageLoad = true;
		var timer_started = "0";
		
		// cache values
		var 
			cache_feed = "",
			cache_feed_winner = "",
			cache_previous_winner = "",
			cache_previous_name = "",
			cache_points = null,
			cache_roundUsers = "",
			cache_userFlag = false,
			cache_roundFlag = false,
			cache_timeStamp = new Date().getTime(),
			cache_userDuration = 2000,
			cache_winner = '0',
			cache_win = '$0',
            start_timer = null,
            start_jackpot,
            new_round = false,
            line = 0,
            timer_loaded=0,
            lastNumberOfUsers = 0,
			cache_chat = [];
		
		var hrIndex, feedIndex;
		var offer_checked_show=false;
		var random = 0;
        
		function loadChat(lastTimeStamp)
		{
			var currentTimeStamp = Date.now();
			if (lastTimeStamp !== null && currentTimeStamp - lastTimeStamp < 200)
			{
               // console.log("setTimeout(loadChat, 500, currentTtamp)");
				setTimeout(loadChat, 400, lastTimeStamp);
				return;
			}
            
			else
			{
               // console.log(previous_last_chat_id);
                //console.log(last_chat_id);
				if (last_chat_id == previous_last_chat_id)
				{
                   last_chat_id++;
				}
				
				
				$.ajax({
					url: "getchatdata?last_chat_id="+last_chat_id,
					type: "get",
					data:{datetime:''},
					timeout: 7000,
					success: function(response) {
                        
						data = jQuery.parseJSON(response);
                        previous_last_chat_id = data.chatfeed == "" ? previous_last_chat_id : last_chat_id;
						last_chat_id = data.last_chat_id;
						
						if (!focus) {
							cache_chat.push(data.chatfeed);
                          //  console.log("setTimeout(loadChat, 1000, currentTimeStamp)");
                            setTimeout(loadChat, 400, currentTimeStamp);
							return;
						}
						
						var divChatHeight1 = document.getElementById('divChatBox').scrollHeight;
						if (cache_chat != "") {
							$('#divchat').append(cache_chat.join(""));
							cache_chat = [];
						}
						$('#divchat').append(data.chatfeed);
						var divChatHeight2 = document.getElementById('divChatBox').scrollHeight;
						
						if (divChatHeight2>divChatHeight1)
						{
							var topPos = $('#divChatBox').scrollTop();
							$('#divChatBox').animate({scrollTop:topPos + (divChatHeight2-divChatHeight1)});
						}
                        
                       // console.log("setTimeout(loadChat, 1000, currentTimeStamp)");
                        setTimeout(loadChat, 400, currentTimeStamp);
					},
                    error: function(response){
                        //console.log("failed in getchatfeed");
                        setTimeout(loadChat, 400, currentTimeStamp);
                        
                    }
				});
				
			}
		};
		
		function loadData()
		{
			$.ajax({
				url: "getdata?last_feed_id="+last_feed_id,
				type: "get", //send it through get method
				data:{datetime:''},
				timeout: 7000,
				success: function(response) {
					data = jQuery.parseJSON(response);
					//console.log(data.outputfeed);
					/// CACHE OPERATIONS
					// restarting user cache
					if (cache_userFlag && (new Date().getTime()) - cache_timeStamp > cache_userDuration) {
						cache_roundUsers = ""; cache_userFlag = false;
					}
                    
					// restarting round cache
					if (cache_roundFlag && data.start_spin!="1") {
						if (focus)
							$('#divFeed').prepend(cache_feed_winner);
						else
							cache_feed = cache_feed_winner + cache_feed;
							
						cache_feed_winner = "";
						cache_roundFlag = false;
						if (cache_winner == '1') {
							$.colorbox({href:"showwinner.php?potvalue=" + cache_win});
							cache_winner = '0';
							cache_win = '$0';
						}
						
						data.prev_winner_photo = cache_previous_winner;
						data.prev_winner_name = cache_previous_name;
						cache_previous_winner = "";
						cache_previous_name = "";
						
						data.total_points = cache_points;
						cache_points = null;
					}
						document.getElementById("online-users").innerHTML =" <span class='glyphicon glyphicon-user' class='panel-heading'></span> Users online: "+data.online_users;					
					// start point for caching feed (winner part)
					if (data.start_spin == "1") {
						feedIndex = (data.outputfeed).indexOf("Won");
						hrIndex = ((data.outputfeed).substring(feedIndex, (data.outputfeed).length)).indexOf("<br />");
						hrIndex += feedIndex + 6;
						if (feedIndex != -1) {
							cache_feed_winner = (data.outputfeed).substring(0, hrIndex);
							cache_roundFlag = true;
							data.outputfeed = (data.outputfeed).substring(hrIndex, (data.outputfeed).length);
						}
						
						cache_roundUsers = data.roundUsers;
						cache_previous_winner = data.prev_winner_photo;
						cache_previous_name = data.prev_winner_name;
						data.prev_winner_photo = "";
						data.prev_winner_name = "";
						
						cache_points = data.total_points;
						data.total_points = null;
					}
					
					// added 01.09. caching feed when tab is not on focus
					if (!focus) {
						if (data.outputfeed != '') {
							cache_feed = data.outputfeed + cache_feed;
							data.outputfeed = '';
						}
					}
	                  
					// ???
					(data.online_users);
					// cache roundUsers (so circle shows up when round ends)
					if (data.start_spin == '1' && cache_roundUsers != "")
						cache_roundUsers = data.roundUsers
	
					// set cache timer for user (wheel stopped spinning)
					if (cache_roundUsers != "" && data.roundUsers == null) {
						cache_timeStamp = new Date().getTime();
						cache_userFlag = true;
					}
	                  //alert(data.roundUsers.length);
					/// END OF CACHE Checks
					
					$('#divFeed').prepend(data.outputfeed);
					document.getElementById("text-center").innerHTML = data.total_rounds;
                    document.getElementById("text-center-2").innerHTML = "$"+parseInt(parseFloat(data.max_pot)*0.95);
                    if (data.prev_winner_name != null && data.prev_winner_name != "") {
	                    document.getElementById("last-winner-img").src = data.prev_winner_photo;
	                    document.getElementById("winner-name").innerHTML ="<strong>" + data.prev_winner_name + "</strong>";
	                }
	                if (data.total_points != null)
	                	document.getElementById('PointContainer').innerHTML="Your points: " + data.total_points + " P";
					/*
					if(data.inventory1 == "no skin added")
						document.getElementById("divinventory").innerHTML = document.getElementById('divInventoryHidden').innerHTML;
					else
						document.getElementById("divinventory").innerHTML = data.inventory1;	
					*/
					//alert(last_feed_id);
					wheelHandler.setTotalItems(data.pc);
					wheelHandler.setPrice(data.tmt);
					document.title = data.tmt + " - CSGO Wheels";
					//document.getElementById("divyouramt").innerHTML = data.youramt;
					//document.getElementById("divchance_to_win").innerHTML = data.chance_to_win;
					start_spin = data.start_spin;
					last_feed_id = data.last_feed_id;
					show_trade_decline = data.show_trade_decline;
					showWinner_popup = data.show_winner_popup;
					show_offer_popup = data.offer_check_show;
					
					if(show_trade_decline == "1" && data.reason_trade_decline!="Not checked or timeout")
						$.colorbox({href:"trade_decline_popup.php?reason="+data.reason_trade_decline});
	
	                if(show_offer_popup == "1" && new Date().getTime() - lastShowOfferPopup > 2000) {
	                  	$.colorbox({href:"offer_check_popup.php?price=" + data.offer_check_price + "&sum=" + data.offer_check_sum + "&offerid=" +data.offer_check_offerid});
	                  	lastShowOfferPopup = new Date().getTime();
					}
	                  	
	                if(showWinner_popup == "1") {
	                	cache_winner = '1';
	                	cache_win = data.tmt;
	                }
	                
	                if (firstTimePageLoad == true)
					{
						resizeDiv();
						document.getElementById('divChatBox').scrollTop = document.getElementById('divChatBox').scrollHeight;
						firstTimePageLoad = false;
					} 
					
	            	roundUsers = jQuery.parseJSON(data.roundUsers);
	            	wheelHandler.setRoundUsers(roundUsers);
	                 
                    if(timer_loaded==0){
                         line = new ProgressBar.Line('#progress', {
	                                    duration:0 ,
	                                    color: 'grey'
	                                });    
                                             // Number from 0.0 to 1.0
                        timer_loaded=1;
                        
                    }
                    
	                if (data.timer_start == "1" && timer_started == "0" && roundUsers["users"].length>1)
	                {
                            
                        	audio.play();
                        	timer_started = "1";
                            
                            //$('#progress').fadeOut();    
                           // $('#time').fadeOut();
                            //$('#progress').css("display","none");
                            $.ajax({url: "timer.php",
                                 success: function(data){
                                    if(data<=60 && data>=0){
                                        clearTimeout(start_jackpot);
                                        $( "#progress" ).empty();
                                        clearInterval(start_timer);
                                        // pozivanje timera(sekunde)
                                        var Minute = data,
                                            display = document.querySelector('#time');
                                        startTimer(Minute, display);
                                        var min_int=parseInt(Minute);
                                       // $("#progress").fadeIn();
                                        //$('#time').fadeIn();
                                            
                                            line = new ProgressBar.Line('#progress', {
                                            duration:(min_int)*1000 ,

                                            color: 'grey'
                                        });    

                                        line.animate(1.0);  // Number from 0.0 to 1.0
                                        start_jackpot = setTimeout(startJackpot, (min_int)*1000);
                                      //  $("#skins-round").attr("style", "margin-left:15px;margin-right:15px;margin-top:-45px;");
                                        }
                                    }
                            });
                        if (new_round === true)
                        {
                            clearInterval(start_timer);
                            start_timer = null;
                            new_round = false;
                        }
//                        if (focus)
                        	lastNumberOfUsers = roundUsers["users"].length;
	                }
	                
					if(start_spin == '1')
					{
                        audio_start.play();
                        clearTimeout(start_jackpot);
                        clearInterval(start_timer);
                        start_timer = null;
                        new_round = false;
                        $( "#progress" ).empty();
                        document.querySelector('#time').innerHTML =  "0:00";
                        random=0;
                        while (random < 0.1) random = Math.random();
                                             // Number from 0.0 to 1.0  
                        wheelHandler.setFinishAngle((parseFloat(data.min_percent) + (parseFloat(data.max_percent) - parseFloat(data.min_percent)) * random ) * 360);
                       
                        wheelHandler.spinWheel();
					}
					else
					{
						setTimeout(loadData, 1000);
					}
				},
				error: function(xhr) {
			  		$.colorbox.close();
			  		setTimeout(loadData, 1000);
					//Do Something to handle error
			  	}
			});
		}
	</script>
  
	<?php  
		// get page for rules
		$qP_rules = "select * from pages where id='8'";
		$rsP_rules = mysql_query($qP_rules);
		$row_rules = mysql_fetch_array($rsP_rules);
		extract($row_rules);
		//$pgetitle_rules=trim($pagetitle);
		$pgecontent_rules=trim($pagecontent);
	?>
	
	<div id="light" class="white_content" style="color:#ffffff">
	<div class="panel panel-primary" style="margin-bottom:0px !important;">
		<div class="panel-heading">
			<h3>Rules</h3>
		</div>
		<div class="panel-body">
			<?php echo $pgecontent_rules; ?>
			<br />
			<div align="right">
			<a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'" style="color:#ffffff" >Close</a></div>
		</div>
	</div>
	</div>

	<div id="fade" class="black_overlay"></div>
  
	<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>-->
	<script type="text/javascript">
		$('.ppt li:gt(0)').hide();
		$('.ppt li:last').addClass('last');
		var cur = $('.ppt li:first');
		
		function animate() {
			//cur.fadeOut( 1000 );
			cur.hide();
			if ( cur.attr('class') == 'last' )
				cur = $('.ppt li:first');
			else
				cur = cur.next();
			cur.fadeIn( 1000 );
		}
		
		function check_steam_status(){
			$.ajax({
				url: "steam-status",
				type: "post", 
				data:{},
				timeout: 7000,
				success: function(response) {
					if(response=="1"){
                        $('.steam-delayed').fadeOut();
			            $('.emergency').fadeOut();
			            $('.steam-running').fadeIn();
			            
			        }
			        else if(response=="2"){
                        $('.emergency').fadeOut();
			            $('.steam-running').fadeOut();
			            $('.steam-delayed').fadeIn();
			            
			        }
			        else {
                        $('.steam-delayed').fadeOut();
			            $('.steam-running').fadeOut();
			            $('.emergency').fadeIn(); 
			            
			        }
		       }
			});
		}
		
		function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            start_timer=setInterval(function () {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10);
		
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.innerHTML = minutes + ":" + seconds;

            if (--timer < 0) {
                display.innerHTML =  "0:00";
                clearInterval(start_timer);
            }
            }, 1000);
        }   
		
		function startJackpot()
		{
            
			$.ajax({
		    	url: "start-the-round.php",
				type: "get", 
				data:{timer:'timer'},
				timeout: 7000,
				success: function(response2) { 
                    timer_loaded=0;
                    timer_started = "0";
					//console.log(response2);
                    clearInterval(start_timer);
                    document.querySelector('#time').innerHTML =  "0:00";
					//$('#progress').fadeOut();    
					//$('#time').fadeOut();
                    //$('#progress').css("display","none");
                    $( "#progress" ).empty();
					//$("#skins-round").attr("style", "margin-left:15px;margin-right:15px;margin-top:-45px;");           
				}
			});
		  
		}
		$(function() {
			setInterval( "animate()", 5000 );
		});
		
		check_steam_status();    
		    
		setInterval("check_steam_status()",30000);    
	</script>
	
	<script>
		$('.rules').click( function() {
			$.colorbox({href:"rulespopup.php"});
		});
        
        $('#enter-the-round').click( function() {
			$.colorbox({href:"enter-the-round.php"});
		});
        
		  
		$("#unmute").click(function(){
	    	$(this).css('display', 'none');
	    	$("#mute").fadeIn();
		});
		 
		$("#mute").click(function(){
		    $(this).css('display', 'none');
		    $("#unmute").fadeIn();
		}); 
	</script>
	
	<?php
		include("footer.php");
	?>