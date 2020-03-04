<?php


/* Contribution_data.php
 * 
 * Main webpage for submitting a contribution by a user.
 * 
 * The submission should be done in a table.
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

	// Check if the user has already logged in
	session_start();
	if ($_SESSION["code"] <=0) // check if the code exist
	{
		session_destroy();
?>
	<div class="main">
		<div class="placeholder_contribution" >
			<p>If you would like to contribute to the database, please first </p>
			<a href="login.php"><button class="button">Login</button></a>
			or
			<a href="login_register_main.php"><button class="button">Register</button></a>
		</div>
<?php
	}
	else // if the code exist == if the user has logged in
	{

		// Connect to database
		include('connect.php');


		echo '<div class="maintable">';
		
		
		// Search for data
		$query_molecule = $_GET['query_contribution'];
		if(strlen($query_molecule)<1)
		{
			echo '<div class="placeholder_search">';
			echo '<div class="search_container_main">';
			echo "<p>Error: please input a chemical formula</p>";
			echo '<a href="contribution_main.php"><button class="button">New search</button></a>';
			echo "</div>";
			echo "</div>";
			die('');
		}

		$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$query_molecule.'"';
		//echo "<p>".$sql."</p>";
		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}

		// Show the number of query results
		$N_results = $retval->num_rows;
		if($N_results < 1)
		echo "<br><br>We have ";
		echo $N_results;
		echo " records of ";
		echo $query_molecule;
		echo ".<br><br>";
			
		// Show the results
		$table_id = "query_results_contribution_".$query_molecule;

		echo '<form method="GET" action="contribution_submit.php">';
		echo '<table id="'.$table_id.'" width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
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
		echo '<th class="th">\(\omega_e \chi_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">B\(_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">\(\alpha_e\) <br>(cm\(^{-1}\))</th>';
		echo '<th class="th">D\(_e\) <br>(10\(^{-7}\) cm\(^{-1}\))</th>';
		echo '<th class="th">R\(_e\) <br>(&#8491)</th>';
		echo '<th class="th">D\(_0\) <br>(eV)</th>';
		echo '<th class="th">IP <br>(eV)</th>';
		echo '<th class="th">Reference</th>';
		echo '<th class="th">Date of reference</th>';
		echo '</tr>';

		$molecules = array();
		//$idMols = array();
		$A1s = array(); // Isotopes
		$A2s = array();
		$states = array();
		$states_short = array();
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
		$references = array();
		while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
		{
			$mass_au = round($row['Mass'] * 1822.8884, 3);
			$mass_amu = $row['Mass'];
			echo "<tr>";
			//echo "<td class='td'> {$row['idAll_in']}</td> ";
			//array_push($idMols, $row['idMol']);
			echo "<td class='td'> {$row['Molecule']}</td> ";
			array_push($molecules, $row['Molecule']);
			//echo "<td class='td'> {$row['idMol']}</td> ";
			
			echo "<td class='td'> {$row['A1']}</td> ";
			array_push($A1s, $row['A1']);
			echo "<td class='td'> {$row['A2']}</td> ";
			array_push($A2s, $row['A2']);
			$state = replace_latex($row['State']);
			echo "<td style='height: 30px; max-width:20px;'> {$state}</td> ";
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
			echo "<td class='td'> {$row['reference']}</td> ";
			array_push($references, $row['reference']);
			echo "<td class='td'> {$row['reference_date']}</td> ";
			array_push($dates, $row['reference_date']);
		
			echo "</tr>";	
		}

	?>
		<tr>
			<td class='td'>
				<input size="7" type="text" name="input_molecule" value="<?php echo $query_molecule; ?>">
			</td>
			<td>
				<input size="3" type="text" placeholder="(NOT NULL)" name="input_A1"/>
			</td>
			<td>
				<input size="3" type="text" placeholder="(NOT NULL)" name="input_A2"/>
			</td>
			<td>
				<input size="15" type="text" placeholder="(NOT NULL, in Latex)" name="input_state"/>
			</td>
			
			<td>
				<input size="10" type="text" name="input_mass" value="<?php echo $mass_amu; ?>">
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_Te"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_omega_e"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_omega_ex_e"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_Be"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_alpha_e"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_De"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_Re"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_D0"/>
			</td>
			<td>
				<input size="5" type="text" placeholder="" value="\N" name="input_IP"/>
			</td>
			<td>
				<input size="25" type="text" placeholder="(NOT NULL)" name="input_reference"/>
			</td>
			<td>
				<input size="10" type="text" placeholder="(NOT NULL)" name="input_reference_date"/>
			</td>
		</tr>
		
		</table>
		
		
		<br><br>
		<button type="submit" class="button">Submit</button>
		</form>
		
		<div style="font-size:15px; color:#444">
			<p>* Please note that the input of electronic state, mass, reference, and date of reference can not be empty.</p>

			<p>* Format of electronic state: </p>
			<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please indicate the state in the format of e.g. X $^1 \Sigma^+$, A $^3\Pi_0^+$, etc., using the Latex format (enclosed in "$") in the input box.</p>

			<p>* Format of reference (APS style): </p>
			<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Journal: Author Surname Author Initial. Title. Publication Title Volume number: Pages Used, Year Published.</p>
			<p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Book: Author Surname Author Initial. Title. City: Publisher, Year Published.</p>
		</div>

	</div> <!---main div-->
	<?php
	
		
		
		echo '</div>'; // "main" div
		// Free memory
		mysqli_free_result($retval);

		mysqli_close($conn);
	}

	
	include('foot.php');
?>

