
//Initialize MySql databases
function initilizeMySql() {
	
	var ajaxurl = 'ajax.php',
	data =  {'action': 'createdb', 'dbname': 'site_list', 'bol': false};
	$.post(ajaxurl, data, function (response) {
		//console.log(response);
	});	
	
}

// Appends sitename/dbname to urls of the left menu links
function updatenav() {
	$("a.navmenu").each(function() {
	   var $this = $(this);       
	   var _href = $this.attr("href");
	   //clear any variables attached to the url if there's any
	   if (_href.indexOf('?') > -1) {
			_href = _href.split('?')[0];
			$this.attr("href", _href + '?sname='+sname);
		}else{
			$this.attr("href", _href + '?sname='+sname);
		}
	});
}

function prependDropMenu($arr) {
	var jArr = $arr;
	//console.log(jArr.length);
	
	//populate the dropdown menu
	for (j = 0; j < jArr.length; j++) {
		var inn =  '<div  class="siteclass">' + jArr[j].sitename  +'</div>';
		$( "#droplist" ).prepend(inn);
		//assign the first database name to global variable sname if empty
		if (sname == ""){
			sname = jArr[j].sitename;
			$(".dropbtn").html(sname+' <i class="fa fa-caret-down"></i>');
		}else{
			$(".dropbtn").html(sname+' <i class="fa fa-caret-down"></i>');
		}
	} 
	appendDropMenu();
}

function appendDropMenu() {
	$( "#droplist" ).append('<div class="divider"></div>');
	$( "#droplist" ).append('<div class="addDelBut insertsite"> Add Site </div>');
	if (sname != ""){
		$( "#droplist" ).append('<div class="addDelBut deletesite"> Delete Site </div>');
	}
}

function showRadioList($el){
	//call another function showRadioList() in ajax.php to populate device list table
	//alert(sname);
	var ajaxurl = 'ajax.php',
	data =  {'action': 'showRadioList','dbname': sname};
	$.post(ajaxurl, data, function (response) {
		// Response div goes here.
		//console.log(response);
		if (response != "No records found."){
			$("#scanbut").show();
			$($el).html(response);				
		}else{
			$($el).html(response);
		}
	});		
}

//A function that creates and populates list of devices from a site/db
//call getsite() in ajax.php to populate table from mysql
function getsitesForm(){
	$("#deviceListTable").empty(); 	//initialize device list table
	$("#scanbut").hide();			//remove the scan all button
	$("#droplist").empty();			//initialize drop down menu values
	var ajaxurl = 'ajax.php',
	data =  {'action': 'getsites'};
	$.post(ajaxurl, data, function (response) {
		
		/*insert here a check if response is not empty null or invalid */
		try {
			//console.log(JSON.parse(response));
			
			prependDropMenu(JSON.parse(response));
			showRadioList('#deviceListTable');

		}catch(err){
		//	return false;
			appendDropMenu();
			console.log("no sites yet");
		}
	});	
	
	//Updates left page menu, adding php variable to the url
	updatenav();
}

//Calls php function to ssh into a radio and get details
function shscan($obj){

	$obj.val("");
	$obj.addClass("loading");
	
	var ip,us,pw,sh = '';
	
	var $row = $obj.closest("tr"),
		$tip = $row.find("td:nth-child(1)"),
		$tus = $row.find("td:nth-child(2)"),
		$tpw = $row.find("td:nth-child(3)"),
		$tsh = $row.find("td:nth-child(4)");
				
		console.log('bearing ='+ bearing);
		console.log('angle ='+ angle);

	$.each($tip, function() {
		ip = $(this).text();
		console.log('ip: '+ip);
	}); 
	$.each($tus, function() {
		us = $(this).text();
		console.log('ip: '+us);
	});
	$.each($tpw, function() {
		pw = $(this).children('input[name="pwinput"]')[0].value;
		console.log('pw: '+ pw);
	});
	$.each($tsh, function() {
		sh = $(this).text();
		console.log('ip: '+sh);
	});
	
	
	/*
	$.each($tbr, function() {
		br = $(this).text();
		//console.log('ip: '+sh);
	});
	$.each($tag, function() {
		ag = $(this).text();
		//console.log('ip: '+sh);
	});
	*/
	//console.log(sname);
	var ajaxurl = 'ajax.php',
	data =  {'action': 'shscan', 'dbname': sname, 'ip': ip, 'usr': us, 'pw': pw, 'sh': sh, 'br': bearing, 'ag': angle};
	$.post(ajaxurl, data, function (response) {
		// Response div goes here.
		console.log(response);
		$obj.removeClass("loading");
		$obj.val("scan");
		alert(response);
	});		
}

function scanlastaddedrow($ip,$pw){
	var $latestrow = $("tr:contains('"+$ip+"')"),
		$lasttd = $latestrow.find("td:nth-child(5)");
	var	$shbutton = $lasttd.find(".shscan");

//	$.each($lasttd, function() {
//		sh = $(this).val();
//		console.log('ip: '+sh);
//		});	

	shscan($shbutton);
}

