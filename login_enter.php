<?php 
session_start();


/* login_enter.php
 * 
 * Main program dealing with user login by checking the user information in the database.
 *  
 */



	include("head.php"); 

?>

<div class="main">
<?php


	// Connect to database
	include('connect.php');

	$username =  mysqli_real_escape_string($conn, $_REQUEST["username"]); //Get the username by post
	$password = mysqli_real_escape_string($conn, $_REQUEST["password"]);
	
	
	mysqli_select_db($conn, 'rios');
	
	$dbusername = null;
	$dbpassword = null;		
	$dbname = null;
	$dbemail = null;
	$dbiduser = null;
	
	
	/*
	$sql = 'SELECT * from user_info WHERE BINARY username="'.$username.'"';
	
	$retval = mysqli_query($conn, $sql);
	*/
	// Use prepared statements to prevent SQL injection
	$stmt = $conn->prepare("SELECT * from user_info WHERE BINARY username=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$retval = $stmt->get_result();
	

	
	if(! $retval)
	{
		die('Error: cannot read data: '  . mysqli_error($conn));
	}
	while ($row=mysqli_fetch_array($retval))
	{
		$dbusername = $row["username"];
		$dbpassword = $row["password"];
		$dbemail = $row["email"];
		$dbname = $row["name"];
		$dbiduser = $row["id_user"];
	}
	if(is_null($dbusername))
	{
		
?>
<script type="text/javascript">
	
	alert("Username does not exist.");
	window.location.href="login.php";
</script>
<?php
	}
	else
	{
		if(!password_verify($password, $dbpassword))
		{
?>
<script type="text/javascript">
	alert("Password error. Please check your password.");
	window.location.href="login.php";
</script>
<?php
		}
		else
		{
			$_SESSION["username"] = $username;
			$_SESSION["email"] = $dbemail;
			$_SESSION["name"] = $dbname;
			$_SESSION["id_user"] = $dbiduser;
			$_SESSION["code"]=mt_rand(10, 100000); // Set a random number to avoid user entering directly the welcome page
			//echo "<script>alert('code=".$_SESSION["code"]."');</script>";

?>
<script type="text/javascript">
	window.location.href="login_welcome.php";
</script>
<?php
		}
	}
	mysqli_close($conn);

?>
</div>

<?php include "foot.php"; ?>
