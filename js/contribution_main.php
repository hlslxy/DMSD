
<?php


/* contribution_main.php
 * 
 * Main webpage of contribution to the database.
 *  
 */


	// Include header and footer for the webpage
	include('head.php');
?>
	<div class="main">

<?php
	//Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] >0) //check if the code exist
	{
?>
	<div class="placeholder_contribution" >
		<h1>Contribute</h1>
		<p>Welcome to contribute to our database.</p>
		<p>Please type in the chemical formula for the molecule:</p>
		<div class="search_container_main">
			<form action="contribute_data.php" method="GET">
				<input type="text" placeholder="A molecule..." name="query_contribution" style="font-size: 18px; font-family:Arial;">
				<button type="submit" class="button">Contribute</button>
			</form>
		</div>
		<br><br>
		<a href="contribution_userpage.php"> <button class="button">My Contributions</button></a>
	</div>
<?php
	}
	else
	{
		session_destroy();
?>
	<div class="placeholder_contribution" >
		<p>If you would like to contribute to the database, please first </p>
		<a href="login.php"><button class="button">Login</button></a>
		or
		<a href="login_register_main.php" ><button class="button">Register</button></a>
	</div>
<?php
	}	
?>


	</div>


<?php 
	include('foot.php');
?>
