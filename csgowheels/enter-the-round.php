<?php 
	include "header2.php";
	//this page is used for trade decline.
    $xx=mysql_fetch_assoc(mysql_query("select * from user where usteamid='".$_SESSION['steamid']."'"));
?>

<div style="font-family:Ebrima; font-size:20px; text-align:center;padding:5px;">
	Enter the amount you want to bet
</div>
<div style="font-family:Ebrima;text-align:center;">
	
        Your points: <span id='points'> <?php echo $xx['points']; ?></span>
    <br>
    <br>
    <input id="enter-points" type="text" placeholder="Type the amount here" >
    
    <br>
     <br>
    <p class="containers">
    <a href="#" class="button-bet"></a>
    </p>

</div>

<?php 
	include "footer.php";
?>


<script>
var prevent_multiple;
$(document).ready(function(){
    prevent_multiple=false;
    $(document).bind('keypress',pressed);
});

  function pressed(e)
{
    if(e.keyCode === 13)
    {
        if(prevent_multiple==true)
            return;
        prevent_multiple=true;
        $.ajax({
            url: "points-bet",
            type: "post",
            data:{

                steamid:<?php echo "'".$_SESSION['steamid']."'" ?>,
                points:document.getElementById("enter-points").value

            },
			success: function(response) {
                    console.log(response);
                    response=response.trim();
                    
                    if(response=="error")
                        alert("You don't have enough points");
                    else if(response=="Less5")
                        alert("You can't deposit less than 5 points");
                    else
                    {
                        document.getElementById("points").innerHTML=parseFloat(document.getElementById("points").innerHTML)-parseFloat(response);
                        parent.jQuery.fn.colorbox.close();
                    }
               
            }
                
			
		}); 
        setTimeout(function(){prevent_multiple=false;},1000);
    }
}
    
$('.button-bet').unbind().click(function(){
         if(prevent_multiple==true)
            return;
        prevent_multiple=true;                
         $.ajax({
			url: "points-bet",
			type: "post",
			data:{
                
                steamid:<?php echo "'".$_SESSION['steamid']."'" ?>,
                points:document.getElementById("enter-points").value
                
            },
			success: function(response) {
                    response=response.trim(); 
                    if(response=="error")
                        alert("You don't have enough points");
                    else if(response=="Less10")
                        alert("You can't deposit less than 10 points");
                    else
                    {
                        document.getElementById("points").innerHTML=parseFloat(document.getElementById("points").innerHTML)-parseFloat(response);
                        parent.jQuery.fn.colorbox.close();
                    }
                      
               
            }
                
			
		}); 
        setTimeout(function(){prevent_multiple=false;},1000);        
    
});


    if ((document.getElementById("enter-points").value).match(/[a-z]/i)) {
        
}
    
</script>
<style>



.containers {
  font: 12px;
  text-align: center;
  font-family: 'Righteous', cursive;
  
}

.button-bet:link, .button-bet:visited {
	position: relative;
  display: inline-block;
	width: 15em;
	height: 3em;
	border-top: 1px solid hsla(240, 9%, 6%,1);
	border-radius: 3px;
	background: hsla(240, 9%, 11%,1);
	border-radius: 2px;
	box-shadow: 0 1px 0 hsla(255,255%,255%,0.05), 0 0 1px hsla(255,255%,255%,0.1), inset 0 1px 2px hsla(0,0%,0%,0.2);
  -webkit-transition: all .1s ease;
  transition: all .1s ease;
	z-index: 1;
}
.button-bet:link:before, .button-bet:visited:before {
	position: absolute;
	top: 2px;
	right: 3px;
	bottom: 3px;
	left: 3px;
	padding: .5em 0 0;
	border-radius: 2px;
	background: -webkit-linear-gradient(top, hsla(240, 8%, 15%,1), hsla(240, 8%,9%,1));
	background: linear-gradient(to bottom, hsla(240, 8%, 15%,1), hsla(240, 8%,9%,1));
	box-shadow: inset 0 1px 0 hsla(255,255%,255%,0.05), inset 0 0 1px hsla(255,255%,255%,0.1), 0 4px 6px hsla(0,0%,0%,0.85), 0 1px 2px hsla(0,0%,0%,0.9);
	color: hsla(0, 0%, 87%,1);
	font-weight: normal;
	font-size: 117%;
	text-shadow: 0 -1px 1px hsla(0,0%,0%,0.5);
	text-decoration: none;
	text-transform: uppercase;
	text-align: center;
	letter-spacing: .08em;
	line-height: 1.2;
	content: "DEPOSIT";
	z-index: 0;
}
.button-bet:hover:before {
	background: hsla(26, 98%, 47%,1);
	background: -webkit-linear-gradient(top, hsla(240, 9%, 18%,1), hsla(240, 6%, 12%,1));
	background: linear-gradient(to bottom, hsla(240, 9%, 18%,1), hsla(240, 6%, 12%,1));
	color: hsla(0, 0%, 96%,1);
}
.button-bet:active:before {
	border-top: 1px solid hsla( 0, 0%, 1%,1);
	background: hsla(26, 98%, 47%,1);
	background: -webkit-linear-gradient(top, hsla(240, 6%, 7%,1), hsla(240, 8%, 7%,1));
	background: linear-gradient(to bottom, hsla(240, 6%, 7%,1), hsla(240, 8%, 7%,1));
	color: hsla( 0, 0%, 20%, 1);
	box-shadow: inset 0 1px 3px hsla(0,0%,0%,0.3), inset 0 0 2px hsla(0,0%,0%,0.6), 0 1px 0 hsla(0,0%,20%,1);
}

#enter-points {
    background-color: transparent;
    font-family:sans-serif;
    font-size:14px;
    outline:none;
    border:0;
    border-bottom:2px solid #aaa;
    -o-transition:box-shadow .5s ease;
    -moz-transition:box-shadow .5s ease;
    -webkit-transition:box-shadow .5s ease;
    -ms-transition:box-shadow .5s ease;
    transition:box-shadow .5s ease;
    box-shadow:0 0 0 0 #ddd inset;
    box-sizing:border-box;
    
    padding:5px;
}

#enter-points:focus{
  box-shadow:0 0 0 20px #eee inset;
  color:black;
}

::-webkit-input-placeholder {
  color: #aaa;
  opacity:1;
  font-weight:100;
  font-family:sans-serif;
}

:-moz-placeholder {
  color: #aaa;
  opacity:1;
  font-weight:100;
  font-family:sans-serif;
}

::-moz-placeholder {
  color: #aaa;
  opacity:1;
  font-weight:100;
  font-family:sans-serif;
}

:-ms-input-placeholder {
  color: #aaa;
  opacity:1;
  font-weight:100;
  font-family:sans-serif;
}





    
    

</style>