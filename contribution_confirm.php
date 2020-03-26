<?php


/* Contribution_confirm.php
 * 
 * Accept or reject a contribution by a user.
 *  
 */
 
 
	// Include header and footer for the webpage
	include('head.php');
	

	echo "<div class='main'>";
	echo "<h1>Please confirm the user submission:</h1>";
	
	
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
		$A1 = $_GET['A1'];
		$A2 = $_GET['A2'];
		$state_input = $_GET['state'];
		$state = str_replace("\\", "\\\\", $state_input);		
		$state = str_replace('%2B', '+', $state); // get back '+' 
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



		// Search for the data existing in the database
		$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$molecule.'"';

		mysqli_select_db($conn, 'rios');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: cannot read data: '  . mysqli_error($conn));
		}
		
		
		// Show the number of query results
		$N_results = $retval->num_rows;
		echo "<br><br>We have ";
		echo $N_results;
		echo " records of ";
		echo $molecule;
		echo ".<br><br>";

		// Show the existing data in the database

		echo '<table width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
		echo '<tr>';
		//echo '<th class="th">idAll_in</th>';
		echo '<th class="th">Molecule</th>';
		echo '<th class="th">A1</th>';
		echo '<th class="th">A2</th>';
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


		while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
		{
			$mass_au = round($row['Mass'] * 1822.8884, 3);
			echo "<tr>";
			//echo "<td class='td'> {$row['idAll_in']}</td> ";
			echo "<td class='td'> {$row['Molecule']}</td> ";
			echo "<td class='td'> {$row['A1']}</td> ";
			echo "<td class='td'> {$row['A2']}</td> ";
			//echo "<td class='td'> {$row['idMol']}</td> ";
			$state_latex = replace_latex($row['State']);
			echo "<td class='td'> {$state_latex}</td> ";
			echo "<td class='td'> {$row['Mass']}</td> ";
			echo "<td class='td'> {$row['Te']}</td> ";
			echo "<td class='td'> {$row['omega_e']}</td> ";
			echo "<td class='td'> {$row['omega_ex_e']}</td> ";
			echo "<td class='td'> {$row['Be']}</td> ";
			echo "<td class='td'> {$row['alpha_e']}</td> ";
			echo "<td class='td'> {$row['De']}</td> ";
			echo "<td class='td'> {$row['Re']}</td> ";
			echo "<td class='td'> {$row['D0']}</td> ";
			echo "<td class='td'> {$row['IP']}</td> ";
			echo "<td class='td'> {$row['reference_date']}</td> ";
           	
			echo "</tr>";	
		}
		echo '</table><br><br>';	
		
		// Show the user submission
		echo '<h1>User submissions</h1>';
		echo '<table width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
		echo '<tr>';
		//echo '<th class="th">idAll_in</th>';
		echo '<th class="th">Molecule</th>';
		echo '<th class="th">A1</th>';
		echo '<th class="th">A2</th>';
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
		echo '<th class="th">Reference</th>';
		echo '<th class="th">Reference date</th>';
		echo '<th class="th">Contributor</th>';
		echo '</tr>';
		
		//'$molecule', $idmol, '$state', $mass, $Te, $omega_e, $omega_ex_e, $Be, $alpha_e, $De, $Re, $D0, $IP, '$reference_date', '$reference', '$contributor', '$contribution_date', '$id_user'
		$table_HTML = "<tr>";
		//$table_HTML = $table_HTML."<td class='td'> {$row['idAll_in']}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$molecule}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$A1}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$A2}</td> ";
		//$table_HTML = $table_HTML."<td class='td'> {$row['idMol']}</td> ";
		$state_latex = replace_latex($state);
		$table_HTML = $table_HTML."<td class='td'> {$state_latex}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$mass}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Te}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$omega_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$omega_ex_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Be}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$alpha_e}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$De}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$Re}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$D0}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$IP}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$reference}</td> ";
		$table_HTML = $table_HTML."<td class='td'> {$reference_date}</td> ";
		$table_HTML = $table_HTML."<td class='td'>".$contributor."</th>";
		$table_HTML = $table_HTML."</tr>";	
		$table_HTML = $table_HTML."</table>";
		$table_HTML = str_replace("\\\\","\\", $table_HTML);
		echo $table_HTML;
		echo "<br><br><br>";
		


		//echo '</div>'; // "maintable" div
		
?>
		<script>
          	function confirm_submission(link)
          	{
              	//alert(link);
              	window.location.href=link;
            }
			function reject_submission(link)
          	{
            	window.location.href=link;
            }
		</script>

<?php
		$state = str_replace("\\\\", "\\", $state_input);	
		$link_confirm = "contribution_insert_data.php?".
				"molecule=".$molecule."&".
				"idmol=".$idmol."&".
				"A1=".$A1."&".
				"A2=".$A2."&".
				"state=".$state."&".
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
				"contributor=".$contributor."&".
          		"contribution_date=".$contribution_date."&".
				"id_user=".$id_user;

		//echo $link_confirm;
		//echo '"confirm_submission(\''.$link_confirm.'\')"';
		$link_confirm = str_replace('+','%2B', $link_confirm); //replace "+" with "%2B"
      	$link_confirm = str_replace(" ", "+",$link_confirm);
      	//$link_confirm = str_replace('\N', '\\N', $link_confirm);
      	$link_confirm = str_replace("\\","\\\\", $link_confirm);
		
		echo '<button class="button" onclick="confirm_submission(\''.$link_confirm.'\')">Confirm submission</button>';
		echo '&nbsp;&nbsp;&nbsp;';

        $sql = 'SELECT * from user_info WHERE BINARY id_user="'.$id_user.'"';
        $retval = mysqli_query($conn, $sql);
        if(! $retval)
        {
            die('Error: cannot read data: '  . mysqli_error($conn));
        }
        while ($row=mysqli_fetch_array($retval))
        {
            $dbusername = $row["username"];
            $dbemail = $row["email"];
            $dbname = $row["name"];
            $dbiduser = $row["id_user"];
        }
		$link_reject = 'contribution_reject.php?'.
          						'user_email='.$dbemail.'&'.
          						'user_name='.$dbname;
		echo '<button class="button" onclick="reject_submission(\''.$link_reject.'\')">Reject submission</button>';

		echo "</div>";


		// Free memory
		mysqli_free_result($retval);
		

		mysqli_close($conn);

	}
	include('foot.php');
	
?>

