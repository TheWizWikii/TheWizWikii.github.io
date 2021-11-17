var canvas = {
  canvas: null,
  ctx: null,
  cw: 0,
  ch: 0,
  currentFps: 0,
  totalFps: 24,
  createItemEvery: 4,
  windowW: window.innerWidth,
  windowH: window.innerHeight
};

canvas.setup = function() {
  var t = this;

  t.canvas = document.querySelector('#introduction-canvas');
  t.ctx = t.canvas.getContext('2d');
  t.canvas.setAttribute("width", t.windowW);
  t.canvas.setAttribute("height", t.windowH);
  t.cw = t.canvas.width;
  t.ch = t.canvas.height;
  t.rythm = 1;
  t.DATA = [];
  t.totalFrameForALoop = t.totalFps * t.createItemEvery;
  t.group = new t.Group();

  canvas.draw();
};

canvas.draw = function () {
  var c = canvas;

  c.currentFps = (c.currentFps > c.createItemEvery * c.totalFps) ? 0 : c.currentFps;

  c.ctx.clearRect(0, 0, c.cw, c.ch);

  for(var r = 0; r < c.rythm; r++){
      if(c.currentFps === (c.createItemEvery * c.totalFps)/(r+1)){
          console.log("every " + c.createItemEvery/(r+1) + " second");
          c.DATA[r+1][1] = [];
          for(var j = 0; j < c.DATA[0].length; j++){
              c.DATA[r+1][1].push(c.group.createItem(c.DATA[0][j], [c.DATA[0][j][0] + c.group.width, c.DATA[0][j][1] + c.group.height], r + 1));
          }
      }
  }

  for(var r = 0; r < c.rythm; r++){
      for(var i = 0; i < c.DATA[r+1][1].length; i++){
          c.group.createSquirt(c.DATA[r+1][1][i][0], c.DATA[r+1][1][i][1], i, c.DATA[r+1]);
      }
      canvas.group.appearSquirt(c.currentFps, r +1);
  }


  setTimeout(function(time) {
      c.currentFps += 1;
      window.requestAnimationFrame(canvas.draw);
  }, 1000 / canvas.totalFps);
};


canvas.grid = function(w, h, nbHR, nbVR, all){
  var a = [];
  for(var i = 0; i < nbHR; i++){
      for(var j = 0; j < nbVR; j++){
          if(all){
              a.push([w*i, h*j])
          }else{
              if(i === j) a.push([w*i, h*j]);
          }
      }
  }
  return a;
},
canvas.randomMinMax = function(min, max){
  return Math.floor((Math.random()*(max - min)) + min);
},
canvas.plusOrMinus = function(){
  return Math.round(Math.random()) * 2 - 1;
}