function fillRadioDetails($ip,$us,$pw,$sh){
	console.log("fill: "+sname+":"+$ip+":"+$us+":"+$pw+":"+$sh);
	var ajaxurl = 'ajax.php',
	data =  {'action': 'shscan', 'dbname': sname, 'ip': $ip, 'usr': $us, 'pw': $pw, 'sh': $sh, 'br': bearing, 'ag': angle};
	$.post(ajaxurl, data, function (response) {
		//console.log(response);
		getsitesForm();
		alert(response);
	});		
}

function validateFields(){
	errmessage = "";
	var ipadd = document.querySelector('[name="ipadd"]').value;
	var usr = document.querySelector('[name="usrname"]').value;
	var pw = document.querySelector('[name="passwrd"]').value;
	var sh = document.querySelector('[name="sshport"]').value;	
	var br = document.querySelector('[name="rbearing"]').value;	
	var ag = document.querySelector('[name="rangle"]').value;	
	
	if (!(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipadd))) {  
		errmessage = "Please enter a valid IP address.";
	}
	
	if (usr == ""){
		errmessage = "Please enter a username.";
	}
	
	if (pw == ""){
		errmessage = "Please enter a password.";
	}
	
	if ( sh == "" || isNaN(sh) ){
		errmessage = "Please enter a valid SSH number.";
	}
	
	if ( br == "" || isNaN(br) ){
		errmessage = "Please enter a valid bearing value.";
	}
	
	if ( ag == "" || isNaN(ag) ){
		errmessage = "Please enter a valid angle value.";
	}	
	
	if (errmessage == ""){
		return true;
	}else{
		return false;
	}
}

function createspectrum() {
	var ajaxurl = 'ajax.php',
	data =  {'action': 'createspectrum'};
		$.post(ajaxurl, data, function (response) {
			// Response div goes here.
			//console.log(response);
			$('.frequencybar').html(response);
		});	
}

function long2ip (proper_address) {
    // http://jsphp.co/jsphp/fn/view/long2ip
    // +   original by: Waldo Malqui Silva
    // *     example 1: long2ip( 3221234342 );
    // *     returns 1: '192.0.34.166'
    var output = false;

    if (!isNaN(proper_address) && (proper_address >= 0 || proper_address <= 4294967295)) {
        output = Math.floor(proper_address / Math.pow(256, 3)) + '.' + Math.floor((proper_address % Math.pow(256, 3)) / Math.pow(256, 2)) + '.' + Math.floor(((proper_address % Math.pow(256, 3)) % Math.pow(256, 2)) / Math.pow(256, 1)) + '.' + Math.floor((((proper_address % Math.pow(256, 3)) % Math.pow(256, 2)) % Math.pow(256, 1)) / Math.pow(256, 0));
    }

    return output;
}


