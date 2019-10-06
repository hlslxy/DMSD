<?php 
	include("head.php");
	include "foot.php"; 
	include('connect.php');
	
	session_start();
	$username=$_REQUEST["username"]; //Get the username by post
	$password=$_REQUEST["password"];
	
	$name = $_REQUEST["name"];
	$email = $_REQUEST["email"];
	
	
	
	mysqli_select_db($conn, 'molecule_database');
	                           
	$dbusername = null;
	$dbpassword = null;	 
	$dbname = null;
	$dbemail = null;
	
	$sql_username = 'SELECT * from user_info WHERE BINARY username="'.$username.'"';
	$sql_email = 'SELECT * from user_info WHERE BINARY email="'.$email.'"';
	
	$result_username = mysqli_query($conn, $sql_username);
	$result_email = mysqli_query($conn, $sql_email);
	
	
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
		$dbusername = $row["username"];
		$dbpassword = $row["password"];
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
		$sql = 'INSERT into user_info (username, password, name, email) VALUES ("'.$username.'","'.$password.'","'.$name.'","'.$email.'")';
		mysqli_query($conn, $sql);
		echo "<script>alert('Register success. Thank you! Please login with your account.');window.location.href='login.php';</script>";
	}
	mysql_close($conn);
	

?>
