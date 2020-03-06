<?php 



/* login_register.php
 * 
 * Main program dealing with user register.
 *  
 */

	include("head.php");
	include "foot.php"; 
	include('connect.php');
	
	session_start();
	
	
	//Get the username and password, and hash
	$username = mysqli_real_escape_string($conn, $_REQUEST["username"]); 
	$password_origin = mysqli_real_escape_string($conn, $_REQUEST["password"]);
	
	$options = [
		'cost' => 11,
	];
	$password = password_hash($password_origin,PASSWORD_BCRYPT, $options);
	
	$name = mysqli_real_escape_string($conn, $_REQUEST["name"]);
	$email = mysqli_real_escape_string($conn, $_REQUEST["email"]);
	
	
	
	mysqli_select_db($conn, 'rios');
	                           
	$dbusername = null;
	$dbpassword = null;	 
	$dbname = null;
	$dbemail = null;
	
	/*
	$sql_username = 'SELECT * from user_info WHERE BINARY username="'.$username.'"';
	$sql_email = 'SELECT * from user_info WHERE BINARY email="'.$email.'"';
	
	$result_username = mysqli_query($conn, $sql_username);
	$result_email = mysqli_query($conn, $sql_email);
	*/
	
	// Use prepared statements to prevent SQL injection
	$stmt = $conn->prepare("SELECT * from user_info WHERE BINARY username=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result_username = $stmt->get_result();
	
	$stmt = $conn->prepare("SELECT * from user_info WHERE BINARY email=?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result_email = $stmt->get_result();
	
	
	
	if(! $result_username)
	{
		session_destroy();
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	if(! $result_email)
	{
		session_destroy();
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	
	while($row = mysqli_fetch_array($result_username, MYSQLI_ASSOC))
	{
		$dbusername = mysqli_real_escape_string($conn, $row["username"]);
		$dbpassword = mysqli_real_escape_string($conn, $row["password"]);
	}
	while($row = mysqli_fetch_array($result_email, MYSQLI_ASSOC))
	{
		$dbemail = $row["email"];
	}
	
	if (!is_null($dbusername)) // If the user already exists
	{
		echo "<script>alert('Username ".$username." already exists. Please login with your account.');window.location.href='login.php';</script>";
		mysql_close($conn);
	
	}
	else if (!is_null($dbemail)) // If the user already exists
	{
		echo "<script>alert('Email address ".$email." already exists. Please login with your account.');window.location.href='login.php';</script>";
		mysql_close($conn);
	
	}
	else // Insert user info
	{
		/*
		$sql = 'INSERT into user_info (username, password, name, email) VALUES ("'.$username.'","'.$password.'","'.$name.'","'.$email.'")';
		mysqli_query($conn, $sql);
		*/
		// Use prepared statements to prevent SQL injection
		$stmt = $conn->prepare("INSERT into user_info (username, password, name, email) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $username,$password,$name,$email);
		$stmt->execute();
		echo "<script>alert('Register success. Thank you! Please login with your account.');window.location.href='login.php';</script>";
	}
	mysql_close($conn);
	

?>
