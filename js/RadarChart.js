function RadarChart(context)
{
   this.ctx = context;
   this.width = 0;
   this.height = 0;
   this.maxSize = 0;
   this.config;
   this.defaults =
   {
      FontFamily: "'Arial'",
      FontSize: 10,
      FontStyle: "normal",
      FontColor: "#666",
      LableBorder: 5,
      BackgroundColor: "rgba(220,220,220,0.5)",
      ScaleLineColor: "rgba(0,0,0,.1)",
      ScaleLineWidth: 1,
      MinAngle: 0,
      MaxAngle: 360,
      AngleScale: 45,
      ValueScale: 20,
      ValueScaleByMaxMin: false,
      ValueScaleMax: 100,
      ValueScaleMin: 0,
      PointDotRadius : 3,
      PointDotStrokeWidth : 1,
      PointColor : "rgba(220,220,220,1)",
      PointStrokeColor : "#fff",
      AngleCorrection: 60
   };

   this.dataPoints = [];

   this.Scale = 0;

   this.init = function(options)
   {
      this.width = this.ctx.canvas.width;
      this.height = this.ctx.canvas.height;
	  this.config = this.mergeChartConfig(this.defaults, options);
      this.config.AngleCorrection -= ((this.config.MaxAngle + this.config.MinAngle) - this.config.AngleCorrection) / 2;

      if (window.devicePixelRatio)
      {
         this.ctx.canvas.style.width = this.width + "px";
         this.ctx.canvas.style.height = this.height + "px";
         this.ctx.canvas.height = this.height * window.devicePixelRatio;
         this.ctx.canvas.width = this.width * window.devicePixelRatio;
         this.ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
      }
   };

   this.drawDiagram = function()
   {
      this.maxSize = (this.min([this.width, this.height]) / 2);

      var maxLableLength = 0;

      this.ctx.clearRect(0, 0, this.width, this.height);

      for (var angle = this.config.MinAngle; angle <= this.config.MaxAngle; angle += this.config.AngleScale)
      {
         this.ctx.font = this.config.FontStyle + " " + this.config.FontSize + "px " + this.config.FontFamily;

         var textMeasurement = this.ctx.measureText(angle + "\u00B0").width + (this.config.LableBorder * 2);

         if(textMeasurement > maxLableLength)
         {
            maxLableLength = textMeasurement;
         }
      }

      this.maxSize -= maxLableLength;

/* this is drawing the radius lines and labels */

      for(var angle = this.config.MinAngle; angle <= this.config.MaxAngle; angle += this.config.AngleScale)
      {
         // prevent drawing on the same ground
         if(angle === 360 && this.config.MinAngle === 0)
         {
            continue;
         }
		
         var labelText = angle + "\u00B0";

         this.ctx.beginPath();

         this.ctx.moveTo(this.width / 2, this.height / 2);
         this.ctx.lineTo((this.width / 2) + this.maxSize * Math.cos((angle + this.config.AngleCorrection) / 180 * Math.PI), (this.height / 2) + this.maxSize * Math.sin((angle + this.config.AngleCorrection) / 180 * Math.PI));

         this.ctx.strokeStyle = this.config.ScaleLineColor;
         this.ctx.lineWidth = this.config.ScaleLineWidth;

         this.ctx.stroke();

         this.ctx.textAlign = 'center';
         this.ctx.font = this.config.FontStyle + " " + this.config.FontSize + "px " + this.config.FontFamily;
         this.ctx.textBaseline = "middle";

         this.ctx.fillStyle = this.config.FontColor;

         var textMeasurement = this.ctx.measureText(labelText).width + (this.config.LableBorder * 2);

         this.ctx.fillText(labelText, (this.width / 2) + (this.maxSize + (textMeasurement / 2)) * Math.cos((angle + this.config.AngleCorrection) / 180 * Math.PI), (this.height / 2) + (this.maxSize + (textMeasurement / 2)) * Math.sin((angle + this.config.AngleCorrection) / 180 * Math.PI));
      }

/* this is drawing the arcs */
      if(!this.config.ValueScaleByMaxMin)
      {
         this.Scale = this.maxSize / (this.config.ValueScaleMax - this.config.ValueScaleMin);

         for(var scaleStep = 0; scaleStep <= this.maxSize; scaleStep += (this.Scale * this.config.ValueScale))
         {
            this.ctx.beginPath();

            this.ctx.arc((this.width / 2), (this.height / 2), scaleStep, (this.config.MinAngle + this.config.AngleCorrection) / 180 * Math.PI, (this.config.MaxAngle + this.config.AngleCorrection) / 180 * Math.PI);

            this.ctx.strokeStyle = this.config.ScaleLineColor;
            this.ctx.lineWidth = this.config.ScaleLineWidth;

            this.ctx.stroke();
         }
      }
      else
      {
         var minMax = this.findMinMaxValue(this.dataPoints, 'value');
         var valueRange = minMax.max - minMax.min;

         this.Scale = this.maxSize / valueRange;

         for(var scaleStep = 0; scaleStep <= this.maxSize; scaleStep += (this.Scale * this.config.ValueScale))
         {
            this.ctx.beginPath();

            this.ctx.arc((this.width / 2), (this.height / 2), scaleStep, (this.config.MinAngle + this.config.AngleCorrection) / 180 * Math.PI, (this.config.MaxAngle + this.config.AngleCorrection) / 180 * Math.PI);

            this.ctx.strokeStyle = this.config.ScaleLineColor;

            this.ctx.lineWidth = this.config.ScaleLineWidth;

            this.ctx.stroke();
         }
      }
	};
/*	 this is filling gradient colors from center to the datapoints */
   this.drawFreq = function(rcolor){
	 //rcolor = this.shuffle(this.colors);
	 //console.log(rcolor[0]);
     for(var y = 0; y < (this.dataPoints.length - 1); ++y)
      {
		//console.log(this.dataPoints);
		
         var pos1 = this.calculateDataPosition(this.dataPoints[y]);

         var pos2 = this.calculateDataPosition(this.dataPoints[y + 1]);

         this.ctx.beginPath();

         this.ctx.moveTo((this.width / 2), (this.height / 2));
		//console.log('moveto point: '+(this.width / 2)+' : '+ (this.height / 2));
		
         this.ctx.lineTo(pos1.x, pos1.y);
		//console.log('1st lineto: '+pos1.x +' : '+pos1.y);
		
         this.ctx.lineTo(pos2.x, pos2.y);
		//console.log('2nd lineto: '+pos2.x +' : '+pos2.y);

         this.ctx.closePath();

		 if (this.colornum == 7){
			 this.colornum = 0;
		 }	
         this.ctx.fillStyle = rcolor;		 
		 this.ctx.fill(); 
		 this.ctx.lineWidth = 2;
		 this.ctx.strokeStyle = rcolor;
		 this.ctx.stroke();
      }
	this.dataPoints = [];
    };


   this.addPoint = function(value, angle)
   {
      var isAvailable = false;

      angle -= this.config.MinAngle;

      var newDataPoint =
               {
                  value: value,
                  angle: angle
               };


      for(var i = 0; i < this.dataPoints.length; ++i)
      {
         if((this.dataPoints[i].angle - angle) < 5 && (this.dataPoints[i].angle - angle) > -5 )
         {
            isAvailable = true;
            this.dataPoints[i] = newDataPoint;
         }
      }

      if(!isAvailable)
      {
         this.dataPoints.push(newDataPoint);
		  //console.log("dataPoints.length: "+this.dataPoints.length);
      }

      //this.dataPoints.sort(this.sortPointsByAngle);
	  //console.log(this.dataPoints);
   };

   
   /*
    * Helper Functions
    */
	
	this.shuffle = function (array) {
	  var currentIndex = array.length, temporaryValue, randomIndex;

	  // While there remain elements to shuffle...
	  while (0 !== currentIndex) {

		// Pick a remaining element...
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex -= 1;

		// And swap it with the current element.
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	  }

	  return array;
	}	
	
   this.min = function( array )
   {
      return Math.min.apply( Math, array );
   };

   this.mergeChartConfig = function(defaults, userDefined)
   {
      var returnObj = {};

      for (var attrname in defaults) { returnObj[attrname] = defaults[attrname]; }

      for (var attrname in userDefined) { returnObj[attrname] = userDefined[attrname]; }

      return returnObj;
   };

   this.findMinMaxValue = function(data, pos)
   {
      var max = data[0][pos];
      var min = data[0][pos];

      for(var i = 0; i < data.length; ++i)
      {
         if(max < data[i][pos])
         {
            max = data[i][pos];
         }

         if(min > data[i][pos])
         {
            min = data[i][pos];
         }
      }

      return {"max": max, "min": min};
   };

   this.sortPointsByAngle = function(a, b)
   {
      return ((a.angle < b.angle) ? -1 : ((a.angle > b.angle) ? 1 : 0));
   };

   this.calculateDataPosition = function(dataPoint, value)
   {
      var minMax = this.findMinMaxValue(this.dataPoints, 'value');

      if(value)
      {
         var distance =  this.config.ValueScaleByMaxMin ? (value - minMax.min) * this.Scale : value * this.Scale;
      }
      else
      {
         var distance =  this.config.ValueScaleByMaxMin ? (dataPoint['value'] - minMax.min) * this.Scale : dataPoint['value'] * this.Scale;
      }

      var x = (this.width / 2) + distance * Math.cos((dataPoint.angle + this.config.MinAngle + this.config.AngleCorrection) / 180 * Math.PI);
      var y = (this.height / 2) + distance * Math.sin((dataPoint.angle + this.config.MinAngle + this.config.AngleCorrection) / 180 * Math.PI);

      return {x: x, y: y};
   };

}