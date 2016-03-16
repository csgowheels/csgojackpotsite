

/**
 * File: wheelHandler.js
 * 
 * This file contains implementation of WheelHandler class. 
 * 
 * WheelHandler class is a class designed to be "wrapper" class standing
 * between Wheel and Program. It holds it's own Wheel instance, which it creates
 * when it is instantiated. It draw's wheel and sets segments. Also it contains
 * methods for spinning wheel animation along side with setting finish angle, where
 * wheel should stop.
 * 
 * Author: Acko.
 */

/**
 * Constructor of WheelHandler class // WheelHandler::WheelHandler(double, double, double, string);
 * 
 * It creates Wheel instance corresponding to values sent as parameters. Also
 * it creates other variables which are used internally, and draw's wheel using
 * Wheel::draw() method.
 */
var WheelHandler = function(x, y, r, canvasId) {
	
	this._wheel 				= new Wheel(x, y, r, canvasId);
	this._isSpinning 			= false;
	this._spinFocus 			= false;
	
	this._finishAngle 			= 0; // finish angle (winner, degrees)
	this._E 					= 0.1; // Error rate (degrees) 
	this._roundUsers			= [];
	this._price					= null;
	
	this._NUMBER_OF_LAPS 		= 8; // constant
	this._spin 					= null; // requestAnimationFrame holder
	this._spinCheck 			= null; // spinning timeout holder
	this._maxTimeCheck 			= null; // spinning max time timer holder
	
	this._MAX_OFF_TIME 			= 4000; // max time user can be looking away once (not global) ms
	this._MAX_SPIN_TIME 		= 25000; // max time wheel can spin (one round) global ms
	this._MAX_SPIN_TIME_R 		= 4000; // max time wheel can spin (after 20 seconds exceeded) ms
	this._MAX_SPINS				= 2;
	
	this._spins					= 0;
	
//	var sound					= document.createElement("audio");
//	sound.setAttribute('src', 'csgowheels2.mp3');
//	this._startSound			= sound;
	
	this._wheel.draw();
}

/**
 * resetCanvas() method // void WheelHandler::resetCanvas();
 * 
 * Function which clears out canvas, deletes old one and creates new one, with
 * wheel redirecting to a new one. This method should be called on some fixed rate of rounds
 * because of small minus rotation of canvas during rounds (truncating error).
 */
WheelHandler.prototype.resetCanvas = function() {
	console.log("RESET CANVAS");
	var canvasWrapper = document.getElementById("wheel");
	var newCanvas = document.createElement("canvas");

	newCanvas.setAttribute("id", "canvas");
	newCanvas.setAttribute("width", "600");
	newCanvas.setAttribute("height", "550");
	
	canvasWrapper.replaceChild(newCanvas, this._wheel._canvas);
	
//	this._wheel._canvas = newCanvas;
//	this._wheel._ctx = this._wheel._canvas.getContext('2d');
	this._wheel = new Wheel(this._wheel._x, this._wheel._y, this._wheel._r, "canvas");
	this._wheel.draw();
}

/**
 * spinWheel() method // void WheelHandler::spinWheel();
 * 
 * Function which is designed to handle start of wheel spin, it changes flag on spinning to true,
 * starts requestAnimationFrame function and also starts maxTimeCheck timeout 
 * (check for time wheel is spinning).
 */
WheelHandler.prototype.spinWheel = function() {
	if (this._isSpinning)
		return;
	
	// start gong-- nono very bads
//	this._startSound.currentTime = 0;
//	this._startSound.play();
	
	this._isSpinning = true;
	this._maxTimeCheck = setTimeout(this._forceStop.bind(this), this._MAX_SPIN_TIME);
	this._spin = requestAnimationFrame(this._spinWheel.bind(this, 0, 0.002));
}

