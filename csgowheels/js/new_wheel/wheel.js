/**
 * File: wheel.js
 * 
 * This file contains implementation of Wheel class. 
 * 
 * Wheel class contains representation of wheel with all data,
 * position of center (x, y), radius (r), innerCircle radius,
 * canvas, on which it will work, segments (array with percentage data...)
 * 
 * Author: Acko.
 */

/**
 * Constructor of wheel class // Wheel::Wheel();
 * 
 * Sets all passed parameters to objects variables, calculates innerCircle radius,
 * and initializes other instance's variables. Also it clips rectangle around wheel
 * so that inside wheel class it will change only that rectangle (not whole canvas).
 */
var Wheel = function(x, y, r, canvasId) {
	this._x 					= x;
	this._y 					= y;
	this._r 					= r;

	this._smallRadius 			= this._r * 0.25;
	this._currentAngle 			= 0; // read-only property 
	
	this._areaBound 			= 10;
	this._IMAGE_SIZE			= 32; // R for little user images
	this._IMAGE_R				= this._r - this._IMAGE_SIZE; // where center of picture will be
	this._CIRCLE_ROUND 			= 7; // circle round area
	this._CIRCLE_ROUND_I		= 2; // thinner line just after circle
	
	this._totalItems 			= "0P";
	this._totalPrice			= "$0";
	this._centralImage			= null;
	
	this._roundUsers			= [];
	this._segments 				= [];
	this._segmentPoints 		= []; // contains information about start and end points for each segment
	this._segmentAngles 		= []; // contains information about angles for each segment
	this._segmentImages			= []; // positions of images for each segment
	this._segmentImagesAngle	= []; // middle angle for each segment
	this._segmentImageObjects	= []; // image objects (loaded image objects)
	
	this._colors 				= ['#2e3338', '#495058', '#5f6872', '#77828e', '#8b99a7', '#9faebe',
					 			   '#1a313f', '#6e808c', '#b6c0a8', '#637370', '#1a313f', '#6e808c',
								   '#1a313f', '#6e808c', '#b6c0a8', '#637370', '#1a313f', '#6e808c'];
	
	this._canvasWrapper 		= "wheel";  // hard-code this for now
	this._canvas 				= document.getElementById(canvasId);
	this._ctx 					= this._canvas.getContext('2d');
	this._ctx.textBaseline 		= "middle";
	this._ctx.textAlign    		= "center";
	this._ctx.font         		= "1.4em Arial";
	
	this._blankImage 			= new Image();
	this._blankImage.src 		= "images/wheel3.png";
	
	// clip canvas only to wheel rectangle
	this._ctx.rect(	this._x - this._r - this._areaBound,
					this._y - this._r - this._areaBound,
					this._x + this._r + this._areaBound,
					this._y + this._r + this._areaBound);
	this._ctx.clip()
}

//////////////////////////////////////////////////////
// Public Methods (Interface)
//////////////////////////////////////////////////////

/**
 * Rotate() method // public bool Wheel::rotate(double);
 * 
 * It rotates wheel for angle degrees (angle should be in degrees not radian).
 * Also it updates _currentAngle variable so that restoreRotation could restore
 * wheel to it's base.
 */
Wheel.prototype.rotate = function(angle) {
	var retValue = false;
	
	this._currentAngle += angle;
	if (this._currentAngle >= 360) {
		this._currentAngle -= 360;
		retValue = true;
	}
	
	// position canvas center to wheel's center
	this._ctx.translate(this._x, this._y);
	this._ctx.rotate(angle * Math.PI  / 180);
	// return position to (0, 0) top left
	this._ctx.translate(-this._x, -this._y);
	
	return retValue;
}

/**
 * _RestoreRotation() method // public void Wheel::restoreRotation();
 * 
 * It restore's canvas rotation to 0 degrees, using 
 */
Wheel.prototype.restoreRotation = function() {
	this.rotate(-this._currentAngle);
}

/**
 * setRoundUsers() method // public void Wheel::setRoundUsers(array);
 * 
 * This is support (patch) for roundUsers instead of only percentage.
 */
