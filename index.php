<?php include("head.php"); ?>

<div class="main">
	<div class="placeholder_introduction" >
		<p>Some nice sentences about the database...</p>
		<!---
		<div style="font-size: 18px; font-family:Arial; margin-top:200px;">
			<p>Database Statistics</p>
			<p>
				(...compounds, ...spectrum,...)
			</p>
		
		</div>
		--->
	</div>
	<div class="placeholder_search">
		<div class="search_container_main">
			<form action="/search_data.php" method="GET">
				<input type="text" placeholder="Try a molecule..." name="query" style="font-size: 18px; font-family:Arial;">
				<button type="submit" class="button">Search</button>
			</form>
		</div>
	</div>
	
	<br><br><br><br><br>

<?php
	//Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] >0) //check if the code exist
	{
?>
	<div class="placeholder_contribution" >
		<br>
		<p>Welcome to contribute to our database.</p>
		<p>Please type in the chemical formula for the molecule:</p>
		<div class="search_container_main">
			<form action="/contribute_data.php" method="GET">
				<input type="text" placeholder="A molecule..." name="query_contribution" style="font-size: 18px; font-family:Arial;">
				<button type="submit" class="button">Contribute</button>
			</form>
		</div>
		<a href="contribution_userpage.php" class="button">My contributions</a>
	</div>
<?php
	}
	else
	{
		session_destroy();
?>
	<div class="placeholder_contribution" >
		<p>If you would like to contribute to the database, please first </p>
		<a href="login.php" class="button">Login</a>
		or
		<a href="login_register_main.php" class="button">Register</a>
	</div>
<?php
	}	
?>


</div>

<?php include "foot.php"; ?>