/**
 * _spinWheel() method // private void WheelHandler::_spinWheel(int, double);
 * 
 * Private function which is responsible for real wheel spinning (rotation and drawing).
 * It is base for requestAnimationFrame function, and it also has it's own check on timing
 * MAX_OFF_TIME is maximum time limit between two calls to this function if wheel is spinning.
 * 
 * It calls _ease function to get new progress to which it should move wheel. Progress is a
 * double variable which presents relation how many angles wheel is rotated and how much it should
 * go total.
 */
WheelHandler.prototype._spinWheel = function(spinCount, spinSpeed) {
	clearInterval(this._spinCheck);
	this._spinFocus = true;
	
	var progress = this._calculateProgress(spinCount);
	progress = this._ease(progress);
	
	if (this._wheel.rotate(this._convertProgress(progress) * spinSpeed))
		spinCount++;
	
	this._wheel.draw();
	
	this._spinCheck = setTimeout(this._forceStop.bind(this), this._MAX_OFF_TIME);
	this._spinFocus = false;
	
	if ((this._NUMBER_OF_LAPS * 360 + this._finishAngle) - (this._wheel.getCurrentAngle() + spinCount * 360) > this._E)
		this._spin = requestAnimationFrame(this._spinWheel.bind(this, spinCount, spinSpeed))
	else
		this._stopSpinning();
}

/**
 * _stopSpinning() method // private void WheelHandler::_stopSpinning();
 * 
 * Function which is designed to stop wheel spinning, it resets spinning flag, stops AnimationFrame,
 * and clears all checking timeouts. And in the end sets 3 second timer to call _restartWheel().
 */
WheelHandler.prototype._stopSpinning = function() {
	cancelAnimationFrame(this._spin);
	this._isSpinning = false;
	this._wheel._centralImage = null;
	clearTimeout(this._maxTimeCheck);
	clearTimeout(this._spinCheck);
	setTimeout(this._restartWheel.bind(this), 3000);
}

/**
 * _restartWheel() method // private void WheelHandler::_restartWheel();
 * 
 * Function which visually (and internally) restarts wheel. It restore wheels rotation, and then,
 * restore its segments to null (signal that wheel is empty). 
 */
WheelHandler.prototype._restartWheel = function() {
	this._wheel.restoreRotation();
	
	this._spins++;
	if (this._spins >= this._MAX_SPINS) {
		this._spins -= this._MAX_SPINS;
		this.resetCanvas();
	}
	
	loadData();
	this.setRoundUsers(null);
}

/**
 * _forceStop() method // private void WheelHandler::_forceStop();
 * 
 * Also stops wheel spin (sets flag to false and stops animation) but it rotate wheel to finish angle,
 * and then sets timer to 3 seconds before calling restartWheel. This method is designed to 
 * stop wheel if user is off browser (or max spin time exceeded).
 */
WheelHandler.prototype._forceStop = function() {
	clearTimeout(this._maxTimeCheck);
	clearTimeout(this._spinCheck);
	
	/*
	if (this._spinFocus) {
		console.log("max time exceeded additional 4 sec");
		setTimeout(this._forceStop.bind(this), this._MAX_SPIN_TIME_R);
		return;
	}
	*/
	
	// stopSpinning part
	cancelAnimationFrame(this._spin);
	this._isSpinning = false;
	
	this._wheel.restoreRotation();
	this._wheel.rotate(this._finishAngle);
	this._wheel.draw();
	this._wheel._centralImage = null;
	
	// @TODO set timeout depends on focus...
	setTimeout(this._restartWheel.bind(this), 3000);
}

/**
 * _calculateProgress() method // private double WheelHandler::_calculateProgress(int);
 * 
 * This function calculates progress, percentage, which wheel is currently on, depending on
 * angle it already spin (taking in count of whole laps), and total angle, number of laps
 * which should be passed, and angle on which it should stop.
 */
WheelHandler.prototype._calculateProgress = function(spinCount) {
	if (this._wheel.getCurrentAngle() == 0 && spinCount == 0)
		return 1 / (this._NUMBER_OF_LAPS * 360 + this._finishAngle);
	return (this._wheel.getCurrentAngle() + spinCount * 360) / (this._NUMBER_OF_LAPS * 360 + this._finishAngle);
}

