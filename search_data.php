<?php

	// Connect to database
	include('connect.php');

	// Include header and footer for the webpage
	include('head.php');
	include('foot.php');
?>

	<!--<script type="text/javascript" src="js/math.js"></script>-->
	<script type="text/javascript" src="js/decimal.js"></script>
	<script type="text/javascript" src="js/legendre_gauss_quadrature.js"></script>
<?php
	// Search for data
	$query_molecule = $_GET['query'];
	if(strlen($query_molecule)<1)
	{
		echo '<div class="placeholder_search">';
		echo '<div class="search_container_main">';
		echo "<p>Error: please input a chemical formula</p>";
		echo '<a href="index.php" class="button">New search</a>';
		echo "</div>";
		echo "</div>";
		die('');
	}

	$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$query_molecule.'"';
	//echo "<p>".$sql."</p>";
	mysqli_select_db($conn, 'molecule_database');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}

	// Show the number of query results
	$N_results = $retval->num_rows;
	if($N_results < 1)
	{
		echo '<div class="placeholder_search">';
		echo '<div class="search_container_main">';
		echo "<p style='font-size:18px'>No record found for ";
		echo $query_molecule;
		echo ".</p>";
		echo '<a href="index.php" class="button">New search</a>';
		echo "</div>";
		echo "</div>";
		die('');
	}
	else
	{
		echo "<br><p style='font-size:18px'>Query results of ";
		echo $query_molecule;
		echo ": ";
		echo $N_results;
		echo " records.</p><br>";
	}

	// Show the results

	echo '<table id="table_query_results" width=95% style="border-top:1px solid #777; border-bottom:1px solid #777; border-collapse:collapse;">';
	echo '<tr>';
	//echo '<th class="th">idAll_in</th>';
	echo '<th class="th">Molecule</th>';
	//echo '<th class="th">idMol</th>';
	echo '<th class="th">Electronic state</th>';
	echo '<th class="th">Mass <br>(au)</th>';
	echo '<th class="th">Te <br>(cm$^{-1})$</th>';
	echo '<th class="th">$\omega_e$ <br>(cm$^{-1}$)</th>';
	echo '<th class="th">$\omega_{e}x_{e}$ <br>(cm$^{-1}$)</th>';
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
?>
	<script language="javascript" src="js/download_table.js"> </script>
	<a href="#" class="button" onclick="download_table_as_csv('table_query_results')">Download as CSV</a>

<?php
	// Franck-Condon calculation
	echo '<br><br><p style="font-size: 18px">Calculate the Franck-Condon factor</p>';
	echo 'Please select two states:&nbsp;&nbsp;&nbsp;&nbsp;';

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
	
	<script>

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
					//i_state = i;
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
				}
			}
			
			return i_state;
		}

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
			*/
			

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
			/*
			FC = new Decimal(4000.0);
			FC = FC.exp();
			alert("FC=" + FC);
			*/
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
		function calculate_FC()
		{
			var omega_e =  
				<?php echo json_encode($omega_e); ?>;
			var omega_ex_e =  
				<?php echo json_encode($omega_ex_e); ?>;
			var Re =
				<?php echo json_encode($Re); ?>;
			var mass_au =
				<?php echo json_encode($mass_au); ?>;
			
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
			
			//document.getElementById("div_FC_result").innerHTML = state_initial.toString() + "," + state_final.toString();
			calculate_FC_main(mass_au, state_initial, state_final, omega_e_initial, omega_ex_e_initial, Re_initial, omega_e_final, omega_ex_e_final, Re_final);
			
		}
	</script>

	<button class="button_FC" onclick="calculate_FC();">Calculate</button>
	<br><br>
	<div id="div_FC_result"></div>
	<br>
	<a href="index.php" class="button">New search</a>
	

<?php

	// Free memory
	mysqli_free_result($retval);

	mysqli_close($conn);

?>