Wheel.prototype.setRoundUsers = function(roundUsers) {
	
	if (roundUsers == null) {
		this._changeSegments(null);
		return;
	}
	
	var img;
	temp_segments = [];
	for (var i = 0; i < roundUsers.length; i++)
		temp_segments.push(parseFloat(roundUsers[i]["percentage"]) * 100);
	
	this._changeSegments(temp_segments);
	this._roundUsers = roundUsers.reverse();
	
	for (var i = 0; i < this._segments.length; i++) {
		img = new Image();
		img.src = this._roundUsers[i]["thumb"] == "" ? null : this._roundUsers[i]["thumb"]; //"https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/c3/c3f8318408d72b0907f47a2299559e706f334179.jpg";
		this._segmentImageObjects[i] = img;
	}
}

/**
 * ChangeSegments() method // public void Wheel::changeSegments(Object[]);
 * 
 * It sets _segments variable to new array passed by to method, only if
 * new array is valid (sums to 100 %). If not valid it logs message and does nothing.
 * Exception to that rule is if null is sent (it is signal that wheel is empty).
 * 
 * TODO: Add roundUsers support
 */
Wheel.prototype._changeSegments = function(segments) {
	
	if (segments == null) {
		this._segments = [];
		return;
	}
	
	var sum = 0;
	segments.forEach(function(element, index, array) {
		sum += element;
	});
	if (Math.abs(100 - sum) > 0.1) {
		throw "Segments unsuitable";
	}
	
	this._segments 				= segments.reverse(); // this is necessary because reversed coordinate system
	this._segmentAngles 		= [];
	this._segmentPoints 		= [];
	this._segmentImages			= [];
	this._segmentImagesAngle	= [];
	this._segmentImageObjects	= [];
	this._calculatePointsAndAngles();
}

/**
 * getCurrentAngle() method // public double Wheel::getCurrentAngle() const;
 * 
 * Getter method for currentAngle property (read-only).
 */
Wheel.prototype.getCurrentAngle = function() {
	return this._currentAngle;
}

/**
 * setPrice() method // public void Wheel::setPrice(string)
 * 
 * Public interface for setting price which will be shown on inner circle.
 */
Wheel.prototype.setPrice = function(price) {
	this._totalPrice = price;
}

/**
 * setTotalItems() method // public void Wheel::setTotalItems(string)
 * 
 * Public interface for setting number of items in round which will be shown on inner circle.
 */
Wheel.prototype.setTotalItems = function(totalItems) {
	this._totalItems = totalItems
}

/**
 * Draw() method // public void Wheel::draw() const;
 * 
 * It is a public interface for drawing the wheel.
 * First it clears clipped rectangle, then draws baseCircle,
 * all segments, and innerCircle (all by private methods)
 */
Wheel.prototype.draw = function() {	
	this._clear();
	this._drawBaseCircle();	
	this._drawSegments();
	
	if (this._segments.length == 0) 
		this._ctx.drawImage(this._blankImage, this._x - this._r, this._y - this._r, this._r * 2, this._r * 2);
	
	this._drawNeedle();
	this._drawInnerCircle();
}

//////////////////////////////////////////////////////
//Private Methods
//////////////////////////////////////////////////////

/**
 * _DrawBaseCircle() method // private void Wheel::_drawBaseCircle() const;
 * 
 * It draws base circle outline (current drawing doesn't change anything)
 * 
 * TODO: Add outline (to be visible)
 */
Wheel.prototype._drawBaseCircle = function() {
	this._ctx.fillStyle = "#3e444c";
	this._ctx.beginPath();
	this._ctx.arc(this._x, this._y, this._r + this._CIRCLE_ROUND, 0, Math.PI * 2);
	this._ctx.closePath();
	this._ctx.fill();
	
	this._ctx.strokeStyle = "#757f89";
	this._ctx.lineWidth = this._CIRCLE_ROUND_I;
	this._ctx.beginPath();
	this._ctx.arc(this._x, this._y, this._r + this._ctx.lineWidth, 0, Math.PI * 2);
	this._ctx.closePath();
	this._ctx.stroke();
}

/**
 * _DrawSegments() method // private void Wheel::_drawSegments() const;
 * 
 * It draws each segment, using _segment array to work with.
 * For each segment it calculates startAngle and endAngle, and
 * using that values it calculates startPoint and endPoint.
 * 
 * Those values are later used for drawing lines from center to points and 
 * drawing arc from startAngle to endAngle.
 * 
 * TODO: Add images and text support
 */
