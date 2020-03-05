<?php


/* contribution_userpage.php
 * 
 * Show the contributions of a user.
 *  
 */
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
	
	
	// Include header and footer for the webpage
	include('head.php');

?>
<div class="main">
<?php
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
		
		// Get the user information

		$contributor = mysqli_real_escape_string($conn, $_SESSION["name"]);
		$id_user = mysqli_real_escape_string($conn, $_SESSION["id_user"]);
		
		// Search for the contribution of the user in the database
		$sql = 'SELECT * from molecule_data WHERE BINARY id_user='.$id_user.'';
		//echo "<p>".$sql."</p>";
		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}
		
  		// Show the number of query results
		$N_results = $retval->num_rows;
		
		if($N_results > 0)
		{
			// Show the results
			echo "<br><br>You have ";
			echo $N_results;
			echo " contributions to our database. Thanks!";
			echo "<br><br>";
          
          


			echo '<table width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
			echo '<tr>';
			//echo '<th class="th">idAll_in</th>';
			echo '<th class="th">Molecule</th>';
			//echo '<th class="th">idMol</th>';
			echo '<th class="th">Electronic state</th>';
			echo '<th class="th">Mass <br>(a.m.u)</th>';
			echo '<th class="th">Te <br>(cm\(^{-1})\)</th>';
			echo '<th class="th">\(\omega_e\) <br>(cm\(^{-1}\))</th>';
			echo '<th class="th">\(\omega_e \chi_e\) <br>(cm\(^{-1}\))</th>';
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
				$state = $row['State'];
				$state = replace_latex($state);
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
		}
		else
		{
			echo "<br><br>You have not contribute to our database. Welcome to contribute!";
			echo "<br><br>";	
		}	
		
		// If the user want to contribute more...
		echo '<a href="contribution_main.php" ><button class="button">More contribution</button></a>';
		
			
		echo '</div>'; // "maintable" div
		
		// Free memory
		mysqli_free_result($retval);
		

		mysqli_close($conn);      
      

    }
?>
	</div>
<?php  
	include('foot.php');
?>