//Group
canvas.Group = function() {
  var c = canvas, t = this;

  t.nbHR = 8;
  t.nbVR = 5;
  t.summit = [];
  t.summit1 = [];
  t.width = c.cw / t.nbHR;
  t.height = c.ch / t.nbVR;

  //Squirt
  t.squirt = {
      x1: 1,
      y1: 1,
      nb: 3,
      opacity:0 ,
      acceleration:0,
      opacityCurve:0,
      velocity: 0.09,
      stepLength: t.velocity * (c.createItemEvery * c.totalFps),
      width: 12
  };
  t.all = true;
  t.counter = 0;
  t.counter1 = 0;
  t.increase = Math.PI / 100;
  t.counterCount = 0;

  c.DATA = [];

  c.DATA.push(c.grid(t.width, t.height, t.nbHR, t.nbVR, true)); // A

  for(var r = 0; r < c.rythm; r++) {c.DATA.push(
          t.squirt
       ); // B, C
  }

  //      [tDATA
  //            A:[Grille] Always first child
  //            B:[groupe 1
  //                C:[ Object : {vitesse, acceleration, opacité, settings, ...} ] Always first child
  //                E:[values x, y
  //                      [x, y]
  //                      [x, y]
  //                      [x, y]...
  //                  ]
  //            ]
  //            B:[groupe 2
  //                C:[ Object : {vitesse, acceleration, opacité, settings, ...} ] Always first child
  //                E:[values x, y
  //                      [x, y]
  //                      [x, y]
  //                      [x, y]...
  //                  ]
  //            ]
  //      ]

  for(var r = 0; r < c.rythm; r++) {
      c.DATA[r+1][1] = [];
      for(var m = 0; m < c.DATA[0].length; m++){
          c.DATA[r+1][1].push(t.createItem(c.DATA[0][m], [c.DATA[0][m][0] + t.width, c.DATA[0][m][1] + t.height], r + 1));
      }
  }

  console.log(c.DATA);

};
canvas.Group.prototype.createItem = function(top, bot, r) {
  var c = canvas, t = this, s = t.squirt;

  // Reset squirt values
  c.DATA[r][0] = [];
  c.DATA[r][0].x1 = 1;
  c.DATA[r][0].y1 = 1;
  c.DATA[r][0].nb = s.nb;
  c.DATA[r][0].width = s.width;
  c.DATA[r][0].velocity = 0.05;
  c.DATA[r][0].stepLength = 25;
  c.DATA[r][0].acceleration = 0;
  c.DATA[r][0].counterCount = 0;
  c.DATA[r][0].counter = 0;
  c.DATA[r][0].counter1 = 0;
  c.DATA[r][0].opacityCurve = 0;

  return [c.randomMinMax( bot[0] ,  top[0] ),       c.randomMinMax(bot[1] ,  top[1] )];

  //// //Draw the grid !
  //c.ctx.strokeStyle="green";
  //c.ctx.fillStyle="green";
  ////c.ctx.strokeRect( top[0] ,  top[0] , t.width, t.height);
  ////c.ctx.fillText(i,  top[0] ,  top[1] );
  //
  //c.ctx.beginPath();
  //c.ctx.moveTo( top[0] , top[1] );
  //c.ctx.lineTo( bot[0] , top[1] );
  //c.ctx.stroke();
  //c.ctx.fillRect( bot[0] ,  top[1] , 10, 10);
  //
  //c.ctx.strokeStyle="yellow";
  //c.ctx.fillStyle="yellow";
  //
  //c.ctx.beginPath();
  //c.ctx.moveTo( bot[0] , top[1] );
  //c.ctx.lineTo( bot[0] ,bot[1] );
  //c.ctx.stroke();
  //c.ctx.fillRect( bot[0] , bot[1] , 10, 10);
  //
  //c.ctx.strokeStyle="red";
  //c.ctx.fillStyle="red";
  //
  //c.ctx.beginPath();
  //c.ctx.moveTo( bot[0] ,bot[1] );
  //c.ctx.lineTo( top[0] ,bot[1] );
  //c.ctx.stroke();
  //c.ctx.fillRect( top[0] , bot[1] , 10, 10);
  //
  //c.ctx.strokeStyle="purple";
  //
  //c.ctx.beginPath();
  //c.ctx.moveTo( top[0] ,bot[1] );
  //c.ctx.lineTo( top[0] , top[1] );
  //c.ctx.stroke();
},
canvas.Group.prototype.createSquirt = function(x, y, k, group) {
  var c = canvas, t = this, s = t.squirt, x, y;

  //group[1] = group[1];
  //console.log(group[1]);
  c.ctx.translate(group[1][k][0], group[1][k][1]);
  c.ctx.rotate((k * 20) * Math.PI / 180);
  c.ctx.translate(-group[1][k][0], -group[1][k][1]);

      for(var i = 0; i < group[0].nb; i++){
          if(group[0].x1 > 0 || group[0].y1 > 0){
              switch(i) {
                  case 1:
                      x = 1; y = 1;
                      c.ctx.strokeRect(group[1][k][0] + ((x * group[0].x1) * (group[0].acceleration + 1)), group[1][k][1] + ((y * group[0].y1) * (group[0].acceleration + 1)), group[0].width, group[0].width);
                      break;
                  case 2:
                      x = -1; y = 1;
                      c.ctx.beginPath();
                      c.ctx.arc(group[1][k][0] + ((x * group[0].x1) * (group[0].acceleration + 1)) - group[0].width, group[1][k][1] + ((y * group[0].y1) * (group[0].acceleration + 1)) + (group[0].width/2), (group[0].width/2), 0, 2 * Math.PI, false);
                      c.ctx.closePath();
                      break;
                  case 0:
                      x = 0; y = -1;
                      c.ctx.beginPath();
                      c.ctx.moveTo(group[1][k][0] + ((x * group[0].x1) * (group[0].acceleration + 1)), group[1][k][1] + ((y * group[0].y1) * (group[0].acceleration + 1))); // begin group[1]
                      c.ctx.lineTo(group[1][k][0] + ((x * group[0].x1) * (group[0].acceleration + 1)) + (group[0].width/2), group[1][k][1] + ((y * group[0].y1) * (group[0].acceleration + 1)) - group[0].width); // continue horizontally (left or right)
                      c.ctx.lineTo(group[1][k][0] + ((x * group[0].x1) * (group[0].acceleration + 1)) - (group[0].width/2), group[1][k][1] + ((y * group[0].y1) * (group[0].acceleration + 1)) - group[0].width); // continue transverse ( "/" or "\" )
                      c.ctx.closePath();
                      break;
              }
              c.ctx.lineWidth = 2;
              c.ctx.strokeStyle = "rgba( 10 ,  10, 10, " + group[0].opacityCurve + ")";
              c.ctx.stroke();
          }
      }
  c.ctx.translate(group[1][k][0], group[1][k][1]);
  c.ctx.rotate(-(k * 20) * Math.PI / 180);
  c.ctx.translate(-group[1][k][0], -group[1][k][1]);
},
canvas.Group.prototype.appearSquirt = function(current, r) {
  var c = canvas, t = this, s = t.squirt;

  if(c.DATA[r][0].x1 > c.DATA[r][0].stepLength || c.DATA[r][0].x1 < -c.DATA[r][0].stepLength  || c.DATA[r][0].y1 === 0 || c.DATA[r][0].x1 === 0 ){
      c.DATA[r][0].x1 = 0;
      c.DATA[r][0].y1 = 0;
  }else{
      c.DATA[r][0].x1 += c.DATA[r][0].velocity;
      c.DATA[r][0].y1 += c.DATA[r][0].velocity;
  }

      t.counterCount += Math.round((1/c.totalFrameForALoop)*100)/100;
      c.DATA[r][0].acceleration = Math.round(Math.abs(Math.sin(t.counter1/2))*100)/100;
      c.DATA[r][0].opacityCurve = Math.round(Math.abs(Math.sin(t.counter1))*10)/10;

      t.counter += 1/c.totalFrameForALoop;
      t.counter1 += Math.PI / c.totalFrameForALoop;

      //Draw the curves (Need to comment clearRect4
      //curveX = t.counterCount;
      //c.ctx.fillRect(curveX * 100, c.DATA[r][0].acceleration*100, 1, 1);
      //c.ctx.fillRect(curveX * 100, c.DATA[r][0].opacityCurve*100, 1, 1);

};

canvas.setup();