Wheel.prototype._drawSegments = function() {
	var startAngle 		= 0 * Math.PI / 180;
	var endAngle 		= 0 * Math.PI / 180;
	var startPoint 		= [this._x + this._r, this._y];
	var endPoint 		= [this._x + this._r, this._y];
	var img				= null;
	var textPoint		= null;
	
	for (var i = 0; i < this._segments.length; i++) {
		// escape calculating (calculate start angle and point, and end angle and point only when segments are changed)
		startAngle 		= this._segmentAngles[i][0];
		endAngle 		= this._segmentAngles[i][1];
		startPoint 		= this._segmentPoints[i][0];
		endPoint 		= this._segmentPoints[i][1];
		imagePoint 		= this._segmentImages[i];
		
		this._ctx.save();
		
		this._ctx.lineWidth = 1;
		this._ctx.fillStyle = "#000000";
		
		// highlight
		if (this._isCurrentSegment(i)) {
		}
		
		this._ctx.fillStyle = this._colors[(this._segments.length - 1 - i) % this._colors.length];
		this._ctx.beginPath();
		this._ctx.moveTo(this._x, this._y);
		this._ctx.lineTo(startPoint[0], startPoint[1]);
		this._ctx.arc(this._x, this._y, this._r, startAngle, endAngle);
		this._ctx.moveTo(this._x, this._y);
		this._ctx.lineTo(endPoint[0], endPoint[1]);
		this._ctx.closePath();
		this._ctx.stroke();
		this._ctx.fill();
		
		// text writing
		
		if (this._segments[i] > 2) {
			this._ctx.translate(this._x, this._y);
			this._ctx.rotate(this._segmentImagesAngle[i]);
			this._ctx.translate(-this._x, -this._y);
			
			this._ctx.font = "15px Ebrima";
			this._ctx.fillStyle = "white";
			
			// does not include sin and cos because whole canvas is rotated on the segment image angle
			textPoint = [this._x + (this._smallRadius + (this._IMAGE_R - this._smallRadius) / 2), this._y];
			
			if (this._segmentImagesAngle[i] + this._currentAngle * Math.PI / 180 > Math.PI / 2 
					&& this._segmentImagesAngle[i] + this._currentAngle * Math.PI / 180 < 3 * Math.PI / 2) {
				this._ctx.translate(textPoint[0], textPoint[1]);
				this._ctx.rotate(Math.PI);
				this._ctx.translate(-textPoint[0], -textPoint[1]);
			}
			
			this._ctx.fillText(this._segments[i].toFixed(2) + " %", this._x + this._smallRadius + (this._IMAGE_R - this._smallRadius) / 2, this._y);
			
			if (this._segmentImagesAngle[i] + this._currentAngle * Math.PI / 180 > Math.PI / 2 
					&& this._segmentImagesAngle[i] + this._currentAngle * Math.PI / 180 < 3 * Math.PI / 2) {
				this._ctx.translate(textPoint[0], textPoint[1]);
				this._ctx.rotate(-Math.PI);
				this._ctx.translate(-textPoint[0], -textPoint[1]);
			}
			
			this._ctx.translate(this._x, this._y);
			this._ctx.rotate(-this._segmentImagesAngle[i]);
			this._ctx.translate(-this._x, -this._y);
		}
		
		// draw image
		this._ctx.translate(imagePoint[0], imagePoint[1]);
		this._ctx.rotate(-this._currentAngle * Math.PI / 180);
		this._ctx.translate(-imagePoint[0], -imagePoint[1]);
		
		this._ctx.fillStyle = "#000000";
		this._ctx.strokeStyle = "#FFFFFF";
		this._ctx.beginPath();
		this._ctx.arc(imagePoint[0], imagePoint[1], this._IMAGE_SIZE / 2, 0, Math.PI * 2);
		this._ctx.closePath();
		this._ctx.clip();
		this._ctx.drawImage(this._segmentImageObjects[i], imagePoint[0] - this._IMAGE_SIZE / 2, imagePoint[1] - this._IMAGE_SIZE / 2, this._IMAGE_SIZE, this._IMAGE_SIZE);
		
		this._ctx.restore();
	}
}

