<?php



/* search_data.php
 * 
 * Query in the database for a certain molecule; Show the results and plot.
 *  
 */


	// Connect to database
	include('connect.php');

	// Include header and footer for the webpage
	include('head.php');
?>
	<div class="main">

	<script type="text/javascript" src="js/math.js"></script>
<?php

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

	// Search for data
	$query_molecule = $_GET['query'];
	if(strlen($query_molecule)<1)
	{
		echo '<div class="placeholder_search">';
		echo '<div class="search_container_main">';
		echo "<p>Error: please input a chemical formula</p>";
		echo '<a href="index.php"><button class="button">New search</button></a>';
		echo "</div>";
		echo "</div>";
		die('');
	}
	
	//Find the upper letters in the chemical formula
	$elements = [];
	$tmp_element = '';
	$N_elements = 0;
	for ($i =0; $i < strlen($query_molecule); $i++)
	{
		$letter = substr($query_molecule,$i,1);
		if(ord($letter)>64 && ord($letter)<91) //capital letter
		{
			$tmp_element  = $letter;
			array_push($elements, $tmp_element);
			$N_elements = $N_elements + 1;
		}
		if(ord($letter)>96 && ord($letter)<123) // lower letter
		{
			$tmp_element = $tmp_element.$letter;
			$elements[$N_elements-1] = $tmp_element;
		}
	}

	$query_molecule_ordered = $elements[1].$elements[0]; // Get the other expression of the molecule
	
	$sql1 = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$query_molecule.'"';
	$sql2 = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$query_molecule_ordered.'"';
	
	
	//echo "<p>".$sql."</p>";
	mysqli_select_db($conn, 'rios');
	$retval1 = mysqli_query($conn, $sql1);
	if(! $retval1)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	$retval2 = mysqli_query($conn, $sql2);
	if(! $retval2)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}

	// Show the number of query results
	$N_results1 = $retval1->num_rows;
	$N_results2 = $retval2->num_rows;
	if($N_results1 + $N_results2 < 1)
	{
		echo '<div class="placeholder_search">';
		echo '<div class="search_container_main">';
		echo "<p style='font-size:18px'>No record found for ";
		echo $query_molecule;
		echo ".</p>";
		echo '<a href="index.php" ><button class="button">New search</button></a>';
		echo "</div>";
		echo "</div>";
		die('');
	}
	else
	{
		if($N_results1 > 0)
		{
			$N_results = $N_results1;
			$retval = $retval1;
		}
		if($N_results2 > 0)
		{
			$N_results = $N_results2;
			$retval = $retval2;
			$query_molecule = $query_molecule_ordered;
		}
		
		echo '<h1>Query results of '.$query_molecule.'</h1>';
		echo '<div id="div_info_molecule" style="font-size: 1.2em;"></div>';
		echo "<p style='font-size:18px'> There are ".$N_results." records.";
		echo "</p><br>";
	}

	// Show the results

	echo '<table id="table_query_results" width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
	echo '<tr>';
	//echo '<th class="th">idAll_in</th>';
	//echo '<th class="th">Molecule</th>';
	//echo '<th class="th">idMol</th>';
	echo '<th class="th">Electronic state</th>';
	//echo '<th class="th">Mass <br>(a.m.u)</th>';
	echo '<th class="th">Te <br>(cm\(^{-1})\)</th>';
	echo '<th class="th">\(\omega_e\) <br>(cm\(^{-1}\))</th>';
	echo '<th class="th">\(\omega_{e}x_{e}\) <br>(cm\(^{-1}\))</th>';
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
		$mass_amu = round($row['Mass'] * 1822.8884, 3);
		echo '<script>document.getElementById("div_info_molecule").innerHTML = "(Reduced mass: '.$row['Mass'].'&nbsp; a.m.u.)"</script>';
		echo "<tr>";
		//echo "<td class='td'> {$row['idAll_in']}</td> ";
		//echo "<td class='td'> {$row['Molecule']}</td> ";
		array_push($molecules, $row['Molecule']);
		//echo "<td class='td'> {$row['idMol']}</td> ";
		$state = replace_latex($row['State']);
		echo "<td class='td'> {$state}</td> ";
		array_push($states, $state);
		//echo "<td class='td'> {$mass_amu}</td> ";
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
?>
	<script language="javascript" src="js/download_table.js"> </script>
	<a href="#"  onclick="download_table_as_csv('table_query_results')"><button class="button">Download as CSV</button></a> 
	<a href="index.php" ><button class="button">New search</button></a>

<?php
	// Franck-Condon calculation
	echo '<br><br><br><h1>The Franck-Condon factor</h1>';
	echo '<text style="font-weight: 600">Please select two states&nbsp;</text><text>(\(\nu = 0\)):&nbsp;&nbsp;&nbsp;&nbsp;</text>';

	echo 'Initial state:&nbsp;&nbsp;';
	echo '<select id="select_FC_states_inital">';

	for ($i_state=0; $i_state<$N_results; $i_state++)
	{
		echo "<option>".$states[$i_state]."</option>\n";
	}
	
	echo '</select>';


	echo '&nbsp;&nbsp;&nbsp;Final state:&nbsp;&nbsp;';
	echo '<select id="select_FC_states_final">';

	for ($i_state=0; $i_state<$N_results; $i_state++)
	{
		echo "<option>".$states[$i_state]."</option>\n";
	}

	echo '</select>';
	echo '&nbsp;&nbsp;&nbsp;';
	
