<?php include("head.php"); ?>
<?php include "foot.php"; ?>

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