function paintspectrum(dbname){
	
	//initialize the spectrum field and devicelist div
	$("#divstat1").empty();
	$(".frequencybar").empty();
	createspectrum();
	arrRadio = [];
	colornum = 0;
	//console.log(dbname);
	var ajaxurl = 'ajax.php',
	data =  {'action': 'getradiodetail', 'dbname': dbname};
	$.post(ajaxurl, data, function (response) {
		//console.log(response);
		if (response == "blank"){
			$("#divstat1").html('<h4>No record found.</h4>');
			$("#radionum").text(0);
		}else{
			//convert object to array
			var jsonresponse = JSON.parse(response);
			//console.log(jsonresponse);
			//get number of radios
			$("#radionum").text(jsonresponse.length);
			
		//iterates every array and color the spectrum
			for (j = 0; j < jsonresponse.length; j++) {
				var ip = long2ip(jsonresponse[j].ip);
				var centerFreq = parseInt(jsonresponse[j].centerFreq);
				var freq = parseInt(jsonresponse[j].freq);
				var chanbw = parseInt(jsonresponse[j].chanbw);
				var essid = jsonresponse[j].essid;				
				var freqlow = centerFreq - (chanbw/2);
				var freqhigh = centerFreq + (chanbw/2);
				//paint the spectrum
				for (i = freqlow; i <= freqhigh; i += 5) {
					//var el = document.getElementById(i);
					var oldcontent = $('#'+i+' div.tooltiptext').html();
					$('#'+i+' div.tooltiptext').append('<div class="ssid">'+essid+' <br>( b: ' +jsonresponse[j].bearing+ '&#176; a: ' +jsonresponse[j].angle+ '&#176; )</div>'); 
					if ($("#"+i+"").hasClass("redchannel")) {
						$("#"+i+"").removeClass("redchannel");
						$("#"+i+"").addClass("pinkchannel");
					}else{
						$("#"+i+"").addClass("redchannel");						
					}
					$('#'+i+' div.tooltiptext').append('<input class="hidden hbearing" style="display: none;" value="' + jsonresponse[j].bearing + '">');
					$('#'+i+' div.tooltiptext').append('<input class="hidden hangle" style="display: none;" value="' + jsonresponse[j].angle + '">');
					$('#'+i+' div.tooltiptext').append('<input class="hidden hcolornum" style="display: none;" value="' + colornum + '">');
				};	
				
				//console.log(jsonresponse[j].angle);
				arrRadio[j] = {};
				arrRadio[j]["bearing"] = jsonresponse[j].bearing;
				arrRadio[j]["angle"] = jsonresponse[j].angle;
				arrRadio[j]["colornum"] = colornum;

				colornum++;
				if (colornum == 8){
					colornum = 0;
				}
				
				//create collapsible table list of radio
				var btn = document.createElement("BUTTON");
				btn.type = "button";
				btn.classList.add('collapsible');
				btn.innerHTML = jsonresponse[j].essid;                  
				document.getElementById("divstat1").appendChild(btn);
				
				var dv = document.createElement("DIV");
				dv.classList.add('content');
				var radiocontent = 	'<ul style="list-style-type:none">'+
									'<li>IP: <a target="_blank" href="http://' + ip +'">' + ip +'</a></li>'+
									'<li>Frequency: ' + freqlow + ' - ' + freqhigh +'</li>'+
									'<li>Center Frequency: ' + jsonresponse[j].centerFreq + '</li>'+
									'<li>TxPower: ' + jsonresponse[j].txPower + '</li>'+
									'<li>Channel Width: ' + jsonresponse[j].chanbw + '</li>'+
									'<li>Signal: ' + jsonresponse[j].rsignal + '</li>'+
									'<li>Chain0 Signal: ' + jsonresponse[j].chain0Signal + '</li>'+
									'<li>Chain1 Signal: ' + jsonresponse[j].chain1Signal + '</li>'+
									'<li>Noise: ' + jsonresponse[j].noise + '</li>'+
									'<li>CINR: ' + jsonresponse[j].cinr + '</li>'+
									'<li>Bearing: ' + jsonresponse[j].bearing + '</li>'+
									'<li>Antenna angle: ' + jsonresponse[j].angle + '</li>'+
									'</ul>'+
									'<input type="submit" class="moreinfo" name="'+ip+'" value="more info" />'+
									'<input type="submit" class="sitesurvey" name="'+ip+'" value="site survey" />';
				dv.innerHTML = radiocontent;
				document.getElementById("divstat1").appendChild(dv);				
			}
			//console.log(arrRadio);
			mapObject.drawDiagram();
			for (i = 0; i < arrRadio.length; i++) {
				paintradial(arrRadio[i].bearing,arrRadio[i].angle,colors[arrRadio[i].colornum]);
				//console.log(arrRadio[i].bearing);
			}	
			
		}
		var coll = document.getElementsByClassName("collapsible");
		var i;

		for (i = 0; i < coll.length; i++) {
		  coll[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var content = this.nextElementSibling;
			if (content.style.display === "block") {
			  content.style.display = "none";
			  $("#divstat2").empty();
			} else {
			  content.style.display = "block";
			}
		  });
		} 
	});	
}

// radial graph 
function paintradial(rBearing,rAngle,rcolor){
	
	var point1 = rBearing - (rAngle / 2); // 296 = 356 - (120/2 = 60)
	//console.log('point1: '+point1);
	var point2 = (rBearing*1) + (rAngle / 2); // 416 = 356 + (120/2 = 60)	
	//console.log('point2: '+point2);
	if (point2 > 360) {
		point2 = point2 - 360;
	}
	//console.log('2nd point2: '+point2);
	mapObject.addPoint(100, point1);
	mapObject.addPoint(100, rBearing);
	mapObject.addPoint(100, point2);
  
	mapObject.drawFreq(rcolor);	  
}

function deletesite(){
	//console.log("delete li clicked.");
	// Dialog here
	$("#deldialog").dialog("open");

	$("#delselect").empty();
	
	//Get site list
	var ajaxurl = 'ajax.php',
	data =  {'action': 'getsites'};
	$.post(ajaxurl, data, function (response) {
		try{
		var jres = JSON.parse(response);
		//populate the select option
		for (j = 0; j < jres.length; j++) {
			var site =  '<option value="' + jres[j].sitename  +'">' + jres[j].sitename  +'</option>';
			$( "#delselect" ).append(site);
			}
		}catch(err){
			console.log("no sites yet");
		}
	
	$("#deldialog").on("click",'#delsubmit', function(){
		var name = $("#delselect").val();
		console.log(name);
		var ajaxurl = 'ajax.php',
		data =  {'action': 'deletedb', 'dbname': name};
		$.post(ajaxurl, data, function (response) {
			//alert(response);
			//reset global sname variable, prevent error in getsites() incase the just deleted site was assined to sname
			sname = ""; 
			$("#deldialog").dialog("close");
			//populate and refresh elements	
			location.reload();			
			getsitesForm();
		});

	});

	});	

}