?>



	<button class="button_FC" onclick="calculate_FC();">Calculate</button>
	
	<div id="div_FC_result"></div>


	
	<br><br>
	

	<br>
	
	
	<div>
		<h1>Visualize the Franck-Condon factors between the ground state and different excited states</h1>
		<div style="font-size: 1em; color:#505050">
			<p>
				(It may take several seconds to calculate the Franck-Condon factors for the plot.)
			</p>
		</div>
	</div>
	<table>
		<tr>
			<td>
				<button class="button_FC"  onclick="plot_FCF_bar()">Bar plot</button> 
			</td>
			<td>
				<div id="div_barplot_select_state"></div>
			</td>
			<td>
				<select id="select_A_states_barplot" onchange="plot_FCF_bar()">
					<option value = "0">v=0</option>
					<option value = "1">v=1</option>
					<option value = "2">v=2</option>
					<!--
					<option value = "3">vA=3</option>
					<option value = "4">vA=4</option>
					-->
				</select>
			</td>
		</tr>
	</table>
	<div id="plotFCF_bar" style="visibility:hidden; height: 0px; margin-left: 100px;">
		<div id="barplot" ></div>
	</div>
	
	<br><br>
	<button class="button_FC"  onclick="plot_FCF_heatmap()">Density plot</button>
	
	<div id="plotFCF_heatmap" style="visibility:hidden; height: 450px; margin-left: 100px;">
		<div id="heatmap"></div>
	</div>
	<div id="plotFCF_heatmap_legend"style="visibility:hidden; height: 100px; margin-left: 100px;">
		<div id="legend" style="visibility:hidden; margin-left:100px; margin-top: 0px; padding: 0px"></div>
	</div>	
	
	
	
	<!---------------------------------------------Calculate FCF -------------------------------------------------------------->
	<script>
		
		
		
		function get_state_symbol_A()
		{
			var omega_e =  
				<?php echo json_encode($omega_e); ?>;
			var omega_ex_e =  
				<?php echo json_encode($omega_ex_e); ?>;
			var Re =
				<?php echo json_encode($Re); ?>;
			var mass_au =
				<?php echo json_encode($mass_amu); ?>;
			var states =
				<?php echo json_encode($states); ?>;
			var state_labels = []; // X: 0; A: 1; B: 2; ...
			
			for(var i = 0; i < states.length; i ++)
			{
				state = states[i][0];
					
				//state_ascii = state.toUpperCase().charCodeAt();// Convert character to ASCII
				state_ascii = state.charCodeAt();// Convert character to ASCII
				if((state_ascii < 65) || (state_ascii > 122))
				{
					// The notation of the state is not correct (A..Z)
					alert("Error in the notation of the state.")
					return 0;
				}
				if((state_ascii == 88) || (state_ascii == 120))
					state_labels.push(0);       // X: 0 (ground state)
				else
					state_labels.push(state_ascii - 64); // A:1; B:2;....
			}
			
			//alert("State labels:" + state_labels);
			var state_X = 1000;
			var state_A = 1000;
			
			for(var i = 0; i < state_labels.length; i ++)
			{
				if(state_labels[i] == 0)
				{
					state_X = i;
					break;
				}
			}
			for(var i = 0; i < state_labels.length; i ++)
			{
				if(state_labels[i] == 1)
				{
					state_A = i;
					break; // Stop at the first A state
				}
			}
			
			if(state_A == 1000) // There is no information about A state
			{
				state_A = state_labels.length - 1; // Then get the last state in the database
				//alert("No A state" + state_A);
			}
			
			state_symbol_A = states[state_A];
			//alert("Excited state: "+state_symbol_A);
			var message = "&nbsp;&nbsp;Please select a state of the excited state&nbsp;" + state_symbol_A +": &nbsp;&nbsp;";
			document.getElementById("div_barplot_select_state").innerHTML = message;
			
			//alert("Msg: "+message);
			return state_symbol_A;
		}
		var state_symbol_A = get_state_symbol_A(); // Global var storing the excited state of the molecule (it is needed because in some cases we do not have A state, but B, C,..states...)

		function get_selected_option(selection)
		{
			/*
			Get the states selected for the FC factor calculation
			*/
			var i_state;
			for (var i = 0, len = selection.options.length; i < len; i++)
			{
				option = selection.options[i];
				if(option.selected == true)
				{
					i_state = i;
					break;
					/*
					state_selected = option.value[0];
					
					state_selected_ascii = state_selected.toUpperCase().charCodeAt();// Convert character to ASCII
					if((state_selected_ascii < 65) || (state_selected_ascii > 90))
					{
						// The notation of the state is not correct (A..Z)
						alert("Error in the notation of the state.")
						return 0;
					}
					if(state_selected_ascii == 88)
						i_state = 0;       // X: 0 (ground state)
					else
						i_state = state_selected_ascii - 64; // A:1; B:2;....
					break;
					*/
				}
			}
			
			return i_state;
		}
		/* ==========================Implementation of Morse function with Gauss-Legendre quadratures========================
		function beta(mass_au, omega_ex_e)
		{
			return Math.sqrt(2 * mass_au * omega_ex_e * 4.5563352529120 * Math.pow(10, -6));
		}
		function De(omega_e, omega_ex_e)
		{
			return omega_e * omega_e / (4 * omega_ex_e);
		}
		function nu(mass_au, omega_e, omega_ex_e)
		{
			var nu_value =  omega_e / (2 * omega_ex_e);
			//var nu_value = Math.sqrt(2 * mass_au * De(omega_e, omega_ex_e) * 4.5563352529120 * Math.pow(10, -6))/beta(mass_au, omega_ex_e);
			return nu_value;
		}	
		function xi(r, Re, nu_value, beta_value)//(r, mass_au, omega_e, omega_ex_e, Re)
		{
			return 2 * nu_value * Math.exp(- beta_value * (r - Re / 0.529177));
		}
		
		
		function gamma(x) 
		{

			/*
			Calculate the Gamma function for a real number x
			
			1. Calculate log(gamma(x))
			var t = x + 5.24218750000000000;
			t = ( x + 0.5 ) * log(t) - t;
			var s = 0.999999999999997092;
			for ( var j = 0 ; j < 14 ; j++ ) s += c[j] / (x+j+1);
			return t + log( 2.5066282746310005 * s / x );
			
			2. Calculate gamma(x)
			return exp(logGamma(x));
			
			
			var c = [ 57.1562356658629235, -59.5979603554754912, 14.1360979747417471,-0.491913816097620199, .339946499848118887e-4, .465236289270485756e-4,-.983744753048795646e-4, .158088703224912494e-3, -.210264441724104883e-3,.217439618115212643e-3, -.164318106536763890e-3, .844182239838527433e-4,-.261908384015814087e-4, .368991826595316234e-5 ];
			if(Number.isInteger(x) && x <= 0) 
			{
				throw Error('Gamma function pole'); 
			}


			tt = new Decimal(x);
			//alert("x="+x+", tt=" + tt);
			tt = tt.plus(new Decimal(5.24218750000000000)); //t = x+5.24
			//alert("tt="+tt);
			t = new Decimal(x).plus(new Decimal(0.5)).mul(tt.naturalLogarithm()).minus(tt);//t = ( x + 0.5 ) * log(t) - t;
			//alert("t=" + t);
			s = new Decimal(0.999999999999997092);
			for(var i = 0; i < 14; i++)
			{
				// s += c[i]/(x + i + 1)
				ss = new Decimal(c[i]);
				sss = new Decimal(x);
				sss = sss.plus(new Decimal(i));
				sss = sss.plus(new Decimal(1.0));
				ss = ss.dividedBy(sss);
				s = s.plus(ss);
				//alert("i="+i+"  x+i+1="+sss + "  s="+s);
			}
			//alert("s="+s);
			//t = t + log( 2.5066282746310005 * s / x )
			ttt = new Decimal(2.506628274631005);
			ttt = ttt.mul(s).dividedBy(new Decimal(x)).naturalLogarithm();
			t = t.plus(ttt);
			//alert("t="+t);
			t = t.naturalExponential();
			//alert("Gamma(x)="+t);
			return t;
		}

		function N_n(n, nu_value, beta_value)
		{
			var nn = 1.0;
			for(i = 2; i < n+1; i++)
			{
				nn = nn * i;
			}
						
			var Nn_value_tmp = beta_value * nn * (2 * nu_value - 2 * n - 1);
			gamma_value = new Decimal(gamma(2 * nu_value - n)); //math.js/gamma( x ) - gamma function of a real or complex number
			
			Nn_value = new Decimal(Nn_value_tmp);
			Nn_value = Nn_value.dividedBy(gamma_value);//Math.sqrt(Nn_value / gamma_value);
			//alert("gamma = "+gamma_value + ", Nn="+Nn_value);
			return Nn_value;
		}
		function LaguerreL(n, a, x) //https://en.wikipedia.org/wiki/Laguerre_polynomials
		{
			//alert("Laguerre: n="+n);
			if(n == 0)
			{
				return 1;
			}
			else if(n == 1)
			{
				return 1 + a - x;
			}
			else // n >= 2
			{
				return (2 * n - 1 + a - x) * LaguerreL(n - 1, a, x) - (n - 1 + a) * LaguerreL(n - 2, a, x) / n;
			}
		}
		function Morse_wf(r, n, mass_au, omega_e, omega_ex_e, Re)
		{
			var nu_value = new Decimal(nu(mass_au, omega_e, omega_ex_e));
			//alert("In Morse: nu="+nu_value);
			var beta_value = new Decimal(beta(mass_au, omega_ex_e));
			//alert("In Morse: beta="+beta_value);
			var xi_value = new Decimal(xi(r, Re, nu_value, beta_value));//xi(r, mass_au, omega_e, omega_ex_e, Re);
			//alert("In Morse: Xi done, xi="+xi_value);
			var N_n_value = new Decimal(N_n(n, nu_value, beta_value));
			//alert("In Morse: N_n_value = " + N_n_value);
			//var wf = N_n_value * Math.exp((nu_value - n - 0.5) * Math.log(xi_value)) * Math.exp(- xi_value / 2.0);
			wf1 = nu_value.sub(new Decimal(n)).sub(new Decimal(0.5));
			wf2 = xi_value.naturalLogarithm();
			wf1 = wf1.mul(wf2);
			wf1 = wf1.naturalExponential();
			wf3 = new Decimal(0.0);
			wf3 = wf3.sub(xi_value).dividedBy(new Decimal(2.0));
			wf = new Decimal(Nn_value);
			wf = wf.mul(wf1).mul(wf3);
			//alert("In Morse: wf = " + wf1 + "  wf2="+wf2 + "  wf3=" + wf3 + "  wf=" + wf);
			var laguerre_value = LaguerreL(n, 2.0 * nu_value - 2.0 * n - 1, xi_value);
			//alert("In Morse: Laguerre="+laguerre_value);
			wf = wf.mul(new Decimal(laguerre_value));//laguerre(n, 2 * n * nu_r - 2 * n - 1, xi_r);//* Laguerrel(n, 2nu-2n-1, xi(r)): math.js/laguerre( n, a, x ) - associated Laguerre polynomial of real or complex index n and real or complex argument a of a real or complex number
			//alert("In Morse: wf_final = "+wf);
			return wf;
			
			//return 0.0;
		}
		function calculate_FC_main(mass_au, state_initial, state_final, omega_e_initial, omega_ex_e_initial, Re_initial, omega_e_final, omega_ex_e_final, Re_final)
		{
			//alert("In FC_main");
			//alert("state_initial="+state_initial);
			var FC = new Decimal(0.0);
			
			
			alert(gamma(500));
			
			var r_lower = Math.min(Re_initial/0.529177, Re_final/0.529177) - 1.25;
			var r_upper = Math.min(Re_initial/0.529177, Re_final/0.529177) + 3;
			var delta_r = r_upper - r_lower;
			
			for(var k_point = 0; k_point < Legendre_Gauss_points_128.length; k_point ++)//k_point < 10; k_point++)//
			{
				var x_k = Legendre_Gauss_points_128[k_point];
				var w_k = Legendre_Gauss_weights_128[k_point];
				var x = x_k * delta_r + r_lower;
				//alert("x = " + x);
				
				var Morse_wf_initial = new Decimal(Morse_wf(x, state_initial, mass_au, omega_e_initial, omega_ex_e_initial, Re_initial));
				var Morse_wf_final = new Decimal(Morse_wf(x, state_final, mass_au, omega_e_final, omega_ex_e_final, Re_final));
				alert(" x = "+x + ", wf_initial=" + Morse_wf_initial + ", wf_final=" + Morse_wf_final);
				//FC = FC + w_k * Morse_wf_initial * Morse_wf_final;
				FC_tmp = new Decimal(w_k).mul(Morse_wf_initial).mul(Morse_wf_final);
				FC = FC.plus(FC_tmp);
				alert(" x = "+x + ", FC_x=" + FC_tmp + ", FC=" + FC);
			}		
			FC = FC.mul(new Decimal(delta_r));
			FC = FC.mul(FC).abs();
			//alert(FC);
			document.getElementById("div_FC_result").innerHTML = FC.toFixed(3).toString();//FC.toExponential(2).toString();//
			
		}
		*/
		function Morse_potential(r, Beta, Re, De)
		{
			var Morse = De * Math.pow((1.0 - Math.exp(- Beta *(r - Re / 0.529177))), 2.0);
			return Morse;
		}

		function Morse_wf(i_state, N_DVR, x, x_lower, x_upper, delta_x, step_x, n, mass_au, omega_e, omega_ex_e, Re)
		{
			var ceau2cm = 2.194746313702 * (Math.pow(10.0, 5.0))  ;
			var convE=1/(4.5563 * Math.pow(10.0, -6.0)); //from au to cm^{-1}
			
			
			var Beta = Math.sqrt(2 * mass_au * omega_ex_e / ceau2cm);
			var De = omega_e * omega_e / (4 * omega_ex_e ) / ceau2cm;
			

			// Initialize the kinetic energy, potential energy and the Hamiltonian matrix
			var T = new Array(N_DVR - 1);
			var V = new Array(N_DVR - 1);
			var H = new Array(N_DVR - 1);

			for(var i = 0; i < N_DVR - 1; i++)
			{
				T[i] = new Array(N_DVR - 1).fill(0);
				V[i] = new Array(N_DVR - 1).fill(0);
				H[i] = new Array(N_DVR - 1).fill(0);
			}

			for(var i = 1; i < N_DVR; i++)
			{
				for(var j = 1; j < N_DVR; j++)
				{
					if(i == j)
					{
						T[i - 1][j - 1] = 0.25 * (Math.PI * Math.PI) / (delta_x * delta_x) * ((2 * (N_DVR * N_DVR) + 1) / 3 - 1 / Math.pow(Math.sin(Math.PI * i / N_DVR), 2)) / mass_au;
						V[i - 1][j - 1] = Morse_potential(x[i - 1], Beta, Re, De);							
					}
					else
					{
						T[i - 1][j - 1] = 0.25 * Math.pow((-1), (i - j)) * (Math.PI * Math.PI) / (delta_x * delta_x) * ( 1 / Math.pow(Math.sin(Math.PI * (i - j) / 2 / N_DVR), 2) - 1 / Math.pow(Math.sin(Math.PI * (i + j) / 2 / N_DVR), 2)) / mass_au;
					}
					H[i - 1][j - 1] = T[i - 1][j - 1] + V[i - 1][j - 1];
				}
			}
			
			var eigen_result = eigensystem(H); //return { eigenvalues: eigenvalues, eigenvectors: eigenvectors }
			var w_unsort = eigen_result["eigenvalues"];
			var H_eigenvector = eigen_result["eigenvectors"];
			for(var i = 0; i < H_eigenvector.length; i ++)
			{
				for(var j = 0; j < H_eigenvector[0].length; j++)
				{
					H_eigenvector[i][j] = H_eigenvector[i][j] / Math.sqrt(step_x);
				}
			}
			//var dimensions = [H_eigenvector.length, H_eigenvector[0].length ];
			//alert("dimension = " + dimensions);
			//alert("H="+H);
			//alert("H_eigenvector="+H_eigenvector);
			
			var Morse_wavefunction = [];
			for(var i = 0; i < H_eigenvector.length; i ++)
			{
				Morse_wavefunction.push(H_eigenvector[i][i_state]);
			}
			//alert("Morse_wf:" + Math.max(...Morse_wavefunction));
			return Morse_wavefunction;
		}

		function trapezoidal_integration(x, f)
		{
			// x, f(x) are both vectors
			var N_points = x.length;
			var integral = 0.0;
			//alert("N_points: " + N_points + ";   N_points:" + f.length);
			for(var i = 1; i < N_points; i++)
			{
				integral = integral + 0.5 * (x[i] - x[i - 1]) * (f[i] + f[i - 1]); 
			}
			return integral;
		}
		
		function Morse_overlap(Morse_wf_initial, Morse_wf_final)
		{
			// Calculate the overlap of Morse wave fuctions by overlap = (wf_initial * wf_final)^2
			
			var N_points = Morse_wf_initial.length;
			var overlap = [];
			for(var i = 0; i < N_points; i++)
			{
				overlap.push(Morse_wf_initial[i] * Morse_wf_final[i]);
			}
			return overlap;
		}
		
		function calculate_FC_main(mass_au, state_initial, state_final, omega_e_initial, omega_ex_e_initial, Re_initial, omega_e_final, omega_ex_e_final, Re_final)
		{

			// Number of DVR quadrature points
			var N_DVR = 200;
			//alert(N_DVR);
			// Lower and upper limits of the integration
			var x_lower = Math.min(Re_initial/0.529177, Re_final/0.529177) - 0.75;
			var x_upper = Math.max(Re_initial/0.529177, Re_final/0.529177) + 3;
			//alert("lower:"+x_lower+",upper:"+x_upper);
			var delta_x = x_upper - x_lower;
			var step_x = delta_x / N_DVR;
			
			// Generate the DVR grid
			var  x = []; 
			for(var i = 0; i < N_DVR - 1; i++)
			{
				x.push(x_lower + delta_x * i / N_DVR);
			}
			
			//alert("state_initial:"+state_initial+"; state_final:"+state_final)
			var Morse_wf_initial = Morse_wf(0, N_DVR, x, x_lower, x_upper, delta_x, step_x, state_initial, mass_au, omega_e_initial, omega_ex_e_initial, Re_initial);
			var Morse_wf_final = Morse_wf(0, N_DVR, x, x_lower, x_upper, delta_x, step_x, state_final, mass_au, omega_e_final, omega_ex_e_final, Re_final);
			//alert("return from Morse");
			
			var overlap = Morse_overlap(Morse_wf_initial, Morse_wf_final);
			var FC = Math.pow(trapezoidal_integration(x, overlap), 2.0);
			if(isNaN(FC))
			{
				document.getElementById("div_FC_result").innerHTML = "<br>We need more spectroscopic constants to calculate the Franck-Condon factor of these states. Please try another (excited) state.";
			}
			else
			{
				document.getElementById("div_FC_result").innerHTML = "<br>The Franck-Condon factor: " + FC.toFixed(6).toString();//FC.toExponential(2).toString();
			}
		}
		
		function calculate_FC()
		{
			var omega_e =  
				<?php echo json_encode($omega_e); ?>;
			var omega_ex_e =  
				<?php echo json_encode($omega_ex_e); ?>;
			var Re =
				<?php echo json_encode($Re); ?>;
			var mass_au =
				<?php echo json_encode($mass_amu); ?>;
			
			var select_state_initial = document.getElementById('select_FC_states_inital');
			var state_initial = get_selected_option(select_state_initial);
			var select_state_final = document.getElementById('select_FC_states_final');
			var state_final = get_selected_option(select_state_final);	
			
			
			var omega_e_initial = parseFloat(omega_e[state_initial]);
			var omega_ex_e_initial = parseFloat(omega_ex_e[state_initial]);
			var Re_initial = parseFloat(Re[state_initial]);
			var omega_e_final = parseFloat(omega_e[state_final]);
			var omega_ex_e_final = parseFloat(omega_ex_e[state_final]);
			var Re_final = parseFloat(Re[state_final]);
			
			//alert("Initial state:" + state_initial + ", Final state:" + state_final);
			//alert("Final state: " + omega_e_final +","+ omega_ex_e_final +","+ Re_final);
			//document.getElementById("div_FC_result").innerHTML = state_initial.toString() + "," + state_final.toString();
			calculate_FC_main(mass_au, state_initial, state_final, omega_e_initial, omega_ex_e_initial, Re_initial, omega_e_final, omega_ex_e_final, Re_final);
			
		}

		function calculate_FC_main_plot(mass_au, state_initial, state_final, omega_e_initial, omega_ex_e_initial, Re_initial, omega_e_final, omega_ex_e_final, Re_final)
		{
			// Calculate the FCFs for selected initial and final states
			// Returns: FC_all: [N_states_plot(initial), N_states_plot(final)]
			
			//alert("In calculate_FC_main_plot");

			// Number of DVR quadrature points
			var N_DVR = 200;

			// Lower and upper limits of the integration
			var x_lower = Math.min(Re_initial/0.529177, Re_final/0.529177) - 1.25;
			var x_upper = Math.min(Re_initial/0.529177, Re_final/0.529177) + 3;
			var delta_x = x_upper - x_lower;
			var step_x = delta_x / N_DVR;
			
			// Generate the DVR grid
			var  x = []; 
			for(var i = 0; i < N_DVR - 1; i++)
			{
				x.push(x_lower + delta_x * i / N_DVR);
			}
			
			var N_states_plot_X = 3; // Number of states shown in the plot
			var N_states_plot_A = 5;
			var FC_all = []; 
			for(var i_state_initial = 0; i_state_initial < N_states_plot_X; i_state_initial++)
			{
				var FC_i = [];
				for(var i_state_final = 0; i_state_final < N_states_plot_A; i_state_final++)
				{
					var Morse_wf_initial = Morse_wf(i_state_initial, N_DVR, x, x_lower, x_upper, delta_x, step_x, state_initial, mass_au, omega_e_initial, omega_ex_e_initial, Re_initial);
					var Morse_wf_final = Morse_wf(i_state_final, N_DVR, x, x_lower, x_upper, delta_x, step_x, state_final, mass_au, omega_e_final, omega_ex_e_final, Re_final);
					//alert("return from Morse");
					
					var overlap = Morse_overlap(Morse_wf_initial, Morse_wf_final);
					var FC = Math.pow(trapezoidal_integration(x, overlap), 2.0);
					FC_i.push(FC);
				}
				FC_all.push(FC_i);
			}
			return FC_all;
			
		}
		
		function calculate_FC_plot()
		{
			//alert("In calculate plot");
			var omega_e =  
				<?php echo json_encode($omega_e); ?>;
			var omega_ex_e =  
				<?php echo json_encode($omega_ex_e); ?>;
			var Re =
				<?php echo json_encode($Re); ?>;
			var mass_au =
				<?php echo json_encode($mass_amu); ?>;
			var states =
				<?php echo json_encode($states); ?>;
			
			var state_labels = []; // X: 0; A: 1; B: 2; ...
			for(var i = 0; i < states.length; i ++)
			{
				state = states[i][0];
					
				//state_ascii = state.toUpperCase().charCodeAt();// Convert character to ASCII
				state_ascii = state.charCodeAt();// Convert character to ASCII
				if((state_ascii < 65) || (state_ascii > 122))
				{
					// The notation of the state is not correct (A..Z)
					alert("Error in the notation of the state.")
					return 0;
				}
				if((state_ascii == 88) || (state_ascii == 120))
					state_labels.push(0);       // X: 0 (ground state)
				else
					state_labels.push(state_ascii - 64); // A:1; B:2;....
			}
			
			//alert("State labels:" + state_labels);
			var state_X = 1000;
			var state_A = 1000;
			
			for(var i = 0; i < state_labels.length; i ++)
			{
				if(state_labels[i] == 0)
				{
					state_X = i;
					break;
				}
			}
			for(var i = 0; i < state_labels.length; i ++)
			{
				if(state_labels[i] == 1)
				{
					state_A = i;
					break; // Stop at the first A state
				}
			}
			
			if(state_A == 1000) // There is no information about A state
			{
				state_A = state_labels.length - 1; // Then get the last state in the database
				//alert("No A state" + state_A);
			}
			
			state_symbol_A = states[state_A];
			//alert("Excited state: "+state_symbol_A);
			
			//alert("X state:" + state_X + ", A state:" + state_A);
			
			var omega_e_X = parseFloat(omega_e[state_X]);
			var omega_ex_e_X = parseFloat(omega_ex_e[state_X]);
			var Re_X = parseFloat(Re[state_X]);
			var omega_e_A = parseFloat(omega_e[state_A]);
			var omega_ex_e_A = parseFloat(omega_ex_e[state_A]);
			var Re_A = parseFloat(Re[state_A]);
			
			//document.getElementById("div_FC_result").innerHTML = state_initial.toString() + "," + state_final.toString();
			var FC_all = calculate_FC_main_plot(mass_au, state_X, state_A, omega_e_X, omega_ex_e_X, Re_X, omega_e_A, omega_ex_e_A, Re_A);
			//alert(FC_all);
			return FC_all;
			
		}
	</script>


	<script language="javascript" type="text/javascript" >	

	// FC_all: Global array (float): [N_states_plot(initial), N_states_plot(final)]
	var FC_all = [];//calculate_FC_plot();
	
	</script>

	<!-----------------------Plot FCF ---------------------------------------------------------->

	<!-----------Bar plot--------------->
	<script src="js/d3.v4.js"></script>
	
	
	<script language="javascript" type="text/javascript" >

		
		function plot_FCF_bar()
		{
			
			if (typeof FC_all[0] == 'undefined')
			{
				FC_all = calculate_FC_plot();
			}
			document.getElementById("plotFCF_bar").style.visibility = "visible";
			document.getElementById("plotFCF_bar").style.height = "";			
			document.getElementById("barplot").innerHTML = "";

			
			//alert(FC_all);
			//alert("In plot bar");
			var data = [];
			var state_A_selected = 0;
			
			//Get the selected A state
			var selection = document.getElementById('select_A_states_barplot')
			for(var i = 0, len = selection.options.length; i < len; i++)
			{
				option = selection.options[i];
				if(option.selected == true)
				{
					state_A_selected = i;
					break;
				}
			}	
			
			for(var i = 0; i < FC_all.length; i ++) // Loop over initial state (X)
			{
				var point = {
							"vx": "vX=" + i.toString(),
							"value": FC_all[i][state_A_selected],
						};
				data.push(point);
			}
			

			
			// set the dimensions and margins of the graph
			var margin = {top: 30, right: 30, bottom: 30, left: 60},
				width = 350 - margin.left - margin.right,
				height = 400 - margin.top - margin.bottom;

			// append the svg object to the div
			var svg = d3.select("#barplot")
				.append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", height + margin.top + margin.bottom)
				.append("g")
				.attr("transform",
						"translate(" + margin.left + "," + margin.top + ")");

			// Build X scales and axis
			var x = d3.scaleBand()
				.range([ 0, width ])
				.domain(data.map(function(d) {return d.vx;}))
				.padding(0.01);
			svg.append("g")
				.style("font-size", 15)
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x));

			// Build y scales and axis
			var y = d3.scaleLinear()
				.range([ height, 0 ])
				.domain([0, 1.0]);
			var yAxis = d3.axisLeft(y)
				.ticks(5);
			svg.append("g")			
				.style("font-size", 15)
				.call(yAxis);
				
			// Y axis label
			svg.append("text")
				.attr("text-anchor", "end")
				.attr("transform", "rotate(-90)")
				.attr("y", -margin.left+20)
				.attr("x", -margin.top-80)
				.text("Franck-Condon factor")


			
			// create a tooltip
			
			var tooltip = d3.select("#barplot")
				.append("div")
				.style("opacity", 0)
				.style("position", "relative")
				.style("width","100px")
				.attr("class", "tooltip")
				.style("background-color", "white")
				.style("padding", "5px");
			
			// Three function that change the tooltip when user hover / move / leave a cell
			var mouseover = function(d) {
				tooltip
					.style("opacity", 1)
				d3.select(this)
					.style("stroke", "black")
					.style("opacity", 1)
			};
			var mousemove = function(d) {
				tooltip
					.html("FC factor: " + d.value.toFixed(6))//.toFixed(3))
					.style("left", (d3.mouse(this)[0]+40) + "px")
					.style("top", (d3.mouse(this)[1]-450) + "px")
					.style("opacity", 1)
			};
			var mouseleave = function(d) {
				tooltip
					.style("opacity", 0)
				d3.select(this)
					.style("stroke", "none")
			};


			// Plot the bar
			svg.selectAll()
				.data(data)
				.enter()
				.append("rect")
				.attr("x", function(d) { return x(d.vx) })
				.attr("y", function(d) { return y(d.value) })
				.attr("width", x.bandwidth() )
				.attr("height", function(d) { return height - y(d.value); })
				.style("fill", "#69b3a2")
				.on("mouseover", mouseover)
				.on("mousemove", mousemove)
				.on("mouseleave", mouseleave);


		}
	
	</script>
	
	<!-------------------Plot heatmap---------------------------------->
	<style type="text/css">
		.axis text {
		  font-size: 15px;
		}

		.axis line, .axis path {
		  fill: none;
		  stroke: #000;
		  shape-rendering: crispEdges;
		}
	</style>
	<script>

		function generate_data_heatmap()
		{
			if(typeof FC_all[0] == 'undefined')
			{
				FC_all = calculate_FC_plot();
			}
			//alert("In generate data");
			var data = [];
			
			for(var i = 0; i < FC_all.length; i++)
			{
				for(var j = 0; j < FC_all[0].length; j++)
				{
					var point = {
						"vx": "vX=" + i.toString(),
						"va": "v"+ state_symbol_A.substr(0,1) + "=" + j.toString(),
						"value": FC_all[i][j],
					};
					data.push(point);					
				}
			}
			return data;
		}

		  
		function plot_FCF_heatmap()
		{
			//alert("in plot heatmap");
			
			// Show the div
			document.getElementById("plotFCF_heatmap").style.visibility = "visible";
			document.getElementById("plotFCF_heatmap").style.height = "";
			document.getElementById("plotFCF_heatmap_legend").style.visibility = "visible";			
			document.getElementById("legend").style.visibility = "visible";
			
			// Clear the existing plots
			document.getElementById("heatmap").innerHTML = "";
			document.getElementById("legend").innerHTML = "";
			
			// Get the data
			var data = generate_data_heatmap();
		
			// set the dimensions and margins of the graph
			var margin = {top: 30, right: 30, bottom: 30, left: 60},
				width = 400 - margin.left - margin.right,
				height = 500 - margin.top - margin.bottom;

			// append the svg object to the body of the page
			var svg = d3.select("#heatmap")
			.append("svg")
			  .attr("width", width + margin.left + margin.right)
			  .attr("height", height + margin.top + margin.bottom)
			.append("g")
			  .attr("transform",
					"translate(" + margin.left + "," + margin.top + ")");

			// Build X scales and axis:
			var x = d3.scaleBand()
				.range([ 0, width ])
				.domain(data.map(function(d) {return d.vx;}))
				.padding(0.01);
				
			svg.append("g")
				.style("font-size", 15)
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(x))

			//svg.append("g")
			//	.call(d3.axisTop(x).tickValues(["","",""]));

			// Build X scales and axis:
			var y = d3.scaleBand()
				.range([ height, 0 ])
				.domain(data.map(function(d) {return d.va;}))
				.padding(0.01);
				
			svg.append("g")			
				.style("font-size", 15)
				.call(d3.axisLeft(y));
				
			//svg.append("g")			
			//	.style("font-size", 15)
			//	.call(d3.axisRight(y));

			// Build color scale
			var myColor = d3.scaleLinear()
				.range(["white", "#69b3a2"])
				.domain([0.0,1.0]);


			// create a tooltip
			var tooltip = d3.select("#heatmap")
				.append("div")
				.style("opacity", 0)
				.style("position", "relative")
				.style("width","100px")
				.attr("class", "tooltip")
				.style("background-color", "white")
				.style("padding", "5px")
			
			// Three function that change the tooltip when user hover / move / leave a cell
			var mouseover = function(d) {
				tooltip
					.style("opacity", 1)
				d3.select(this)
					.style("stroke", "black")
					.style("opacity", 1)
			}
			var mousemove = function(d) {
				tooltip
					.html("FC factor: " + d.value.toFixed(6))//.toFixed(6))
					.style("left", (d3.mouse(this)[0]+70) + "px")
					.style("top", (d3.mouse(this)[1]-450) + "px")
					.style("opacity", 1)
			}
			var mouseleave = function(d) {
				tooltip
					.style("opacity", 0)
				d3.select(this)
					.style("stroke", "none")
			}



			// Plot the heatmap
			svg.selectAll()
				.data(data)
				.enter()
				.append("rect")
				.attr("x", function(d) { return x(d.vx) })
				.attr("y", function(d) { return y(d.va) })
				.attr("width", x.bandwidth() )
				.attr("height", y.bandwidth() )
				.style("fill", function(d) { return myColor(d.value)} )
				.on("mouseover", mouseover)
				.on("mousemove", mousemove)
				.on("mouseleave", mouseleave);



			// Legend
			
			var width_legend = 300, height_legend = 50;
				
			var key = d3.select("#legend")
				.append("svg")
				.attr("width", width_legend.toString() + " px")
				.attr("height", height_legend.toString() + " px");
				
			var legend = key.append("defs")
				.append("svg:linearGradient")
				.attr("id", "gradient")
				.attr("x1", "0%")
				.attr("y1", "100%")
				.attr("x2", "100%")
				.attr("y2", "100%")
				.attr("spreadMethod", "pad");
				
			legend.append("stop")
				.attr("offset", "0%")
				.attr("stop-color", "#fff")
				.attr("stop-opacity", 1);
				
				
			legend.append("stop")
				.attr("offset", "100%")
				.attr("stop-color", "#69b3a2")
				.attr("stop-opacity", 1);
			
			
			key.append("text")
				.attr("x", 90)
				.attr("y", 12)
				.style("text-anchor", "left")
				.style("font-size", "15 px")
				.text("Franck-Condon factor");
				
			key.append("rect")
				.attr("width", width_legend)
				.attr("height", height_legend - 40)
				.style("fill", "url(#gradient)")
				.attr("transform", "translate(0,20)");
				
			var y_legend = d3.scaleLinear()
				.range([300, 10])
				.domain([1.0, 0]);
				
			var yAxis_legend = d3.axisBottom()
				.scale(y_legend)
				.ticks(5);
				
			key.append("g")
				.attr("class", "y axis")
				.attr("transform", "translate(0,30)")
				.call(yAxis_legend)
				.append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", 0)
				.attr("dy", ".71em")
				.style("text-anchor", "end");
			
		}
	</script>
			
	
<?php


	//include('foot.php');

	// Free memory
	mysqli_free_result($retval);

	mysqli_close($conn);

	
?>
	</div>

<?php 
	include('foot.php');
?>
