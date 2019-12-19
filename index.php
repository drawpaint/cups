<?php
	session_start();
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
		if ($_POST["user_name"] == "gplink" ){
			$_SESSION["user_name"] = $_POST["user_name"];
			header("Location: dashboard.php");
			exit();			
		} else {
			header("Location: index.php");
		}
	}
?>

<html>
<head>
<title>CUPS 1.0</title>
<link rel="stylesheet" type="text/css" href="login.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>

    <div class="container">
        <form action="" method="post" id="frmLogin">
            <div class="demo-table">

                <div class="form-head">Login</div>
                <?php 
                if(isset($_SESSION["errorMessage"])) {
                ?>
                <div class="error-message"><?php  echo $_SESSION["errorMessage"]; ?></div>
                <?php 
                unset($_SESSION["errorMessage"]);
                } 
                ?>
                <div class="field-column">
                    <div>
                        <label for="username">Username</label><span id="user_info" class="error-info"></span>
                    </div>
                    <div>
                        <input name="user_name" id="user_name" type="text"
                            class="demo-input-box">
                    </div>
                </div>
                <div class="field-column">
                    <div>
                        <label for="password">Password</label><span id="password_info" class="error-info"></span>
                    </div>
                    <div>
                        <input name="pass_word" id="password" type="password"
                            class="demo-input-box">
                    </div>
                </div>
                <div class=field-column>
                    <div>
                        <input type="submit" name="login" value="Login"
                        class="btnLogin">
                    </div>
                    <!--div>
                        <button class="btnLogin btRegister" >Create Account</button>
                    </div-->					
                </div>
            </div>
        </form>
    </div>

<script>

</script>

</body>
</html>


