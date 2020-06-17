
<?php


/* login.php
 * 
 * Main webpage of user login.
 *  
 */


	// Include header and footer for the webpage
	include('head.php');
?>


<?php
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
				<br><br>
				<input type="submit" class="button" value="Login">
			</form>
          	<br>
          	<a href="login_register_main.php" ><button class="button">Register</button></a>
			<script type="text/javascript">
				function enter()
				{
					var username = document.getElementById("username").value;
					var password = document.getElementById("password").value;
					// Check if there is any blank in username or email
					if((/\s/.test(username))||username.length==0)
					{
						alert("Please check the format of your username (no blank).");
						return false;
					}
					if(password.length==0)
					{
						alert("Please check the format of your password.");
						return false;
					}
					if((/\s/.test(email))||email.length==0)
					{
						alert("Please check the format of your email (no blank).");
						return false;
					}
					return true;
			</script>
		</div>
<?php
	}

?>



<?php 
	include('foot.php');
?>
