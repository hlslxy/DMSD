<?php 



/* login_welcome.php
 * 
 * Redirect to the homepage if the user sucessfully logged in.
 *  
 */
 
	session_start();
  // echo "<script>alert('login_welcome:code=".$_SESSION["code"]."');</script>";
	if ($_SESSION["code"] >0) //check if the code exist
	{
		echo "<script>window.location.href='index.php';</script>";
		
	}
	else
	{
		session_destroy();
		echo "<script>window.location.href='login_exit.php';</script>";
	}
?>

