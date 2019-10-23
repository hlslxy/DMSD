<?php


/* Contribution_submit.php
 * 
 * Submit a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
	include('foot.php');

	// Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] <=0) // check if the code exist
	{
		session_destroy(); // Force quit if the user has not logged in
?>
	<div class="placeholder_contribution" >
		<p>If you would like to contribute to the database, please first </p>
		<a href="login.php" class="button">Login</a>
		or
		<a href="login_register_main.php" class="button">Register</a>
	</div>
<?php
	}
	else // if the code exist == if the user has logged in
	{

		// Connect to database
		include('connect.php');
		mysqli_select_db($conn, 'molecule_database');
		
		// Get the submittion
		$molecule = $_GET['input_molecule'];
		$state_input = $_GET['input_state'];
		$state = str_replace("\\", "\\\\", $state_input);
		$mass = ((float) $_GET['input_mass'] )/ 1822.8884;
		$Te = $_GET['input_Te'];
		$omega_e = $_GET['input_omega_e'];
		$omega_ex_e = $_GET['input_omega_ex_e'];
		$Be = $_GET['input_Be'];
		$alpha_e = $_GET['input_alpha_e'];
		$De = $_GET['input_De'];
		$Re = $_GET['input_Re'];
		$D0 = $_GET['input_D0'];
		$IP = $_GET['input_IP'];
		$reference = $_GET['input_reference'];
		$reference_date = $_GET['input_reference_date'];

		$contributor = $_SESSION["name"];
		$contribution_date = date("Y-m-d");
		$id_user = $_SESSION["id_user"];
		
		
		// Check for duplicate
		$sql = "SELECT * FROM molecule_data WHERE molecule='".$molecule."' AND mass=".$mass;
		if($Te!='\N')
		{
			$sql = $sql." AND Te=".$Te;
		}
		if($omega_e != '\N')
		{
			$sql = $sql." AND omega_e=".$omega_e;
		}
		if($omega_ex_e != '\N')
		{
			$sql = $sql." AND omega_ex_e=".$omega_ex_e;
		}
		if($Be != '\N')
		{
			$sql = $sql." AND Be=".$Be;
		}
		if($alpha_e != '\N')
		{
			$sql = $sql." AND alpha_e=".$alpha_e;
		}
		if($De != '\N')
		{
			$sql = $sql." AND De=".$De;
		}
		if($Re != '\N')
		{
			$sql = $sql." AND Re=".$Re;
		}
		if($D0 != '\N')
		{
			$sql = $sql." AND D0=".$D0;
		}
		if($IP != '\N')
		{
			$sql = $sql." AND IP=".$IP;
		}
		//echo $sql;
		
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}
		
		$N_duplications = $retval->num_rows;
		
		// Alert the users if their submission is the same as what we already have in database
		if($N_duplications > 0) 
		{
			echo '<script>alert("It seems that your submission is duplicated with previous contributions. Please check again your submission. Thanks!"); window.history.go(-1);</script>';
			die('');
		}
		
				

		// Insert data
		$sql =  "INSERT INTO molecule_data".
				"(Molecule, State, Mass, Te, omega_e, omega_ex_e, Be, alpha_e, De, Re, D0, IP, reference_date, reference, contributor, contribution_date, id_user)".
				"VALUES".
				"('$molecule', '$state', $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, '$reference_date', '$reference', '$contributor', '$contribution_date', '$id_user')";
		mysqli_select_db($conn, 'molecule_database');

		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not insert data: '  . mysqli_error($conn));
		}
		
		echo '<div class="maintable">';
		echo "Submittion success!";


		// Search for the data existing in the database
		$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$molecule.'"';
		//echo "<p>".$sql."</p>";
		mysqli_select_db($conn, 'molecule_database');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}
		
		
		// Show the number of query results
		$N_results = $retval->num_rows;
		echo "<br><br>Now we have ";
		echo $N_results;
		echo " records of ";
		echo $molecule;
		echo ".<br><br>";

		// Show the results

		echo '<table width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
		echo '<tr>';
		//echo '<th class="th">idAll_in</th>';
		echo '<th class="th">Molecule</th>';
		//echo '<th class="th">idMol</th>';
		echo '<th class="th">Electronic state</th>';
		echo '<th class="th">Mass <br>(au)</th>';
		echo '<th class="th">Te <br>(cm$^{-1})$</th>';
		echo '<th class="th">$\omega_e$ <br>(cm$^{-1}$)</th>';
		echo '<th class="th">$\omega_{exe}$ <br>(cm$^{-1}$)</th>';
		echo '<th class="th">B$_e$ <br>(cm$^{-1}$)</th>';
		echo '<th class="th">$\alpha_e$ <br>(cm$^{-1}$)</th>';
		echo '<th class="th">D$_e$ <br>(10$^{-7}$ cm$^{-1}$)</th>';
		echo '<th class="th">R$_e$ <br>(&#8491)</th>';
		echo '<th class="th">D$_0$ <br>(eV)</th>';
		echo '<th class="th">IP <br>(eV)</th>';
		echo '<th class="th">Date</th>';
		echo '</tr>';

		$molecules = array();
		$states = array();
		$masses = array();
		$Te = array();
		$omega_e = array();
		$omega_ex_e = array();
		$Be = array();
		$alpha_e = array();
		$De = array();
		$Re = array();
		$D0 = array();
		$IPs = array();
		$dates = array();
		while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
		{
			$mass_au = round($row['Mass'] * 1822.8884, 3);
			echo "<tr>";
			//echo "<td class='td'> {$row['idAll_in']}</td> ";
			echo "<td class='td'> {$row['Molecule']}</td> ";
			array_push($molecules, $row['Molecule']);
			//echo "<td class='td'> {$row['idMol']}</td> ";
			echo "<td class='td'> {$row['State']}</td> ";
			array_push($states, $row['State']);
			echo "<td class='td'> {$mass_au}</td> ";
			array_push($masses, $row['mass_au']);
			echo "<td class='td'> {$row['Te']}</td> ";
			array_push($Te, $row['Te']);
			echo "<td class='td'> {$row['omega_e']}</td> ";
			array_push($omega_e, $row['omega_e']);
			echo "<td class='td'> {$row['omega_ex_e']}</td> ";
			array_push($omega_ex_e, $row['omega_ex_e']);
			echo "<td class='td'> {$row['Be']}</td> ";
			array_push($Be, $row['Be']);
			echo "<td class='td'> {$row['alpha_e']}</td> ";
			array_push($alpha_e, $row['alpha_e']);
			echo "<td class='td'> {$row['De']}</td> ";
			array_push($De, $row['De']);
			echo "<td class='td'> {$row['Re']}</td> ";
			array_push($Re, $row['Re']);
			echo "<td class='td'> {$row['D0']}</td> ";
			array_push($D0, $row['D0']);
			echo "<td class='td'> {$row['IP']}</td> ";
			array_push($IPs, $row['IP']);
			echo "<td class='td'> {$row['reference_date']}</td> ";
			array_push($dates, $row['reference_date']);
			echo "</tr>";	
		}
		echo '</table><br><br>';	
		
		// If the user want to contribute more...
		echo '<a href="index.php" class="button">More contribution</a>';
		
			
		echo '</div>'; // "maintable" div
		
		// Free memory
		mysqli_free_result($retval);
		

		mysqli_close($conn);
	}

?>

