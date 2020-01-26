
<?php


/* Contribution_confirm.php
 * 
 * Accept or reject a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
	
	echo "<div class='main'>";
	echo "<h1>Confirm user submission</h1>";
	
	/* TODO: Administrator login 
	 * (problem: how togo back to the confirmation page after login?)
	 
	// Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] <=0) // check if the code exist
	{
		session_destroy(); // Force quit if the user has not logged in
?>
	<div class="placeholder_contribution" >
		<p>Please login </p>
		<a href="login.php" class="button">Login</a>
		or
		<a href="login_register_main.php" class="button">Register</a>
	</div>
<?php
	}
	else // if the code exist == if the user has logged in
	{
	*/
	// Connect to database
	include('connect.php');
	mysqli_select_db($conn, 'rios');
	
	
	function replace_latex($latex)
	{
		$N = -1;
		$latex_replaced = '';
		for($i = 0; $i < strlen($latex); $i++)
		{
			$letter = substr($latex,$i,1);
			if($letter == '$')
			{
				$N = - $N;
				if($N > 0) //The first $
				{
					$latex_replaced = $latex_replaced.'\(';
				}
				else //The second $
				{
					$latex_replaced = $latex_replaced.'\)';				
				}
			}
			else
			{
				$latex_replaced = $latex_replaced.$letter;
			}
		}
		//echo '<script>alert('.$latex_replaced.')</script>';
		return $latex_replaced;
	}
	
	// Get the submittion
	$molecule = $_GET['molecule'];
	$idmol = $_GET['idmol'];
	$state = $_GET['state'];
	//$state = str_replace("\\", "\\\\", $state_input);
	$mass = ((float) $_GET['mass'] );/// 1822.8884;
	$Te = $_GET['Te'];
	$omega_e = $_GET['omega_e'];
	$omega_ex_e = $_GET['omega_ex_e'];
	$Be = $_GET['Be'];
	$alpha_e = $_GET['alpha_e'];
	$De = $_GET['De'];
	$Re = $_GET['Re'];
	$D0 = $_GET['D0'];
	$IP = $_GET['IP'];
	$reference = $_GET['reference'];
	$reference_date = $_GET['reference_date'];

	$contributor =  $_GET['contributor'];
	$contribution_date =  $_GET['contribution_date'];
	$id_user =  $_GET['id_user'];

	// Insert data
	$sql =  "INSERT INTO molecule_data".
			"(Molecule, idMol, State, Mass, Te, omega_e, omega_ex_e, Be, alpha_e, De, Re, D0, IP, reference_date, reference, contributor, contribution_date, id_user)".
			"VALUES".
			"('$molecule', $idmol, '$state', $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, '$reference_date', '$reference', '$contributor', '$contribution_date', '$id_user')";
	
	mysqli_select_db($conn, 'rios');
	//echo $sql."<br>";
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not insert data: '  . mysqli_error($conn));
	}

	
	echo "<br><br><br><br><p>User submission has been insert into the database.</p>";


	// Send an email to the contributor
	
	$sql = 'SELECT * from user_info WHERE BINARY id_user="'.$id_user.'"';
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

	$to = $dbemail;   
	$subject = "[The diatomic database] Thanks for your contribution.";             
	$message = "Thanks for contributing to the diatomic spectroscopic database. Your contribution is confirmed.";
			
	$from = "xyliu@fhi-berlin.mpg.de";   
	$headers = "From:" . $from;        
	mail($to,$subject,$message,$headers);



	// Free memory
	mysqli_free_result($retval);
	

	mysqli_close($conn);
	
	echo "</div>";
	
	include('foot.php');

?>