/**
 * _ease(double) method // private double WheelHandler::_ease(double);
 * 
 * Function based on mathematical join of three functions which represents acceleration of 
 * wheel rotation.
 */
WheelHandler.prototype._ease = function(progress) {
	if (progress < 0.05)
		return 20 * progress;
	else if (progress < 0.7)
		return Math.sqrt(1.7 - progress)
	else if (progress < 0.87)
		return 1;
	else
		return 7 * (1.005 - progress);
}

/**
 * _convertProgress(double) method // private double WheelHandler::_convertProgress(double);
 * 
 * Function which does return's angle (in degrees) for progress given to it.
 */
WheelHandler.prototype._convertProgress = function(progress) {
	return progress * (this._NUMBER_OF_LAPS * 360 + this._finishAngle);
}

/**
 * changeSegments() method // public void WheelHandler::changeSegments(list);
 * 
 * Function which sends segments to wheel (to be changed) and calls wheel's draw method
 * so that new segment's become visual.
 */
WheelHandler.prototype.changeSegments = function(segments) {
	this._wheel.changeSegments(segments);
//	this._wheel.draw();
}

/**
 * setRoundUsers() method // public void setRoundUsers(Object[]);
 * 
 * Function which gets array of new roundUsers and if they are not equal, it sets them
 * into wheel, and if they are equal then does nothing.
 */
WheelHandler.prototype.setRoundUsers = function(roundUsers) {
	if (!this._isEqualRoundUsers(roundUsers)) {
		try {
			this._wheel.setRoundUsers(roundUsers !== null ? roundUsers["users"] : null);
		} catch (e) {
			console.log("Segments unsuitable.");
		}
		this._roundUsers = roundUsers === null ? null : roundUsers["users"];
		// console.log("New roundUsers: ");
		// console.log(roundUsers);
		// console.log(this._wheel._segments);
		setTimeout(this._wheel.draw.bind(this._wheel), 500);
	}
}

/**
 * _isEqualRoundUsers(roundUsers) method // private bool _isEqualRoundUsers(Object[]);
 * 
 * Function which checks if given roundUsers is equal to last set roundUsers.
 */
WheelHandler.prototype._isEqualRoundUsers = function(roundUsers) {
	if (roundUsers === null)
		return (this._roundUsers === null);
	
	if (this._roundUsers === null)
		return false;
	
	if ((roundUsers !== null && roundUsers["users"].length == this._roundUsers.length)) {
		for (var i = 0; i < this._roundUsers.length; i++)
			if (Math.abs(roundUsers["users"][i].percentage - this._roundUsers[i].percentage) > 0.001)
				return false;
		return true;
	} else
		return false;
}

/**
 * setFinishAngle(double) method() // public void WheelHandler::setFinishAngle(double);
 * 
 * Function which sets given angle as a finish one for wheel to stop on.
 * It has soft checking (no errors are thrown, just logged to console).
 */
WheelHandler.prototype.setFinishAngle = function(angle) {
	if (angle < 360 && angle > 0)
		this._finishAngle = angle;
	else
		console.log("Unsuitable angle.");
}

/**
 * setPrice() method // public void WheelHandler::setPrice(string)
 * 
 * Public interface for setting price which will be shown on inner circle.
 */
WheelHandler.prototype.setPrice = function(price) {
	if (price != this._price)
		this._wheel.setPrice(price);
	setTimeout(this._wheel.draw.bind(this._wheel), 500);
}

/**
 * setTotalItems() method // public void WheelHandler::setTotalItems(string)
 * 
 * Public interface for setting number of items in round which will be shown on inner circle.
 */
WheelHandler.prototype.setTotalItems = function(items) {
	this._wheel.setTotalItems(items);
}

window.onload = function() {
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
}

function onFocus() {
	   
	focus = true;
	if (typeof cache_feed !== 'undefined' && cache_feed != '') {
		$('#divFeed').prepend(cache_feed);
		cache_feed = '';
	}
    // loadChat();
}

function onBlur() {
	focus = false;
}

