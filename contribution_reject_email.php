<?php


/* Contribution_reject.php
 * 
 * Write some reasons why rejecting a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
?>


	<div class="main">
	<h1> Reject user contribution </h1>

<?php

	// Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] <=0) // check if the code exist
	{
		session_destroy(); // Force quit if the user has not logged in
?>
	<div class="placeholder_contribution" >
		<p>Please first login with your account, then come back with the link provided in the email.</p>
		<a href="login.php" class="button">Login</a>
	</div>
<?php
	}
	else // if the code exist == if the user has logged in
	{
		// Check if the account belongs to the administrator
		$username_admin = ["hlslxy","jesus"];
		if(!in_array($_SESSION["username"], $username_admin))
		{
			die('Please login with the administrator accounts.');
		}

?>

	<p>The explanation for the rejection has been sent to the contributor. Thanks!</p>

<?php	
	echo "email:".$_GET['mail'];   
    echo "    To:".$_GET['user_email'];
	$subject = "[The diatomic database] Unfortunately, your contribution is rejected.";             
	//$message = "Thanks for contributing to the diatomic spectroscopic database. Your contribution is confirmed.";
    $message = "Dear ".
        $_GET['user_name'].
        ",
        
Unfortunately, your submission to the database is rejected because of the following reason:
         
          ".
        $_GET['comments'].
        "
         
However, you are always welcome to contribute to our database again!

Thanks so much for your submission, and best regards,

The DMSD team
        ";
	//$message = wordwrap($message,70);
	$from = $_GET['mail'];   
	$headers = "From:" . $from;        
    $to =$_GET['user_email'];
	mail($to,$subject,$message,$headers);
      
?>
	</div>
<?php   
	}
	include('foot.php');
?>