/**
 * _isCurrentSegment(int) method // bool Wheel::_isCurrentSegment(int) const;
 * 
 * Method returns true if i-th segment is current, and false otherwise.
 * Mathematics is based on that coordinate system in canvas has reversed y vector, so current angle
 * is actually - currentAngle, and adding 2pi for range boundaries, angle * pi / 180 is just turning
 * angle from degrees to radians.
 */
Wheel.prototype._isCurrentSegment = function(i) {
	if (2 * Math.PI - this._currentAngle * Math.PI / 180 >= this._segmentAngles[i][0] /* startAngle */
			&& 2 * Math.PI - this._currentAngle * Math.PI / 180 < this._segmentAngles[i][1] /* endAngle */) {
		this._centralImage = this._roundUsers[i]["thumb"];
		return true;
	}
	return false;
}

/**
 * _DrawInnerCircle() method // private void Wheel::_drawInnerCircle() const;
 * 
 * It draws innerCircle on top of the segments drawn. (Must be called after _DrawSegments())
 * 
 * TODO: Add round info and image support
 */
Wheel.prototype._drawInnerCircle = function() {
	this._ctx.translate(this._x, this._y);
	this._ctx.rotate(-this._currentAngle * Math.PI / 180);
	this._ctx.translate(-this._x, -this._y);
	
	// Top inner circle
	this._ctx.strokeStyle = "#757f89";
	this._ctx.lineWidth = 4;
	this._ctx.fillStyle = "#900407";
	this._ctx.beginPath();
	this._ctx.arc(this._x, this._y, this._smallRadius, Math.PI, 2 * Math.PI);
	this._ctx.closePath();
	this._ctx.stroke();
	this._ctx.fill();
	
	// Bottom inner circle
	this._ctx.strokeStyle = "#757f89";
	this._ctx.lineWidth = 4;
	this._ctx.fillStyle = "#510000";
	this._ctx.beginPath();
	this._ctx.arc(this._x, this._y, this._smallRadius, 0, Math.PI);
	this._ctx.closePath();
	this._ctx.stroke();
	this._ctx.fill();
	
	// mid line
	this._ctx.strokeStyle = "#FFFFFF";
	this._ctx.lineWidth = 2;
	this._ctx.beginPath();
	this._ctx.moveTo(this._x + this._smallRadius, this._y);
	this._ctx.lineTo(this._x - this._smallRadius, this._y);
	this._ctx.closePath();
	this._ctx.stroke();
	
	this._ctx.font = "bold 17px Ebrima";
	this._ctx.fillStyle = "white";
	
	this._ctx.fillText(this._totalItems, this._x, this._y - this._ctx.lineWidth - 15);
	this._ctx.fillText(this._totalPrice, this._x, this._y + 20);
	
	if (this._centralImage != null) {
		var img = new Image();
		img.src = this._centralImage;
		
		this._ctx.save();
		this._ctx.strokeStyle = "#FFFFFF";
		this._ctx.lineWidth = 6;
		this._ctx.beginPath();
		this._ctx.arc(this._x, this._y, this._IMAGE_SIZE, 0, Math.PI * 2);
		this._ctx.closePath();
		this._ctx.stroke();
		this._ctx.clip();
		this._ctx.drawImage(img, this._x - this._IMAGE_SIZE, this._y - this._IMAGE_SIZE, this._IMAGE_SIZE * 2, this._IMAGE_SIZE * 2);
		this._ctx.restore();
	}
	
	// restore rotation
	this._ctx.translate(this._x, this._y);
	this._ctx.rotate(this._currentAngle * Math.PI / 180);
	this._ctx.translate(-this._x, -this._y);
}

/**
 * _drawNeedle() method // private void Wheel::_drawNeedle() const;
 * 
 * Function which draw's needle on right side of wheel (which indicates winner).
 */
