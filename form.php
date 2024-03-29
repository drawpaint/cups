<?php

 session_start();
 if ( isset( $_SESSION['user_name'] ) ) {
    // Grab user data from the database using the user_id
    // Let them access the "logged in only" pages

 include 'head.php';

?>
  
	<div class="container-fluid">

		<div class="jumbotron">
		
			<div class="uform">
			
				<h3>Here you add devices</h3>
				
				<div class="dform">

					<div id="wrapper">
					
						
						<div id="col1">
						  <label class="description col1"> IP Address </label>
						  <input class="inputfield col1" type="text" name="ipadd" value="" placeholder="e.g. 10.10.1.2">
						  <!--input class="pingbutton" type="submit" name="submit" id="pingbutton" value="ping"-->
						  <br><br>
						  <label class="description"> Username </label>
						  <input class="inputfield" type="text" name="usrname" value="" placeholder="e.g. username">
						  <span class="error"> <?php echo $usrErr;?></span>
  						  <br><br>
						  <label class="description"> Password </label>
						  <input class="inputfield" type="password" name="passwrd" value="" placeholder="e.g. pass">
						  <span class="error"><?php echo $passErr;?></span>
					  </div>
						
						<div id="col2">				
						  <label class="description"> SSH Port </label>
						  <input class="inputfield" type="number" name="sshport" value="22" placeholder="22">
						  <span class="error"> <?php echo $shErr;?></span>
						  <br><br>
						  <label class="description"> Bearing </label>
						  <input class="inputfield" type="number" name="rbearing" value="" placeholder="e.g. 45.33">
						  <span class="error"><?php echo $brErr;?></span>
						  <br><br>
						  <label class="description"> Angle </label>
						  <input class="inputfield" type="number" name="rangle" value="" placeholder="e.g. 120">
						  <span class="error"> <?php echo $agErr;?></span>
						</div>
						
						<table class="">
							<tr>
								<td>
							</tr>
						</table>
						
					</div>
						<br><br>
						<input class="insertbutton" type="submit" name="submit" id="subbut" value="Add Radio">  
			
				</div>
				
				<?php
				 include 'dialog.php';
				?>
								
				<div id="deviceList" >
					
					<h3> Device List </h3>
					<!--input class="insertbutton" type="submit" name="submit" id="scanbut" value="Scan All"--> 
					
					<div id="deviceListTable" >
					</div>
					
				</div>

			</div>
		</div>
	</div>
</div> 

<?php
 include 'foot.php';
?>
  
<script> 

//global variables
var errmessage,oldip,bearing,angle = '';
var sname = $('#hiddenvariable').val();

// Display table list of devices in mysql
$(document).ready(function (){
	
	initilizeMySql();
	
	//Assigns a site/db name to dropdown button
	if (sname != ""){
		$(".dropbtn").html(sname+' <i class="fa fa-caret-down"></i>');
	}else{
		$(".dropbtn").html('Sites <i class="fa fa-caret-down"></i>');
	}

	//A function that creates and populates list of devices from a site/db
	getsitesForm();

	//Initialize dialog divs
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
});

//Add a new site
$("#droplist").on("click",'.insertsite', function() {

	// Opens the Dialog form in modal
	$("#dialog").dialog("open");
	
	$("#dialog").on("click",'#submit', function(){
		var name = $("#name").val();
		//alert(name);
		storeData(name);
	});

	function storeData(name) {
		//alert(name);
		var ajaxurl = 'ajax.php',
		data =  {'action': 'insertsite', 'dbname': 'site_list', 'sitename': name};
		$.post(ajaxurl, data, function (response) {
			//console.log(name);
			//console.log(response);
			sname = name;
			//alert(response);
			getsitesForm();
		});
		
		var ajaxurl = 'ajax.php',
		data =  {'action': 'createdb', 'dbname': name, 'bol': true};
		$.post(ajaxurl, data, function (response) {
			alert(response);
		});
					
	}

});

// Choosing a site from dropdown menu
$("#droplist").on("click",'.siteclass', function() {
	var n = $(this).text();
	$(".dropbtn").html(n+' <i class="fa fa-caret-down"></i>');
	//Assigns the selected sitename to the global var sname
	sname = n;
	//A function that creates and populates list of devices from a site/db
	getsitesForm();
});

//Deleting a site - triggered by .deletesite on click
$("#droplist").on("click",'.deletesite', function() {
	deletesite();
});


// SSH to ALL radio and get details from 'mca-status' command
	$("#deviceList").on("click",'#scanbut', function() {
		var table = document.getElementsByClassName("customTable");
		var t = table[0];
		
		//Gets the IP of each row
		for(var i = 1; i < t.rows.length; i++){
			var ip = t.rows[i].cells[0].innerHTML;
			console.log(ip);
			scanlastaddedrow(ip);
		}

	});

// Insert radio details to mysql
	$(".dform").on("click",'.pingbutton', function() {
		
		var ipadd = document.querySelector('[name="ipadd"]').value;
		
		var ajaxurl = 'ajax.php',
		data =  {'action': 'pingIP','ip': ipadd};
			$.post(ajaxurl, data, function (response) {
				// Response div goes here.
					alert(response);
			});	


	});

// Insert radio details to mysql
	$(".dform").on("click",'.insertbutton', function() {
		
		var ipadd = document.querySelector('[name="ipadd"]').value;
		var usr = document.querySelector('[name="usrname"]').value;
		var pw = document.querySelector('[name="passwrd"]').value;
		var sh = document.querySelector('[name="sshport"]').value;	
		var br = document.querySelector('[name="rbearing"]').value;	
		var ag = document.querySelector('[name="rangle"]').value;			
		var btvalue = document.querySelector('[name="submit"]').value; 
		console.log(sname+":"+ipadd+":"+usr+":"+pw+":"+sh+":"+br+":"+btvalue);
		//alert(btvalue);
		//Assigns data to global variables
		bearing = br;
		angle = ag;
		
		if (validateFields()) {
		
			if (btvalue == "Add Radio") {
				var ajaxurl = 'ajax.php',
				data =  {'action': 'insert', 'dbname': sname, 'ip': ipadd, 'usr': usr, 'pw': pw, 'sh': sh};
					$.post(ajaxurl, data, function (response) {
						// Response div goes here.
						if (response == "duplicate") {
							alert("This IP address is already in database.");
						} else {
							fillRadioDetails(ipadd,usr,pw,sh);
						}
					});	
			}
			
			if (btvalue == "Update") {
				//console.log('btvalue: '+btvalue);
				var ajaxurl = 'ajax.php',
				data =  {'action': 'update', 'dbname': sname, 'oldip': oldip, 'newip': ipadd, 'usr': usr, 'pw': pw, 'sh': sh, 'br': br, 'ag': ag};
					$.post(ajaxurl, data, function (response) {
						// Response div goes here.
						$('#deviceListTable').html(response);
						alert("Update successful!");
					});	
				$("#subbut").prop('value', 'Add Radio');
			}
		
		}else{
			alert(errmessage);
		}

	});
   
   // Delete radio details in mysql
	$("#deviceListTable").on("click",'.delip', function() {
		var ipDelete = '';
		var $row = $(this).closest("tr"),
			$tip =  $row.find("td:nth-child(1)");
		$.each($tip, function() {
			$(this).find("input").each(function() {
				ipDelete = this.value;
				//console.log('ip: '+ipDelete);
			});
		}); 
			//console.log('db: '+sname);
		var ajaxurl = 'ajax.php',
		data =  {'action': 'deletedevice', 'ip': ipDelete, 'dbname': sname};
		$.post(ajaxurl, data, function (response) {
			getsitesForm();
			//$('#deviceListTable').html(response);
		});		
	});

// Update radio details in mysql
	$("#deviceListTable").on("click",'.upip', function() {
		
		//disables this button
		this.disabled = true;
		//Change the value of the Add Radio button to Upate
		$("#subbut").prop('value', 'Update');
		
		//Gets the value from this row and assigns them to the input fields above
		var $row = $(this).closest("tr"),
			$tip = $row.find("td:nth-child(1)"),
			$tus = $row.find("td:nth-child(2)"),
			$tpw = $row.find("td:nth-child(3)"),
			$tsh = $row.find("td:nth-child(4)"),
			$tbr = $row.find("td:nth-child(5)"),
			$tag = $row.find("td:nth-child(6)");
			
		$.each($tip, function() {
			$(this).find("input").each(function() {
				var ipMod = this.value;
				document.querySelector("input[name=ipadd]").value = ipMod;
				oldip = ipMod;
				//console.log('ip: '+ipMod);
			});
		}); 
		$.each($tus, function() {
			$(this).find("input").each(function() {
				usMod = this.value;
				document.querySelector("input[name=usrname]").value = usMod;
				//console.log('ip: '+usMod);
			});
		});
		$.each($tpw, function() {
			$(this).find("input").each(function() {
				var newpass = this.value;
				document.getElementsByName("passwrd")[0].value = newpass;
				//console.log('pw: '+newpass);
			});
		});
		$.each($tsh, function() {
			$(this).find("input").each(function() {
				shMod = this.value;
				document.querySelector("input[name=sshport]").value = shMod;			
				//console.log('sh: '+shMod);
			});
		});
		$.each($tbr, function() {
			$(this).find("input").each(function() {
				brMod = this.value;
				document.querySelector('input[name=rbearing]').value = brMod;
				//console.log('br: '+brMod);
			});
		});
		$.each($tag, function() {
			$(this).find("input").each(function() {			
				agMod = this.value;
				document.querySelector('input[name=rangle]').value = agMod;			
				//console.log('ag: '+agMod);
			});	
		});		
	});	

// SSH to radio and get details from 'mca-status' command
	$("#deviceListTable").on("click",'.shscan', function() {
		//console.log($(this));
		//shscan($(this));
		
	var $obj = $(this);
	$obj.addClass("loading");
	
	var ip,us,pw,sh,br,ag = '';
	
	//Gets the value from this row and assigns them to the input fields above
	var $row = $(this).closest("tr"),
		$tip = $row.find("td:nth-child(1)"),
		$tus = $row.find("td:nth-child(2)"),
		$tpw = $row.find("td:nth-child(3)"),
		$tsh = $row.find("td:nth-child(4)"),
		$tbr = $row.find("td:nth-child(5)"),
		$tag = $row.find("td:nth-child(6)");
		//console.log($tip);	
		
		$.each($tip, function() {
			$(this).find("input").each(function() {
				ip = this.value;
				//console.log('ip: '+ip);
			});
		}); 
		$.each($tus, function() {
			$(this).find("input").each(function() {
				us = this.value;
				//console.log('ip: '+us);
			});
		});
		$.each($tpw, function() {
			$(this).find("input").each(function() {
				pw = this.value;
				//console.log('pw: '+pw);
			});
		});
		$.each($tsh, function() {
			$(this).find("input").each(function() {
				sh = this.value;
				//console.log('sh: '+sh);
			});
		});
		$.each($tbr, function() {
			$(this).find("input").each(function() {
				br = this.value;
				//console.log('br: '+br);
			});
		});
		$.each($tag, function() {
			$(this).find("input").each(function() {			
				ag = this.value;
				//console.log('ag: '+ag);
			});	
		});
	
		//console.log(sname);
		var ajaxurl = 'ajax.php',
		data =  {'action': 'shscan', 'dbname': sname, 'ip': ip, 'usr': us, 'pw': pw, 'sh': sh, 'br': br, 'ag': ag};
		$.post(ajaxurl, data, function (response) {
			// Response div goes here.
			//console.log(response);
			//$obj.val("scan");
			alert(response);
			$obj.removeClass("loading");
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