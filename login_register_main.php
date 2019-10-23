<?php include("head.php"); ?>

<!------------------------------
	User register webpage.
-------------------------------->

<div class="main">
	<form action="login_register.php" method="post" onsubmit="return register()">
		Username
		<input type="text" name="username" id="username"><br>
		<br>Password
		<input type="password" name="password" id="password"><br>
		<br>Confirm password
		<input type="password" name="password_confirm" id="password_confirm"><br>
		<br>Name
		<input type="text" name="name" id="name"><br>
		<br>Email
		<input type="text" name="email" id="email"><br>
		<br>
		<input type="submit" value="Register">
	</form>
	<script type="text/javascript">
		function register()
		{
			var username = document.getElementById("username").value;
			var password = document.getElementById("password").value;
			var password_confirm = document.getElementById("password_confirm").value;
			var email = document.getElementById("email").value;
			
			var name = document.getElementById("name").value;
			var email = document.getElementById("email").value;
			
			if(password_confirm != password)
			{
				alert("Please confirm the password.");
				return false;
			}
			
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
			if(regex.test(email)||email.length==0)
			{
				alert("Please check the format of your email (no blank before/after).");
				return false;
			}
			return true;
		}
	</script>
</div>

<?php include "foot.php"; ?>
