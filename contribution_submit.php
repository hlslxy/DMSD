<?php


/* Contribution_submit.php
 * 
 * Submit a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
	

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
		$molecule = mysqli_real_escape_string($conn, $_GET['input_molecule']);
		$A1 = (int)$_GET['input_A1'];
		$A2 = (int)$_GET['input_A2'];
		$state_input = mysqli_real_escape_string($conn, $_GET['input_state']);
		$state =$state_input ;//str_replace("\\", "\\\\", $state_input);
		$mass = ((float) $_GET['input_mass'] );/// 1822.8884;
		$Te = mysqli_real_escape_string($conn, $_GET['input_Te']);
		$omega_e = mysqli_real_escape_string($conn, $_GET['input_omega_e']);
		$omega_ex_e = mysqli_real_escape_string($conn, $_GET['input_omega_ex_e']);
		$Be = mysqli_real_escape_string($conn, $_GET['input_Be']);
		$alpha_e = mysqli_real_escape_string($conn, $_GET['input_alpha_e']);
		$De = mysqli_real_escape_string($conn, $_GET['input_De']);
		$Re = mysqli_real_escape_string($conn, $_GET['input_Re']);
		$D0 = mysqli_real_escape_string($conn, $_GET['input_D0']);
		$IP = mysqli_real_escape_string($conn, $_GET['input_IP']);
		$reference = mysqli_real_escape_string($conn, $_GET['input_reference']);
		$reference_date = mysqli_real_escape_string($conn, $_GET['input_reference_date']);

		$contributor = $_SESSION["name"];
		$contribution_date = date("Y-m-d");
		$id_user = $_SESSION["id_user"];
		
		// Get the number of molecules in the database
		$sql =  'select distinct Molecule from molecule_data';
		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  .$sql. mysqli_error($conn));
		}
		$N_molecules = $retval->num_rows;
		
		// Get the idMol for the contributed molecule
		$sql = "SELECT * FROM molecule_data WHERE molecule='".$molecule."'";
		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  .$sql. mysqli_error($conn));
		}
		$N_results = $retval->num_rows;
		if($N_results < 1)
		{
			$idmol = $N_molecules + 1;
		}
		else
		{
			while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
			{
				$idmol = $row['idMol'];
			}
		}
		
		
		// Check for duplicate
		$sql = "SELECT * FROM molecule_data WHERE molecule='".$molecule."' AND mass=".$mass;
		if(!strstr($Te,'\N'))
		{
			$sql = $sql." AND Te=".$Te;
		}
		if(!strstr($omega_e,'\N'))
		{
			$sql = $sql." AND omega_e=".$omega_e;
		}
		if(!strstr($omega_ex_e,'\N'))
		{
			$sql = $sql." AND omega_ex_e=".$omega_ex_e;
		}
		if(!strstr($Be,'\N'))
		{
			$sql = $sql." AND Be=".$Be;
		}
		if(!strstr($alpha_e,'\N'))
		{
			$sql = $sql." AND alpha_e=".$alpha_e;
		}
		if(!strstr($De,'\N'))
		{
			$sql = $sql." AND De=".$De;
		}
		if(!strstr($Re,'\N'))
		{
			$sql = $sql." AND Re=".$Re;
		}
		if(!strstr($D0,'\N'))
		{
			$sql = $sql." AND D0=".$D0;
		}
		if(!strstr($IP,'\N'))
		{
			$sql = $sql." AND IP=".$IP;
		}
		
		
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  . mysqli_error($conn));
		}
		
		$N_duplications = $retval->num_rows;
		
		// Alert the users if their submission is the same as what we already have in database
		if($N_duplications > 0) 
		{
			echo '<script>alert("It seems that your submission is duplicated with previous contributions. Please check again your submission. Thanks!"); window.history.go(-1);</script>';
			die('');
		}
		
		
		// Send an email to the administrator

		$subject = "[The diatomic database] User contribution";             

      	$contributor_url = str_replace(" ", "+", $contributor);
      	$state_link = str_replace('+','%2B', $state); //replace "+" in state with "%2B"
		$link = "contribution_confirm.php?".
				"molecule=".$molecule."&".
				"idmol=".$idmol."&".
				"A1=".$A1."&".
				"A2=".$A2."&".
				"state=".$state_link."&".
				"mass=".$mass."&".
				"Te=".$Te."&".
				"omega_e=".$omega_e."&".
				"omega_ex_e=".$omega_ex_e."&".
				"Be=".$Be."&".
				"alpha_e=".$alpha_e."&".
				"De=".$De."&".
          		"Re=".$Re."&".
				"D0=".$D0."&".
				"IP=".$IP."&".
				"reference_date=".$reference_date."&".
				"reference=".$reference."&".
				"contributor=".$contributor_url."&".
          		"contribution_date=".$contribution_date."&".
				"id_user=".$id_user;
      
		//$link_encoded = rawurlencode($link);
		
      	$link = str_replace(" ", "+",$link);
      	$link = str_replace('\\N', '\N', $link);
      	$link = str_replace("\\\\","\\", $link);
		$message = "Please confirm the user contributions from ".$contributor.
				" via (by copying the following link to your browser):  https://rios.mp.fhi.mpg.de/".
				$link;
		
		$from = "xyliu@fhi-berlin.mpg.de";   
		$headers = "From:" . $from;        
		//$to = "xyliu@fhi-berlin.mpg.de, jperezri@fhi-berlin.mpg.de";  
        $to = "xyliu@fhi-berlin.mpg.de";  
		mail($to,$subject,$message,$headers);
		echo '<div class="maintable">';
		echo "<h1>Submittion success!</h1>";
		echo "<p>Thanks for your contribution! An email has been sent to the website administrator. You will be informed by email after your contribution has been confirmed.</p>";
		
		/*
		// Search for the data existing in the database
		$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$molecule.'"';
		//echo "<p>".$sql."</p>";
		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  . mysqli_error($conn));
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
		echo '<th class="th">Mass <br>(a.m.u)</th>';
		echo '<th class="th">Te <br>(cm$^{-1})$</th>';
		echo '<th class="th">\(\omega_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">\(\omega_{exe}\) <br>(cm$^{-1}$)</th>';
		echo '<th class="th">B\(_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">\(\alpha_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">D\(_e\) <br>(10\(^{-7}\) cm\(^{-1}\))</th>';
		echo '<th class="th">R\(_e\) <br>(&#8491)</th>';
		echo '<th class="th">D\(_0\) <br>(eV)</th>';
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
			$state = replace_latex($row['State']);
			echo "<td class='td'> {$state}</td> ";
			array_push($states, $state);
			echo "<td class='td'> {$row['Mass']}</td> ";
			array_push($masses, $row['Mass']);
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
		*/
		// Show the user submission
		echo '<table width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
		echo '<tr>';
		//echo '<th class="th">idAll_in</th>';
		echo '<th class="th">Molecule</th>';
		echo '<th class="th">A\(_1\)</th>';
		echo '<th class="th">A\(_2\)</th>';
		//echo '<th class="th">idMol</th>';
		echo '<th class="th">Electronic state</th>';
		echo '<th class="th">Mass <br>(a.m.u)</th>';
		echo '<th class="th">Te <br>(cm\(^{-1})\)</th>';
		echo '<th class="th">\(\omega_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">\(\omega_{e}\chi_{e}\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">B\(_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">\(\alpha_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">D\(_e\) <br>(10\(^{-7}\) cm\(^{-1}\))</th>';
		echo '<th class="th">R\(_e\) <br>(&#8491)</th>';
		echo '<th class="th">D\(_0\) <br>(eV)</th>';
		echo '<th class="th">IP <br>(eV)</th>';
		echo '<th class="th">Date</th>';
		echo '</tr>';
		
		//'$molecule', $idmol, '$state', $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, '$reference_date', '$reference', '$contributor', '$contribution_date', '$id_user'
		$table_HTML = "<tr>";
		//echo "<td class='td'> {$row['idAll_in']}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$molecule}</td> ";
		//echo "<td class='td'> {$row['idMol']}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$A1}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$A2}</td> ";
		$state_latex = replace_latex($state);
		$table_HTML = $table_HTML."<td class='td'> {$state_latex}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$mass}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Te}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$omega_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$oemga_ex_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Be}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$alpha_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$De}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Re}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$D0}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$IP}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$reference_date}</td> ";
		$table_HTML = $table_HTML."</tr>";	
		$table_HTML = $table_HTML."</table>";
		
		$table_HTML = str_replace("\\\\","\\", $table_HTML);
		echo $table_HTML;
		echo "<br><br><br>";
		
		// If the user want to contribute more...
		echo '<a href="contribution_main.php"><button class="button">More contributions</button></a>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a href="contribution_userpage.php"><button class="button">My Contributions</button></a>';
			
		echo '</div>'; // "maintable" div
		
		// Free memory
		mysqli_free_result($retval);
		

		mysqli_close($conn);
	}
	
	include('foot.php');

?>

