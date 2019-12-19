<?php
 session_start();
 if ( isset( $_SESSION['user_name'] ) ) {
    // Grab user data from the database using the user_id
    // Let them access the "logged in only" pages

 include 'head.php';

?>
  

    <!-- -->
		<div class="spectrumfield">
			<h1>SPECTRUM</h1>
			<div class="frequencyregion">
				<div class="frequencybar">
				  <?php
					include_once('ajax.php');
					createspectrum();	
				  ?>
				</div>
			
			</div>
		</div>

		<?php
		 include 'dialog.php';
		?>
		
		<div id="graphregion">
			<canvas id="radialgraph" width="400" height="300"></canvas>

		</div>
		
		<div id="stats">
			<div id="divflexheader" class="statscolumn">
				<div class="textGray1">Total Radio: <span id="radionum"></span></div>
			</div>
			<div id="divflex">
				<div id="divstat1" class="statscolumn"><br style="clear: both"></div>
				<div id="divstat2" class="statscolumn"><br style="clear: both"></div>			
			</div>
			
		</div>

</div>

<?php
 include 'foot.php';
?>
  

<script>

var sname = $('#hiddenvariable').val();
var arrRadio = [];
var colors =[
			"rgba(120,177,232,0.5)",
			"rgba(120,232,128,0.5)",
			"rgba(232,139,120,0.5)",
			"rgba(232,120,161,0.5)",
			"rgba(272,120,232,0.5)",
			"rgba(145,120,232,0.5)",
			"rgba(120,232,232,0.5)",
			"rgba(120,232,174,0.5)"
			];
var colornum = 0;

var mapObject;	
	
// Display table list of devices in mysql
$(document).ready(function (){
	
	initilizeMySql();
	
	//Assigns a site/db name to dropdown button
	if (sname != ""){
		$(".dropbtn").html(sname+' <i class="fa fa-caret-down"></i>');
	}else{
		$(".dropbtn").html('Sites <i class="fa fa-caret-down"></i>');
	}
	
	getsitesIndex();
	
	$(function() {
		$("#dialog").dialog({
			modal: true,
			autoOpen: false
		});
		$("#deldialog").dialog({
			modal: true,
			autoOpen: false
		});		
	});	

	//draw radial chart

	mapObject = new RadarChart(document.getElementById('radialgraph').getContext('2d'));
	mapObject.init({
	// OPTIONS HERE
	});
	mapObject.drawDiagram();	
	//alert("Main");
});

function getsitesIndex(){
	$("#droplist").empty();
	var ajaxurl = 'ajax.php',
	data =  {'action': 'getsites'};
	$.post(ajaxurl, data, function (response) {
		try {
			prependDropMenu(JSON.parse(response));
			paintspectrum(sname);
		}catch(err){
			appendDropMenu();
			console.log("no sites yet");
		}
	});
	//Updates left page menu, adding php variable to the url
	updatenav();
}


$(document).on('mouseenter','.redchannel', function (event) {
	var hbearing = $(this).find(".hbearing").val();
	var hangle = $(this).find(".hangle").val();
	var hcolornum = $(this).find(".hcolornum").val();	
	
	mapObject.drawDiagram();
	paintradial(hbearing,hangle,colors[hcolornum]);

}).on('mouseenter','.pinkchannel', function (event) {

	var hbearing = $(this).find(".hbearing");
	var hangle = $(this).find(".hangle");
	var hcolornum = $(this).find(".hcolornum")
	var arrBearing = [];
	var arrAngle = [];
	var arrColor = [];
	$.each(hbearing, function() {
		arrBearing.push($(this).val());
	}); 
	$.each(hangle, function() {
		arrAngle.push($(this).val());
	});
	$.each(hcolornum, function() {
		arrColor.push($(this).val());
	});
	
	mapObject.drawDiagram();
	for (j = 0; j < arrBearing.length; j++) {
		paintradial(arrBearing[j],arrAngle[j],colors[arrColor[j]]);
		//console.log(arrBearing[j] +' : '+ arrAngle[j]);
	}
	
}).on('mouseleave','.pinkchannel',  function(){
	mapObject.drawDiagram();
	for (j = 0; j < arrRadio.length; j++) {
		paintradial(arrRadio[j].bearing,arrRadio[j].angle,colors[arrRadio[j].colornum]);
		//console.log(colors[arrRadio[j].hcolornum]);
	}
}).on('mouseleave','.redchannel',  function(){
	mapObject.drawDiagram();	
	for (j = 0; j < arrRadio.length; j++) {
		paintradial(arrRadio[j].bearing,arrRadio[j].angle,colors[arrRadio[j].colornum]);
		//console.log(arrRadio[j].color);		
	}
});

// Choosing a site from dropdown menu
$("#droplist").on("click",'.siteclass', function() {
	var n = $(this).text();
	//alert(n);
	$(".dropbtn").html(n+' <i class="fa fa-caret-down"></i>');
	//console.log(n);
	sname = n;
	updatenav();
	paintspectrum(n);
	mapObject.drawDiagram();
	//location.reload();
});	

// SSH to radio and get ALL details from 'mca-status' command
$("#divstat1").on("click",'.moreinfo', function() {
	$(".sitesurvey").removeClass("active");
	$("#divstat2").empty();	
	$("#divstat2").addClass("loading2");
	$(this).addClass("active");
	var ip = $(this).attr("name");
	//console.log(ip);
	console.log(sname);
	var ajaxurl = 'ajax.php',
	data =  {'action': 'shscanAll', 'ip': ip, 'dbname': sname};
		$.post(ajaxurl, data, function (response) {
			// Response div goes here.
			console.log(response);
			var jsonres = JSON.parse(response);
			console.log(jsonres.length);
			console.log(typeof(response));
			var radiodata = "";
			for (i = 0; i < jsonres.length; i++) {
				radiodata += '<div>'+jsonres[i]+'</div>';
			}
			$('#divstat2').html(radiodata);
			$("#divstat2").removeClass("loading2");
		});
});	

// SSH to radio and do a site survey using 'iwlist ath0 scan' command
$("#divstat1").on("click",'.sitesurvey', function() {
	$(this).addClass("active");
	$("#divstat2").empty();
	$("#divstat2").addClass("loading2");	
	var ip = $(this).attr("name");
	//console.log(ip);
	//console.log(sname);
	$(".moreinfo").removeClass("active");
	var ajaxurl = 'ajax.php',
	data =  {'action': 'sitesurvey', 'ip': ip, 'dbname': sname};
		$.post(ajaxurl, data, function (response) {
			// Response div goes here.
			console.log(response);
			var jsonres = JSON.parse(response);
			//console.log(jsonres.length);
			//console.log(typeof(response));
			var radiodata = "";
			for (i = 0; i < jsonres.length-2; i+=2) {
				radiodata += '<div class=\"surveydiv\"><ul><li>'+jsonres[i]+'</li><li>'+jsonres[i+1]+'</li></ul></div>';
			}
			$('#divstat2').html(radiodata);
			$("#divstat2").removeClass("loading2");	
		});
});	

//Add a new site
$("#droplist").on("click",'.insertsite', function() {

	// Dialog here
	$("#dialog").dialog("open");
	
	$("#dialog").on("click",'#submit', function(){
		var name = $("#name").val();
		storeData(name);
	});

	function storeData(name) {
		
		// insert the new site name into site_list db into sitelist table
		var ajaxurl = 'ajax.php',
		data =  {'action': 'insertsite', 'dbname': 'site_list', 'sitename': name};
		$.post(ajaxurl, data, function (response) {
			alert(response);
			//$("#droplist").empty();
			console.log(name);
			sname = name;
			//getsites();
		});
		
		// creates a new database for this site		
		var ajaxurl = 'ajax.php',
		data =  {'action': 'createdb', 'dbname': name, 'bol': true};
		$.post(ajaxurl, data, function (response) {
			//alert(response);
		});
	}

});

//Deleting a site - triggered by .deletesite on click
$("#droplist").on("click",'.deletesite', function() {
	deletesite();
});

//Deleting a site - triggered by .deletesite on click
$(".logout").on("click",'.navmenu', function() {
		// creates a new database for this site		
		var ajaxurl = 'ajax.php',
		data =  {'action': 'sessiondestroy'};
		$.post(ajaxurl, data, function (response) {
			console.log(response);
			//location.reload();
		});
});

</script>

</body>

</html>
<?php
} else {
    // Redirect them to the login page
    header("Location: http://172.16.2.5/index.php");
}
?>