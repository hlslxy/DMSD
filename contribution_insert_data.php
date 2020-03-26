
<?php


/* contribution_insert_data.php
 * 
 * Insert a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
	
	echo "<div class='main'>";

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
		$username_admin = ["hlslxy", "jesus"];
		
		if(!in_array($_SESSION["username"], $username_admin))
		{
			die('Please login with the administrator accounts.');
		}
	
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
		$molecule = mysqli_real_escape_string($conn, $_GET['molecule']);
		$idmol = (int)$_GET['idmol'];
		$state = mysqli_real_escape_string($conn, $_GET['state']);
		//$state = str_replace("\\", "\\\\", $state);
		$state = str_replace('%2B', '+', $state); // get back '+' 
		$state = str_replace("\\\\", "\\", $state);
		$A1 = mysqli_real_escape_string($conn,$_GET['A1']);
		$A2 = mysqli_real_escape_string($conn,$_GET['A2']);
		$mass = ((float) $_GET['mass'] );/// 1822.8884;
		$Te = mysqli_real_escape_string($conn,$_GET['Te']);
		$omega_e = mysqli_real_escape_string($conn,$_GET['omega_e']);
		$omega_ex_e = mysqli_real_escape_string($conn,$_GET['omega_ex_e']);
		$Be = mysqli_real_escape_string($conn,$_GET['Be']);
		$alpha_e = mysqli_real_escape_string($conn,$_GET['alpha_e']);
		$De = mysqli_real_escape_string($conn,$_GET['De']);
		$Re = mysqli_real_escape_string($conn,$_GET['Re']);
		$D0 = mysqli_real_escape_string($conn,$_GET['D0']);
		$IP = mysqli_real_escape_string($conn,$_GET['IP']);
		
		//Replace '\N' with null for binding parameters
		if(strpos($Te, 'N') !== false)
		{
			$Te = null;
		}
		if(strpos($omega_e, 'N') !== false)
		{
			$omega_e = null;
		}
		if(strpos($omega_ex_e, 'N') !== false)
		{
			$omega_ex_e = null;
		}
		if(strpos($Be, 'N') !== false)
		{
			$Be = null;
		}
		if(strpos($alpha_e, 'N') !== false)
		{
			$alpha_e = null;
		}
		if(strpos($De, 'N') !== false)
		{
			$De = null;
		}
		if(strpos($Re, 'N') !== false)
		{
			$Re = null;
		}
		if(strpos($D0, 'N') !== false)
		{
			$D0 = null;
		}
		if(strpos($IP, 'N') !== false)
		{
			$IP = null;
		}
		$reference = mysqli_real_escape_string($conn, $_GET['reference']);
		$reference_date = mysqli_real_escape_string($conn, $_GET['reference_date']);

		$contributor =  mysqli_real_escape_string($conn, $_GET['contributor']);
		$contribution_date =  mysqli_real_escape_string($conn, $_GET['contribution_date']);
		$id_user =  mysqli_real_escape_string($conn, $_GET['id_user']);

		// Insert data
		
		/*
		$sql =  "INSERT INTO molecule_data".
				"(Molecule, idMol, A1, A2, State, Mass, Te, omega_e, omega_ex_e, Be, alpha_e, De, Re, D0, IP, reference_date, reference, contributor, contribution_date, id_user)".
				"VALUES".
				"('$molecule', $idmol, $A1, $A2, '$state', $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, '$reference_date', '$reference', '$contributor', '$contribution_date', '$id_user')";
		
		$sql = str_replace("\\\\", "\\", $sql);
		mysqli_select_db($conn, 'rios');
		
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot insert data: '  . mysqli_error($conn));
		}
		*/
		
		//Check again the connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		// Use prepared statements to prevent SQL injection
		$stmt = $conn->prepare("INSERT INTO molecule_data (Molecule, idMol, A1, A2, State, Mass, Te, omega_e, omega_ex_e, Be, alpha_e, De, Re, D0, IP, reference_date, reference, contributor, contribution_date, id_user) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param("siiisddddddddddssssi", $molecule, $idmol, $A1, $A2, $state, $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, $reference_date, $reference, $contributor, $contribution_date, $id_user);
		$stmt->execute();
		
		echo "<br><br><br><br><p>User submission has been insert into the database.</p>";


		// Send an email to the contributor
		
		$sql = 'SELECT * from user_info WHERE BINARY id_user="'.$id_user.'"';
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  . mysqli_error($conn));
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
	}
	
	include('foot.php');

?>

