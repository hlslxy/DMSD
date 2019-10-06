<?php include("head.php"); ?>
<?php include "foot.php"; ?>

<div class="main">
<?php
	session_start();
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

</div>