Wheel.prototype._drawNeedle = function() {
	this._ctx.translate(this._x, this._y);
	this._ctx.rotate(-this._currentAngle * Math.PI / 180);
	this._ctx.translate(-this._x, -this._y);
	
	var addConstantX 	= 30,
		addConstantY 	= 10,
		r 				= 14,
		angle 			= Math.asin(addConstantY / r),
		centerPosX 		= this._x + this._r + addConstantX + r * Math.cos(angle),
		centerPosY 		= this._y;


	this._ctx.lineWidth = 3;
	this._ctx.strokeStyle = '#000000';
	var g = this._ctx.createRadialGradient(this._x + this._r - addConstantX, this._y, addConstantY,
									 this._x + this._r + addConstantX * 1.2, this._y, addConstantY);
	g.addColorStop(1, "#333333");
	g.addColorStop(0, "#7a8088");
	this._ctx.fillStyle = g;
	
	this._ctx.beginPath();
	this._ctx.moveTo(this._x + this._r - addConstantX, this._y);
	this._ctx.lineTo(this._x + this._r + addConstantX, this._y + addConstantY);			
	this._ctx.arc(centerPosX, centerPosY, r, Math.PI - angle, Math.PI + angle);
	this._ctx.lineTo(this._x + this._r - addConstantX, this._y);
	this._ctx.closePath();
	
	this._ctx.stroke();
	this._ctx.fill();
	
	this._ctx.translate(this._x, this._y);
	this._ctx.rotate(this._currentAngle * Math.PI / 180);
	this._ctx.translate(-this._x, -this._y);
}

/**
 * _calculateEndAngle() method // private double Wheel::_calculateEndAngle() const;
 * 
 * Based on current startAngle (last endAngle) and segment size, it calculates
 * new angle on which current segment should end.
 */
Wheel.prototype._calculateEndAngle = function(i, startAngle) {
	if (i == this._segments.length - 1)
		return 2 * Math.PI;
	
	return startAngle + (this._segments[i] * Math.PI * 2 / 100);
}

/**
 * _calculatePointsAndAngles() method // private void Wheel::_calculatePointAndAngles();
 * 
 * Calculates all angles and (start and end) points based on current _segment array.
 */
Wheel.prototype._calculatePointsAndAngles = function() {
	var startAngle 		= 0;
	var endAngle 		= 0;
	var startPoint 		= 0;
	var endPoint 		= 0;
	var imageAngle		= 0;
	var imagePoint		= 0;
	
	for (var i = 0; i < this._segments.length; i++) {
		if (i > 0)
			startAngle = this._segmentAngles[i - 1][1]; // endAngle from last segment
		endAngle = this._calculateEndAngle(i, startAngle);
		this._segmentAngles[i] = [startAngle, endAngle];
		
		startPoint = [this._x + Math.cos(startAngle) * this._r, this._y + Math.sin(startAngle) * this._r];
		endPoint = [this._x + Math.cos(endAngle) * this._r, this._y + Math.sin(endAngle) * this._r];
		this._segmentPoints[i] = [startPoint, endPoint];
		
		imageAngle = startAngle + (endAngle - startAngle) / 2;
		this._segmentImagesAngle[i] = imageAngle;
		imagePoint = [this._x + Math.cos(imageAngle) * this._IMAGE_R, this._y + Math.sin(imageAngle) * this._IMAGE_R];
		this._segmentImages[i] = imagePoint;
	}
}

/**
 * _Clear() method // private double Wheel::_clear() const;
 * 
 * Clears clipped rectangle, deleting old wheel data.
 */
Wheel.prototype._clear = function() {
	// in order for this to work, ctx.lineWidth must stay same as it was when wheel was drawn.
	this._ctx.clearRect(this._x - this._r - this._areaBound,
						this._y - this._r - this._areaBound,
						this._x + this._r + this._areaBound,
						this._y + this._r + this._areaBound);
}


//////////////////////////////////////////////////////
// Testing
//////////////////////////////////////////////////////

/*
window.onload = function() {
	var wheel = new Wheel(400, 300, 200, "canvas");
	wheel.changeSegments([12.22, 18.93, 43.12, 25.73]);
	wheel.draw();
	setTimeout(test, 1000, wheel, 0);
}

function test(wheel, i) {
	if (i < 20) {
		wheel.rotate(20);
		wheel.draw();
		setTimeout(test, 1000, wheel, ++i);
	} else {
		wheel.restoreRotation();
		wheel.draw();
	}
}
*/