<?php
	// Include header and footer for the webpage
	include('head.php');
	include('foot.php');

	// Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] > 0) // check if the code exist
	{
		echo "<script>window.location.href='index.php';</script>";
	}
	else // If the user has not logged in
	{
?>
		<div class="main">
			<form action="login_enter.php" method="post" onsubmit="return enter()">
				Username
				<input type="text" name="username" id="username"><br>
				Password
				<input type="password" name="password" id="password"><br>
				<input type="submit" class="button" value="Login">
				<input type="button" class="button" value="Register" onclick="register()">
			</form>
			<script type="text/javascript">
				function enter()
				{
					var username = document.getElementById("username").value;
					var password = document.getElementById("password").value;
					var regex=/^[/s]+$/; //Check if there is blank before or after the username
					if(regex.test(username)||username.length==0)
					{
						alert("Please check the format of your username (no blank before/after).");
						return false;
					}
					if(regex.test(password)||password.length==0)
					{
						alert("Please check the format of your password (no blank before/after).");
						return false;
					}
					return true;
				}
				function register()
				{
					window.location.href="login_register_main.php";
				}
			</script>
		</div>
<?php
	}

