// Helpers
shuffle = function(o) {
	for ( var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
	return o;
};

String.prototype.hashCode = function(){
	// See http://www.cse.yorku.ca/~oz/hash.html		
	var hash = 5381;
	for (i = 0; i < this.length; i++) {
		char = this.charCodeAt(i);
		hash = ((hash<<5)+hash) + char;
		hash = hash & hash; // Convert to 32bit integer
	}
	return hash;
};

Number.prototype.mod = function(n) {
	return ((this%n)+n)%n;
};

var roundUsers;
var segments_count;
var roundUsers_blank = {"users": [
          {
          "name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          "name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          "name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
    
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	 
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	 
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	 
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
           {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
           {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	 
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          },
          {
          	"name": "",
           	"thumb": "", 
           	"percentage": "0.0625"
          }
    ]
};
    
var roundInfo = {"info": 
{
	"items": "10",			// deprecated..
	"value": "2432.00",		// deprecated.. // always should be rounded on two zeros
	"sound": "on"
    }
};

var winner1 = Math.PI * 2 * 0.500;
var winner2 = Math.PI * 2 * 0.505;
var sideVar = false;			// used for highlighting segments
var timer, sideTimer;
var lastShowOfferPopup = 0;
var focus = true;
	
	
// WHEEL!
var wheel = {

	timerHandle : 0,
	timerDelay : 33,

	angleCurrent : 0,
	angleDelta : 0,

	size : 210,

	canvasContext : null,

	colors : [ '#2e3338', '#495058', '#5f6872', '#77828e', '#8b99a7', '#9faebe',
			   '#1a313f', '#6e808c', '#b6c0a8', '#637370', '#1a313f', '#6e808c',
			   '#1a313f', '#6e808c', '#b6c0a8', '#637370', '#1a313f', '#6e808c',
			   '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',		// resreve
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000',
	           '#ffffff', '#990000', '#ffffff', '#990000', '#ffffff', '#990000'

	            ],

	//segments : [ 'Andrew', 'Bob', 'Fred', 'John', 'China', 'Steve', 'Jim', 'Sally', 'Andrew', 'Bob', 'Fred', 'John', 'China', 'Steve', 'Jim'],
	segments : [],

	seg_colors : [], // Cache of segments to colors
	
	maxSpeed : Math.PI / 16,

	upTime : 3000, // How long to spin up for (in ms)
	downTime : 15000, // How long to slow down for (in ms)

	spinStart : 0,
	finished : true,
    stopStart : 0,
    stopFlag  : false,
    w0: 0,
	frames : 0,
    side  : 0,
    last : 0,
    last2 : 0,
	centerX : 300,
	centerY : 300,
    
	maxProgres : 0.7,
	timeForSlow : 15000,

	spin : function() {
		// Start the wheel only if it's not already spinning
		if (wheel.timerHandle == 0)
			setTimeout(wheel.startSpinning, 4000);
	},
	 
	startSpinning : function() {
		wheel.spinStart = new Date().getTime();
		wheel.maxSpeed = Math.PI / 16;//(16 + Math.random()); // Randomly vary how hard the spin is
		wheel.frames = 0;
		if ( roundInfo.info.sound != "off" )
			wheel.sound.play();
			wheel.timerHandle = setInterval(wheel.onTimerTick, wheel.timerDelay);
		},
		onTimerTick : function() {
		wheel.draw();
		wheel.rotate();
	},
			
	rotate : function() {
		wheel.frames++;
		
		var duration = (new Date().getTime() - wheel.spinStart);
		var progress = 0;
        if (duration>=25000) {
            wheel.finished=true;
			// stop music if necessary
			if ( roundInfo.info.sound == "on" ) {
				wheel.sound.pause();
				wheel.sound.currentTime = 0;
			}
        }
        else
        	wheel.finished = false;
		
		if (duration < wheel.upTime) {
			progress = duration / wheel.upTime;
			wheel.angleDelta = wheel.maxSpeed *
							Math.sin(progress * Math.PI / 2);
		} else {
			progress = duration / wheel.downTime > wheel.maxProgres ? wheel.maxProgres : duration / wheel.downTime;
			wheel.angleDelta = wheel.maxSpeed *
							Math.sin(progress * Math.PI / 2 + Math.PI / 2);
			   
			wheel.last2 = wheel.last;
			wheel.last = new Date().getTime();
			
			if (duration > wheel.timeForSlow && wheel.maxProgres < 0.9) {
				wheel.maxProgres += 0.05;
				wheel.timeForSlow += 10000;
			}
			   
			if (wheel.stopFlag) {
				// if slowing down just started, start counting time
				if (wheel.stopStart == 0) {
					wheel.stopStart = new Date().getTime();
					wheel.w0 = wheel.angleDelta / ((new Date().getTime() - wheel.last2) / 1000);
				}
				
				var deltaT = new Date().getTime() - wheel.stopStart;
				var deltaT2 = new Date().getTime() - wheel.last2;
			
				if (wheel.side < 2 * Math.PI && deltaT2 > 0.1 && deltaT > 0.1) {
					deltaT2 /= 1000;
					wheel.angleDelta = (wheel.w0 - (deltaT / 1000 - deltaT2 / 2) * ((wheel.w0 * wheel.w0) / (Math.PI * 4.01))) * deltaT2;
				} else
					wheel.angleDelta = 0;
				
				wheel.side += wheel.angleDelta;
				
				// if all overall angle goes over 2PI then put it down to 2PI
				if (wheel.side > Math.PI * 2) {
					wheel.angleDelta -= Math.PI * 2 - wheel.side;
					wheel.side = Math.PI * 2;
				}
			}
		}
		
		// adding new angle to current angle before check for end
		wheel.angleCurrent += wheel.angleDelta;
		// Keep the angle in a reasonable range
		while (wheel.angleCurrent >= Math.PI * 2)
			wheel.angleCurrent -= Math.PI * 2;
		
		var tmpCurrentAngle = Math.PI * 2 - wheel.angleCurrent;
		var partTime = new Date().getTime() - wheel.stopStart;
		if(duration > 8000) {
			if(winner1 < tmpCurrentAngle && tmpCurrentAngle < winner2) {
				//console.log('in here');
				if (wheel.stopFlag && wheel.side == Math.PI * 2) {
					console.log(wheel.side);
					console.log(wheel.angleDelta);
					
					wheel.finished = true;
					
					// stop music if necessary
					if ( roundInfo.info.sound == "on" ) {
						wheel.sound.pause();
						wheel.sound.currentTime = 0;
					}
					
					// reset stop flags
					wheel.stopFlag = false;
					wheel.side = 0;
					wheel.stopStart = 0;
					
					// reset slow safety mechanisms
					wheel.timeForSlow = 12000;
					wheel.maxProgres = 0.7;
					
				} else if (!wheel.stopFlag) {
					wheel.stopFlag = true;
					wheel.finished = false;
					wheel.side = 0;  
				}
			}
		}
				
		if (wheel.finished) {
			//console.log('finished');    
			setTimeout(changeWheelSpinning, 4000);
			clearInterval(wheel.timerHandle);
			wheel.timerHandle = 0;
			wheel.angleDelta = 0;
		}
		
		/*
		// Display RPM
		var rpm = (wheel.angleDelta  (1000 / wheel.timerDelay)  60) / (Math.PI * 2);
		$("#counter").html( Math.round(rpm) + " RPM" );
		*/
	},

	init : function(optionList) {
		try {
			wheel.initWheel();
			wheel.initAudio();
			wheel.initCanvas();
			wheel.draw();
            
			$.extend(wheel, optionList);

		} catch (exceptionData) {
			alert('Wheel is not loaded ' + exceptionData);
		}

	},

	initAudio : function() {
		var sound = document.createElement('audio');
		sound.setAttribute('src', 'csgowheels2.mp3');
		wheel.sound = sound;
	},

	initCanvas : function() {
		var canvas = $('#wheel #canvas').get(0);

		if ($.browser.msie) {
			canvas = document.createElement('canvas');
			$(canvas).attr('width', 1000).attr('height', 600).attr('id', 'canvas').appendTo('.wheel');
			canvas = G_vmlCanvasManager.initElement(canvas);
		}

		//canvas.addEventListener("click", wheel.spin, false);
		wheel.canvasContext = canvas.getContext("2d");
        
	},

	initWheel : function() {
		//shuffle(wheel.colors);
	},

	// Called when segments have changed
	update : function() {
		// Ensure we start mid way on a item
		//var r = Math.floor(Math.random() * wheel.segments.length);
		var r = 0;
		//wheel.angleCurrent = ((r + 0.5) / wheel.segments.length) * Math.PI * 2;
		wheel.angleCurrent = 0;
        
		var segments = wheel.segments;
		var len      = segments.length;
		var colors   = wheel.colors;
		var colorLen = colors.length;

		// Generate a color cache (so we have consistant coloring)
		var seg_color = new Array();
		for (var i = 0; i < len; i++)
			seg_color.push( colors [ i ] ); //segments[i].hashCode().mod(colorLen)

		wheel.seg_color = seg_color;

		wheel.draw();
	},

	draw : function() {
		if (isWheelData)
			wheel.clear();
		wheel.drawWheel();
        
		wheel.drawNeedle();
	},

	clear : function() {
		var ctx = wheel.canvasContext;
		ctx.clearRect(0, 0, 1000, 800);
	},

	drawNeedle : function() {
		var ctx = wheel.canvasContext;
		var centerX = wheel.centerX;
		var centerY = wheel.centerY;
		var size = wheel.size;

		// variables used for needle draw "<(" shape			
		var addConstantX 	= 30,
			addConstantY 	= 10,
			r 				= 14,
			angle 			= Math.asin(addConstantY / r),
			centerPosX 		= centerX + size + addConstantX + r * Math.cos(angle),
			centerPosY 		= centerY;


		ctx.lineWidth = 3;
		ctx.strokeStyle = '#000000';
		var g = ctx.createRadialGradient(centerX + size - addConstantX, centerY, addConstantY,
										 centerX + size + addConstantX * 1.2, centerY, addConstantY);
		g.addColorStop(1, "#333333");
		g.addColorStop(0, "#7a8088");
		ctx.fillStyle = g;

		ctx.beginPath();
		ctx.moveTo(centerX + size - addConstantX, centerY);
		ctx.lineTo(centerX + size + addConstantX, centerY + addConstantY);			
		ctx.arc(centerPosX, centerPosY, r, Math.PI - angle, Math.PI + angle);
		ctx.lineTo(centerX + size - addConstantX, centerY);
		ctx.closePath();

		ctx.stroke();
		ctx.fill();
	},

	drawSegment : function(key, lastAngle, angle, current, img_draw) {
		var ctx = wheel.canvasContext;
		var centerX = wheel.centerX;
		var centerY = wheel.centerY;
		var size = wheel.size;
		var segments = wheel.segments;
		var len = wheel.segments.length;
		var colors = wheel.seg_color;
        var flag = wheel.segments.length % 4 == 1 && wheel.segments.length != 1 ? true : false;
		var value = segments[key];
		
		// highlighting segment if it was current
		/*if ( current && isWheelSpinning){
			ctx.lineWidth = 3;
			ctx.strokeStyle = "#05fe17";
		}
		*/
        if(roundUsers.users[key].thumb!=""){
			ctx.save();
			ctx.beginPath();
	        
			// Start in the centre
			ctx.moveTo(centerX, centerY);
			ctx.arc(centerX, centerY, size - 5, lastAngle, angle, false); // Draw a arc around the edge
			ctx.lineTo(centerX, centerY); // Now draw a line back to the centre
	
			// Clip anything that follows to this area
			//ctx.clip(); // It would be best to clip, but we can double performance without it
			ctx.closePath();
	        
			ctx.fillStyle = flag && (key == 0 || key == 1) ? (key == 0 ? colors[1] : colors[0]) : colors[key];
	           // if ( current )					// highlighting by coloring whole segment
				// ctx.fillStyle = "rgba(1, 0, 0, 0.1)";
	             
				
			// if ( current )					// highlighting by coloring whole segment every second time
				// if ( sideVar ) {
					// ctx.fillStyle = '#3399FF';
					// sideVar = false;
				// } else sideVar = true;
			ctx.fill();
	        if(current && isWheelSpinning)
				ctx.stroke();
	
			// Now draw the text
			ctx.save(); // The save ensures this works on Android devices
			ctx.translate(centerX, centerY);
			ctx.rotate((lastAngle + angle) / 2);
			
			
	        if ( current && isWheelSpinning)
	            ctx.font = "17px Calibri";
	        else    
	            ctx.font = "17px Calibri";
            ctx.fillStyle = 'white';
	        // check percentage 
	        if(roundUsers.users[0].percentage==0.0625 && roundUsers.users[1].percentage==0.0625)segments_count=1;
			
	        if (roundUsers.users[key].percentage * 100 >= 2.5) {
	        	// if segment is in left half of circle
	        	if ((lastAngle + angle) / 2 > Math.PI / 2 && (lastAngle + angle) / 2 < 3 * Math.PI / 2) {
		        	// rotate percentage writing, so that left side has letters normall, and right side upside down
		        	ctx.rotate(Math.PI);
		        	ctx.fillText( parseFloat(100* roundUsers.users[key].percentage).toFixed(2) + "%", - size / 2 - 5, 0); ///TODO check if is ok to short name to 10 chars
		        	// rotate canvas back (for other writing and drawing..)
		        	ctx.rotate(-Math.PI);
	        	} else {
	        		ctx.fillText( parseFloat(100* roundUsers.users[key].percentage).toFixed(2) + "%", size / 2 + 5, 0); 
	        	}
	        }
	        
			// image draw
			// ctx.globalCompositeOrientation = "source-over";
			var img1 = new Image();
			img1.src = roundUsers.users[key].thumb == "" ? null : roundUsers.users[key].thumb; //"https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/c3/c3f8318408d72b0907f47a2299559e706f334179.jpg";
			   
			ctx.beginPath();
			ctx.arc(size / 2 + 76, 0, 16, 0, 2 * Math.PI, true);
			ctx.closePath();
			ctx.clip();

			// added for image rotation
			// context positioning for image rotation
			ctx.translate(size / 2 + 76, 0);
			ctx.rotate(- (lastAngle + angle) / 2);
			
			ctx.drawImage(img1, -16, -16, 32, 32);
			ctx.save();
			ctx.restore();
			
			// context restoring for other drawings
			ctx.rotate((lastAngle + angle)/2);
			ctx.translate(-size / 2 - 76, 0);

			ctx.restore();
        }
        else {
           if(img_draw==0){
               if(roundUsers.users[0].thumb ==""){
        	   var img2=new Image();
        	   //alert(roundUsers.users[0].thumb);
        	   img2.src = "images/wheel3.png";
        	   ctx.drawImage(img2,(canvas.height / 2 - img2.width / 2 ) + 25,canvas.height / 2 - img2.height / 2+25);
                }   
           } 
       	}
        ctx.restore();
		// restoring non-highlighted settings
	 /* if ( current && isWheelSpinning) {
			ctx.strokeStyle = '#000000';
			ctx.lineWidth = 1;
		} */  
	},
	
	drawCenterCircle : function(imgToDraw) {
		var ctx = wheel.canvasContext;
		
		var centerX = wheel.centerX;
		var centerY = wheel.centerY;
		var size    = wheel.size;
		
		var PI2 = Math.PI * 2;
		
		if (imgToDraw == null)
		{
			// Draw a center circle
			// draw outer bottom circle 
			ctx.beginPath();
			ctx.lineWidth   = 4;
			ctx.arc(centerX, centerY, 55,  0, Math.PI, false);
			ctx.closePath();
			ctx.fillStyle   = '#510000';
			ctx.strokeStyle = '#ffffff';
			ctx.fill();
			ctx.stroke();
			
			// draw mid line
			ctx.beginPath();
			ctx.moveTo(centerX - 55, centerY - ctx.lineWidth / 2);
			ctx.lineTo(centerX + 55, centerY - ctx.lineWidth / 2);
			ctx.lineWidth = 2;
			ctx.stroke();
			ctx.lineWidth = 4;
			ctx.closePath();
			   
			// draw outer top circle
			ctx.beginPath();
			ctx.fillStyle   = '#900407';
			ctx.arc(centerX, centerY, 55, Math.PI, PI2, false);
			ctx.fill();
			ctx.stroke();
			ctx.closePath();
			   
			// inner bottom circle
			ctx.beginPath();
			ctx.strokeStyle   = '#1a313f';
			ctx.arc(centerX, centerY, 52, 0, Math.PI, false);
			// ctx.fill();
			ctx.stroke();
			ctx.closePath();
			   
			// inner top circle
			ctx.beginPath();
			ctx.strokeStyle   = '#1a313f';
			ctx.arc(centerX, centerY, 52, Math.PI, PI2, false);
			// ctx.fill();
			ctx.stroke();
			ctx.closePath();
	   
			ctx.font = "bold 17px Ebrima";
			ctx.fillStyle = "white";
			
			ctx.fillText(player_count, (canvas.height / 1.83), (canvas.height / 2) +2);
			ctx.fillText(jackpot_price, (canvas.height / 1.85), (canvas.height / 2) +45);
		} else {
			ctx.beginPath();
			ctx.lineWidth   = 4;
			ctx.arc(centerX, centerY, 55,  0, Math.PI, false);
			ctx.closePath();
			ctx.fillStyle   = '#510000';
			ctx.strokeStyle = '#ffffff';
			ctx.fill();
			ctx.stroke();
			
			ctx.beginPath();
			ctx.fillStyle   = '#900407';
			ctx.arc(centerX, centerY, 55, Math.PI, PI2, false);
			ctx.fill();
			ctx.stroke();
			ctx.closePath();
			
			// for clip()
			ctx.save();
			
			ctx.beginPath();
			ctx.arc(centerX, centerY, 32, 0, 2 * Math.PI, true);
			ctx.closePath();
			ctx.lineWidth = 6;
			ctx.stroke();
			ctx.clip();
			
			var img = new Image();
			img.src = imgToDraw;
			ctx.drawImage(img, centerX - 32, centerY - 32, 64, 64);
			
			// for clip()
			ctx.restore();
		}
	},


	drawWheel : function() {
		var ctx = wheel.canvasContext;
        var img_draw=0;
		var angleCurrent = wheel.angleCurrent;
		var lastAngle    = angleCurrent;

		var segments  = wheel.segments;
		var len       = wheel.segments.length;
		var colors    = wheel.colors;
		var colorsLen = wheel.colors.length;

		var centerX = wheel.centerX;
		var centerY = wheel.centerY;
		var size    = wheel.size;

		var PI2 = Math.PI * 2;
		
		// Shared information
		ctx.textBaseline = "middle";
		ctx.textAlign    = "center";
		ctx.font         = "1.4em Arial";
		
		// Draw outer(bigger) circle
		ctx.beginPath();
		ctx.arc(centerX, centerY, size, 0, PI2, false);
		ctx.closePath();
		
		ctx.lineWidth   = 6;
		ctx.strokeStyle = '#3e444c';
		ctx.stroke();

        // Draw outer circle
		ctx.beginPath();
		ctx.arc(300, 300, 212, 0, PI2, false);
		ctx.closePath();
		
		ctx.lineWidth   = 1;
		ctx.strokeStyle = '#757f89';
		ctx.stroke();
        
		// Draw segments
		// Finding current segment so that it could be highlighted
		var tmpAngle = 0.0;
		var tmpCurrentAngle = Math.PI * 2 - wheel.angleCurrent;
		var currentSegment = 0;
		
		for(loop=0; loop<wheel.segments.length; loop++)
		{
			if (tmpAngle<=tmpCurrentAngle && tmpCurrentAngle<(tmpAngle + (roundUsers.users[loop].percentage* Math.PI * 2)))
			{
				currentSegment = loop;
				break;
			}
			tmpAngle = tmpAngle + (roundUsers.users[loop].percentage* Math.PI * 2);
		}
		
		//lastAngle = 0;
		for (var i = 1; i <= len; i++) {
			//var angle = PI2 * (i / len) + angleCurrent;
			//var angle = PI2 * (1 / tickets[i]) + lastAngle;
			var angle = (PI2 * roundUsers.users[i-1].percentage) + lastAngle;

			//alert("angle = " + angle + " lastAngle=" + lastAngle);
			wheel.drawSegment(i - 1, lastAngle, angle, i - 1 == currentSegment ? true : false, img_draw);
            img_draw=1;
			lastAngle = angle;
		}
		
		//console.log(currentSegment);
		wheel.drawCenterCircle(isWheelSpinning ? roundUsers.users[currentSegment].thumb : null);
	},
};

window.onload = function() {
	wheel.init();	
		
	// setting event handlers for focus change
	if (/*@cc_on!@*/false) { // check for Internet Explorer
		document.onfocusin = onFocus;
		document.onfocusout = onBlur;
	} else {
		window.onfocus = onFocus;
		window.onblur = onBlur;
	}
    
	loadData();
	loadChat(null);
};

function onFocus() {
   
	focus = true;
	if (typeof cache_feed !== 'undefined' && cache_feed != '') {
		$('#divFeed').prepend(cache_feed);
		cache_feed = '';
	}
    // loadChat();
};

function onBlur() {
	focus = false;
}


var isWheelData = true;

function reloadWheel()
{
	var segments = new Array();
	
	if(roundUsers != null){
		isWheelData = true;
	}
	else
	{
		isWheelData = false;
		roundUsers = roundUsers_blank;
	}

	for(i=0;i<roundUsers.users.length;i++)
	{
		segments.push( roundUsers.users[i].name );
	}

	wheel.segments = segments;
	wheel.update();
}

function changeData() {

	roundUsers.users[0].percentage = 0.25;
	roundUsers.users[1].percentage = 0.25;
	roundUsers.users[2].percentage = 0.5;
	
	wheel.update();
}

function soundChange() {
	if ( roundInfo.info.sound == "off")
        
        roundInfo.info.sound = "on";
	else
        
        roundInfo.info.sound = "off";
	if ( !wheel.finished ) {
		if ( roundInfo.info.sound == "on" ) wheel.sound.play();
		else wheel.sound.pause();
	}
}

function soundChangebutton() {
	if ( roundInfo.info.sound == "off")
		roundInfo.info.sound = "on";
	else
        roundInfo.info.sound = "off";

	if ( !wheel.finished ) {
		if ( roundInfo.info.sound == "on" ) wheel.sound.play();
		else wheel.sound.pause();
	}
}


function changeWheelSpinning() {
   
	 isWheelSpinning = false;
	// clearInterval(timer);
	 loadData();
}

//function reloadWheel()
//{
//	var segments = new Array();
//	/*$.each($('#venues input:checked'), function(key, cbox) {
//		segments.push( cbox.value );
//	});*/
//	for(i=0;i<2;i++)
//	{
//		segments.push( roundUsers.users[i].name );
//	}
//
//	wheel.segments = segments;
//	wheel.update();
//}


//////////////////

//window.onload = function() {
//	wheel.init();
//	/*
//	var segments = new Array();
//	
//	for(i=0;i<roundUsers.users.length;i++)
//	{
//		segments.push( roundUsers.users[i].name );
//	}
//
//	wheel.segments = segments;
//	wheel.update();
//
//	// Hide the address bar (for mobile devices)!
//	setTimeout(function() {
//		window.scrollTo(0, 1);
//	}, 0);*/
//	
//	 loadData();
//	 // alert('2');
//	
//	$(".iframe").colorbox({iframe:true, width:"750px", height:"360px"});
//	
//}
/////////////////////////
