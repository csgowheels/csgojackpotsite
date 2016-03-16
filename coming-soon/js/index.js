(function() {
  var Blip, blips, c, ch, clear, ctx, cw, divider, globalTick, initialBlips, pi2, rand, run;

  c = document.getElementById('c');

  ctx = c.getContext('2d');

  divider = 4;

  cw = c.width = window.innerWidth / divider;

  ch = c.height = window.innerHeight / divider;

  pi2 = Math.PI * 2;

  blips = [];

  initialBlips = 30;

  globalTick = 0;

  rand = function(min, max) {
    return Math.floor((Math.random() * (max - min + 1)) + min);
  };

  Blip = function(x, y) {
    this.x = x;
    this.y = y;
    this.r = .1;
    this.rGrowthBase = 1;
    this.rGrowth = this.rGrowthBase;
    this.rMax = (cw + ch) / 7;
    return this.alpha = 1;
  };

  Blip.prototype.update = function(i) {
    var percent;
    percent = (this.rMax - this.r) / this.rMax;
    this.rGrowth = .1 + this.rGrowthBase * percent;
    this.r += this.rGrowth;
    this.alpha = percent;
    if (this.r > this.rMax) {
      return blips.splice(i, 1);
    }
  };

  Blip.prototype.render = function(i) {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.r, 0, pi2, false);
    ctx.fillStyle = 'hsla(' + rand(globalTick - 280, globalTick + 80) + ', 50%, 1%, ' + this.alpha + ')';
    return ctx.fill();
  };

  clear = function() {
    ctx.globalCompositeOperation = 'destination-out';
    ctx.fillStyle = 'hsla(0, 0%, 0%, .05)';
    ctx.fillRect(0, 0, cw, ch);
    return ctx.globalCompositeOperation = 'lighter';
  };

  run = function() {
    var i;
    
      
    window.requestAnimationFrame(run, c);
    clear();
    i = blips.length;
    while (i--) {
      blips[i].update(i);
    }
    i = blips.length;
    while (i--) {
      blips[i].render(i);
    }
    blips.push(new Blip(rand(0, cw), rand(0, ch)));
    return globalTick++;
  };

  $(window).on('resize', function() {
    cw = c.width = window.innerWidth / divider;
    return ch = c.height = window.innerHeight / divider;
  });

  window.requestAnimationFrame || (window.requestAnimationFrame = window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function(callback, element) {
    return window.setTimeout(function() {
      return callback(+new Date());
    }, 1000 / 60);
  });

  run();

}).call(this);