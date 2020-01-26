<?php 



/* login_exit.php
 * 
 * Log out the user.
 *  
 */

	include("head.php"); 

?>

<div class="main">
<?php
	session_start();
	session_destroy();
?>
<p>You have logged out.</p>
<script type="text/javascript">
	window.location.href="login.php";
</script>

</div>




<?php 
	include('foot.php');
?>
