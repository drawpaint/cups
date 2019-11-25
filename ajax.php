<?php
	//	DB details
	$servername = "172.16.2.54";
	$username = "gplink";
	$password = "GPL1nkCl!ent";

	
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'createspectrum':
                createspectrum();
                break;
			case 'createdb':
                createdb($_POST['dbname'],$_POST['bol']);
                break;
			case 'deletedb':
                deletedb($_POST['dbname']);
                break;
			case 'getsites':
                getsites();
                break;
            case 'insertsite':
                insertsite($_POST['dbname'],$_POST['sitename']);
                break;
			case 'insert':
                insert($_POST['dbname'],$_POST['ip'],$_POST['usr'],$_POST['pw'],$_POST['sh']);
                break;
			case 'pingIP':
                pingIP($_POST['ip']);
                break;				
            case 'deletedevice':
                deletedevice($_POST['ip'],$_POST['dbname']);
                break;
            case 'update':
                update($_POST['dbname'],$_POST['oldip'],$_POST['newip'],$_POST['usr'],$_POST['pw'],$_POST['sh'],$_POST['br'],$_POST['ag']);
                break;
			case 'showRadioList':
                showRadioList($_POST['dbname']);
                break;
			case 'shscan':
                shscan($_POST['dbname'],$_POST['ip'],$_POST['usr'],$_POST['pw'],$_POST['sh'],$_POST['br'],$_POST['ag']);
                break;
			case 'shscanAll':
                shscanAll($_POST['ip'],$_POST['dbname']);
                break;
			case 'sitesurvey':
                sitesurvey($_POST['ip'],$_POST['dbname']);
                break;				
			case 'getradiodetail':
                getradiodetail($_POST['dbname']);
                break;				
		}
    }

	
	function pingIP($i){
		// to send ping tests if the IP address is online
		exec("/bin/ping -c 1 -W 1 $i", $output, $status);
			if ($status == 0){
				echo $i." success ".$status;
			}else {
				echo $i." failed ". $status;
			}	
	}
	
	
	function createspectrum(){
		for ($x = 4800; $x <= 6200; $x+=5) {
			echo '<div class="tooltip" id="'.$x.'"><div class="tooltiptext"><div class="freqnum">'.$x.'</div></div></div>';
		}
	}
	
	function createtable($dbname){
		
		global 	$servername, $username, $password;			
		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		// sql to create table
		$sql = "CREATE TABLE iplist (
				ip INT(10) UNSIGNED PRIMARY KEY,
				usr VARCHAR(65) NOT NULL,
				pw VARCHAR(65) NOT NULL,
				sh INT(10)				
				)";
		if ($conn->query($sql) === TRUE) {
			echo "Table iplist created successfully";
		} else {
			echo "Error creating table: " . mysqli_error($conn);
		}
		// sql to create table
		$sql = "CREATE TABLE radiodetails (
				ip INT(10) UNSIGNED PRIMARY KEY,
				essid VARCHAR(20) NOT NULL,
				freq INT(10),
				centerFreq INT(10),
				txPower INT(10),
				chanbw INT(10),
				rsignal INT(10),
				chain0Signal INT(10),
				chain1Signal INT(10),
				noise INT(10),
				cinr INT(10),
				bearing DECIMAL(5,2),
				angle INT(3)
				)";
		if (mysqli_query($conn, $sql)) {
			echo "Table iplist created successfully";
		} else {
			echo "Error creating table: " . mysqli_error($conn);
		}	
		$conn->close();			
	}
	
	function createdb($dbname,$bol) {

		global 	$servername, $username, $password;
		
		// Create connection
		$conn = new mysqli($servername, $username, $password);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$db = strtolower($dbname);
		
		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS $db";
		if ($conn->query($sql) === TRUE) {
			echo "Database ".$dbname." created successfully";
			if ($bol <> 'false') {
				createtable(strtolower($dbname));				
			}else{
				// Create connection
				$conn2 = new mysqli($servername, $username, $password, $dbname);
				// Check connection
				if ($conn2->connect_error) {
					die("Connection failed: " . $conn2->connect_error);
				}				
				// create table
				$sql2 = "CREATE TABLE IF NOT EXISTS sitelist (
						id INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
						sitename VARCHAR(150) NOT NULL UNIQUE
						)";
				if ($conn2->query($sql2) === TRUE) {
					echo "sql2 success.";
				} else {
					echo "Error creating table: " . mysqli_error($conn2);
				}
				$conn2->close();	
			}
		} else {
			echo "Error creating database: " . $conn->error;
		}

		$conn->close();		
	}

	function deletedb($dbname) {

		global 	$servername, $username, $password;
		
		// Create connection
		$conn = new mysqli($servername, $username, $password);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$dbn = strtolower($dbname);
		// Drop database
		$sql = "DROP DATABASE $dbn";
		if ($conn->query($sql) === TRUE) {
					// Create connection
			$conn2 = new mysqli($servername, $username, $password, 'site_list');
			// Check connection
			if ($conn2->connect_error) {
				die("Connection failed: " . $conn2->connect_error);
			}
			$sql2 = "DELETE FROM sitelist WHERE sitename='$dbname'";
			if ($conn2->query($sql2) === TRUE) {
				echo "Record deleted successfully";
			} else {
				echo "Error deleting record: " . $conn2->error;
			}
			$conn2->close();
			
			echo "Site ".$dbname." was deleted successfully";

		} else {
			echo "Error deleting database: " . $conn->error;
		}

		$conn->close();		
	}
	
    function insertsite($dbname, $sitename) {
		
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername, $username, $password, $dbname);
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$sql = "INSERT INTO sitelist (sitename) VALUES ('$sitename')";
		
		if ($conn->query($sql) === TRUE) {
			echo "Add Success!";
		} else {
			if (mysqli_errno($conn) == 1062) {
				echo "Site name already exists.";
			}else{
				echo "DB insert error:".$sql."<br>";
			}
		}
		
        $conn->close();
    }
	
	function getsites(){
		
		global 	$servername, $username, $password;
		
		$dbname = "site_list";

		$conn = new mysqli($servername,$username,$password, $dbname);
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}		
		
		// Attempt select query execution
		$sql = "SELECT * FROM sitelist";
		if($result = mysqli_query($conn, $sql)){
			if(mysqli_num_rows($result) > 0){
			    //echo (mysqli_num_rows($result));

				$row = mysqli_fetch_all ($result, MYSQLI_ASSOC);
					//echo "console.log(".array_unique($row).")";
					//echo "console.log(".$row.")";					
					echo json_encode($row);
					//echo ($row);
				// Free result set

			} else{
				echo "No records found.";
			}
			mysqli_free_result($result);
		} else{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		}

		$conn->close();
	}	
	
	function getradiodetail($dbname){
		
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername,$username,$password,strtolower($dbname));
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}		
		
		// Attempt select query execution
		$sql = "SELECT * FROM radiodetails ORDER BY bearing";
		if($result = mysqli_query($conn, $sql)){
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_all ($result, MYSQLI_ASSOC);
					echo json_encode($row);
					//print_r($row );
				// Free result set
				mysqli_free_result($result);
			} else{
				echo "blank";
			}
		} else{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		}

		$conn->close();
	}
	
	function shscan($dbname,$i,$u,$p,$sh,$br,$ag) {

		// an ssh library 
		set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');

		// here we include it in our code
		include('phpseclib/Net/SSH2.php');
		
		// to send ping tests if the IP address is online
		function pingtest($i){
			exec("/bin/ping -c 1 -W 1 $i", $output, $status);
			if ($status == 0){
				return true;
			}
			return false;
		}
	
		if (pingtest($i)){
			
			// try connecting to an ip address
			$ssh = new Net_SSH2($i, $sh);
			if (!$ssh->login($u,$p)) {
				exit('Login Failed');
			}
			/* get current connection */
			$sshCurrent = $ssh->exec('mca-status');
			//	echo '<pre>';
			//	echo $sshCurrent;
			$arr = array_filter(explode(PHP_EOL,$sshCurrent));
			//	print_r($arr);
			
			// here we iterate each value of the array to create a multidimensional array
			$cells = array();
			foreach ($arr as $value) {
				$temparr = explode("=",$value);
				$cells[$temparr[0]] = $temparr[1];
			}
			//echo $cells['essid'];
			//echo $cells['afTxpowerEirp'];
			//print_r($cells);	
			
			global 	$servername, $username, $password;

			$conn = new mysqli($servername,$username,$password,strtolower($dbname));
			
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			//checks if AF5x
			if (array_key_exists("afOpmode",$cells)) {
				$essid = $cells['essid'];
				$freq = $cells['afTxfreq'];
				$chanbw = $cells['afTxchanbw'];
				$centerFreq = $cells['afTxfreq'];
				$txPower = $cells['afTxpower'];
				$rsignal = $cells['signal'];
				$chain0Signal = $cells['afRxpower0'];
				$chain1Signal = $cells['afRxpower1'];
				$noise = 0;
				$cinr = 0;			
			}else{
				$essid = $cells['essid'];
				$freq = $cells['freq'];
				$centerFreq = $cells['centerFreq'];
				$txPower = $cells['txPower'];
				$chanbw = $cells['chanbw'];
				$rsignal = $cells['signal'];
				$chain0Signal = $cells['chain0Signal'];
				$chain1Signal = $cells['chain1Signal'];
				$noise = $cells['noise'];
				$cinr = $cells['cinr'];
			}
			$sql = "REPLACE INTO radiodetails (ip, 
							essid, 
							freq, 
							centerFreq, 
							txPower, 
							chanbw, 
							rsignal, 
							chain0Signal, 
							chain1Signal, 
							noise, 
							cinr,
							bearing,
							angle) 
					VALUES (INET_ATON('$i'), 
							'$essid', 
							'$freq',
							'$centerFreq',
							'$txPower',
							'$chanbw',
							'$rsignal',
							'$chain0Signal',
							'$chain1Signal',
							'$noise',
							'$cinr',
							'$br',
							'$ag')";
			
			if ($conn->query($sql) === TRUE) {
				echo("Add successful!");	
			} else{ 
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
			} 
			  
			$conn->close(); 		
			exit;				
			
		} else {
			echo 'Ping to device with IP: '.$i.' failed.';
		}		
	}

	function shscanAll($i,$dbname) {

		// an ssh library 
		set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');

		// here we include it in our code
		include('phpseclib/Net/SSH2.php');
		
		// to send ping tests if the IP address is online
		function pingtest($ipadd){
			exec("/bin/ping -c 1 -W 1 $ipadd", $output, $status);
			if ($status == 0){
				return true;
			}
			return false;
		}
	
		if (pingtest($i)){

			global 	$servername, $username, $password;
			
			$conn = new mysqli($servername,$username,$password,strtolower($dbname));
			
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}		
			
			// Attempt select query execution
			$sql = "SELECT usr, pw, sh FROM iplist WHERE ip = INET_ATON('$i')";
			if($result = mysqli_query($conn, $sql)){
				if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_array($result)){
						$ip = long2ip(sprintf("%d", $row['ip']));
						$usr = $row['usr'];
						$pw = $row['pw'];
						$sh = $row['sh'];
						// try connecting to an ip address
						$ssh = new Net_SSH2($i, $sh);
						if (!$ssh->login($usr,$pw)) {
							exit('Login Failed');
						}
						/* get current connection */
						$sshCurrent = $ssh->exec('mca-status');
						//	echo '<pre>';
						//	echo $sshCurrent;
						$arr = array_filter(explode(PHP_EOL,$sshCurrent));
						$arry = array_filter(explode(",",$arr[0]));
						array_shift($arr);
						$mergearray = array_merge($arry, $arr);
							//print_r($mergearray);
							//print_r($arr);
						echo json_encode($mergearray);
					}	
					// Free result set
					mysqli_free_result($result);
				} else{
					echo "No records matching your query were found.";
				}
			} else {
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
			}
			$conn->close();		
		} else {
			echo 'Ping to device with IP: '.$i.' failed.';
		}
	}	

	function sitesurvey($i,$dbname) {

		// an ssh library 
		set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');

		// here we include it in our code
		include('phpseclib/Net/SSH2.php');
		
		// to send ping tests if the IP address is online
		function pingtest($ipadd){
			exec("/bin/ping -c 1 -W 1 $i", $output, $status);
			if ($status == 0){
				return true;
			}
			return false;
		}
	
		if (pingtest($i)){

			global 	$servername, $username, $password;
			
			$conn = new mysqli($servername,$username,$password,$dbname);
			
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}		
			
			// Attempt select query execution
			$sql = "SELECT usr, pw, sh FROM iplist WHERE ip = INET_ATON('$i')";
			if($result = mysqli_query($conn, $sql)){
				if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_array($result)){
						$ip = long2ip(sprintf("%d", $row['ip']));
						$usr = $row['usr'];
						$pw = $row['pw'];
						$sh = $row['sh'];
						// try connecting to an ip address
						$ssh = new Net_SSH2($i, $sh);
						if (!$ssh->login($usr,$pw)) {
							exit('Login Failed');
						}
						/* get current connection */
						$sshCurrent = $ssh->exec('iwlist ath0 scan | grep \'ESSID\|Frequency\'');
						$arry = preg_split('/\n|\r\n?/', $sshCurrent);
						echo json_encode($arry);
					}	
					// Free result set
					mysqli_free_result($result);
				} else{
					echo "No records matching your query were found.";
				}
			} else {
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
			}
			$conn->close();		
		} else {
			echo 'Ping to device with IP: '.$i.' failed.';
		}
	}
	
    function deletedevice($ipd, $dbname) {
	
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername,$username,$password,strtolower($dbname));
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}	
		$sql = "DELETE FROM iplist WHERE ip=INET_ATON('". $ipd ."')";
		
		if($conn->query($sql) === true){ 
			delfromradiodetails($ipd, $dbname);
			//showlist($dbname); 
		} else{ 
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		} 
		  
		$conn->close(); 		
        exit;
    }

    function delfromradiodetails($ipd, $dbname) {
	
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername,$username,$password,strtolower($dbname));
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}	
		$sql = "DELETE FROM radiodetails WHERE ip=INET_ATON('". $ipd ."')";
		
		if($conn->query($sql) === true){ 
			echo "Delete success.";
		} else{ 
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		} 
		  
		$conn->close(); 		
        exit;
    }
	
    function update($dbname,$o,$i,$u,$p,$sh,$br,$ag) {
		
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername,$username,$password,strtolower($dbname));
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}		
		
		//echo "<script>console.log('updating');</script>";
		
		$sql = "UPDATE iplist SET ip = INET_ATON('$i'), usr = '$u', pw = '$p', sh = '$sh' WHERE ip = INET_ATON('$i')";
	
		if($conn->query($sql) === true){
			$sql2 = "UPDATE radiodetails SET bearing = '$br', angle = '$ag' WHERE ip = INET_ATON('$i')";
			if($conn->query($sql2) === true){
				showRadioList($dbname); 
			}else{
				echo "ERROR: Could not able to execute $sql2. " . mysqli_error($conn). "dbname = ".$dbname;
			}
		} else{ 
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn). "dbname = ".$dbname;
		} 
		  
		$conn->close(); 		
        exit;
    }
	
    function insert($dbname,$i,$u,$p,$sh) {
		
		global 	$servername, $username, $password;

		// to send ping tests if the IP address is online
		function pingtest($ipadd){
			exec("/bin/ping -c 1 -W 1 $ipadd", $output, $status);
			if ($status == 0){
				return true;
			}else{
				return false;
			}
		}
	
		if (pingtest($i)){
			
			$conn = new mysqli($servername,$username,$password,strtolower($dbname));
			
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}		
			
			$sql = "INSERT INTO iplist (ip, usr, pw, sh) VALUES (INET_ATON('$i'), '$u', '$p', '$sh')";
			
			if ($conn->query($sql) === TRUE) {
				//showlist($dbname);	
				$conn->close();
		        exit;
			} else {
				
				if (mysqli_errno($conn) == 1062){
					echo "duplicate";
				}else{
					echo "db insertion error:".$sql."<br>";
				}
			}
		}else{
			echo 'Ping to device with IP: '.$i.' failed.';
		}
    }
	
	function showRadioList($dbname){
		
		global 	$servername, $username, $password;
		
		$conn = new mysqli($servername,$username,$password,strtolower($dbname));
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}		
		
		// Attempt select query execution
		$sql = "SELECT * FROM iplist";
		if($result = mysqli_query($conn, $sql)){
			if(mysqli_num_rows($result) > 0){
			 echo "<table class=\"customTable\">";
				echo "<tr>";
					echo "<th>IP</th>";
					echo "<th>Username</th>";
					echo "<th>Password</th>";
					echo "<th>SSH</th>";
					echo "<th>Bearing</th>";
					echo "<th>Angle</th>";					
					echo "<th>Action</th>";
				echo "</tr>";
				while($row = mysqli_fetch_array($result)){
					echo "<tr>";
						echo "<td>" . long2ip(sprintf("%d", $row['ip'])). "</td>";
						echo "<td>" . $row['usr']. "</td>";
						echo "<td><input type=\"password\" name=\"pwinput\" value=\"". $row['pw'] ."\" id=\"pwinput\" disabled></td>";
						echo "<td>" . $row['sh']. "</td>";
						$ip = $row['ip'];
						$sql2 = "SELECT bearing, angle FROM radiodetails WHERE ip = '$ip' ";
						if($result2 = mysqli_query($conn, $sql2)){
							if(mysqli_num_rows($result2) > 0){
								while($row2 = mysqli_fetch_array($result2)){
									echo "<td>" . $row2['bearing']. "</td>";
									echo "<td>" . $row2['angle']. "</td>";
								}
								mysqli_free_result($result2);
							}else{
								echo "No records found.";
							}
						}else{
							echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
						}
						echo "<td> 	
									<input type=\"submit\" class=\"button delip\" name=\"delete\" value=\"delete\" /> 
									<input type=\"submit\" class=\"button upip\" name=\"update\" value=\"update\" /> 
									<input type=\"submit\" class=\"button shscan\" name=\"scan\" value=\"scan\" />
							  </td>";
					echo "</tr>";
				}
			 echo "</table>";
				// Free result set
				mysqli_free_result($result);
			} else{
				echo "No records found.";
			}
		} else{
			echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
		}

		$conn->close();			
	}	
?>