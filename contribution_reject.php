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
	<p>Please write down the reason why the user contribution is rejected. Thanks!</p>

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


	//mailto:user@email
	$user_email = $_GET['user_email'];
	$user_name = $_GET['user_name'];
    //$mailto_action = "contribution_reject_email.php?user_email=".$user_email."&user_name=".$user_name;
    //echo '<form method="GET" action="'.$mailto_action.'">';
      //<form method="post" action="mailto:email@example.com" >
?>
     	<br>
      	<form method="GET" action="contribution_reject_email.php">
<?php
      	echo 'This email will be sent to the contributor: <br>'.
          			'<input type="text" name="user_name" value="'.$user_name.'" size="0">';
      	echo '<input type="text" name="user_email" value="'.$user_email.'" size="0">';
?>
      	<br><br>
      	Your email &nbsp; &nbsp;<input type="text" name="mail" value="" size="20">
      	<br><br>
		<textarea name="comments" rows = "6" cols = "30">
<?php
		echo 'Please write down here your comments to the contribution of '.$user_name.'.';
        echo ' It will be sent by email to the contributor.';
?>
		</textarea>
		<br>
          <p><input style="button" type="submit" name="submit" value="Send" />
	</p>
    </form>
	</div>
<?php
	}
	include('foot.php');
	
?>
