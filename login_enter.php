<?php include("head.php"); ?>

<div class="main">
<?php
	// Connect to database
	include('connect.php');
	session_start();
	$username = $_REQUEST["username"]; //Get the username by post
	$password = $_REQUEST["password"];
	
	mysqli_select_db($conn, 'molecule_database');
	
	$dbusername = null;
	$dbpassword = null;		
	$dbname = null;
	$dbemail = null;
	$dbiduser = null;
	$sql = 'SELECT * from user_info WHERE BINARY username="'.$username.'"';
	
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
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
	alert("Username not exist.");
	window.location.href="login.php";
</script>
<?php
	}
	else
	{
		if($dbpassword != $password)
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
			$_SESSION["code"]=mt_rand(0, 100000); // Set a random number to avoid user entering directly the welcome page
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