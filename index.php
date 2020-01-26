<?php include("head.php"); ?>

<div class="main">
	<div class="placeholder_introduction" >
		<h1>About the database</h1>
		
		<p>
			The importance of controlling diatomic molecules is growing in chemical physics, owing to their applications in quantum information, ultracold chemistry, and the study of physics beyond the standard model. The majority of these applications rely on laser cooling and trapping techniques, which have been achieved for a few molecules. These techniques are suitable for molecules showing almost vertical Franck-Condon factors (FCF's), which depend directly on the spectroscopic constants of the ground and excited states. Thereby, developing a database with the spectroscopic constants as well as Franck-Condon information will help the research to target the perfect candidates for molecular laser cooling. 
		</p>
		<p>
			This database is devoted to the spectroscopic constants of polar diatomic molecules, taken from Herzberg, for the ground state and first excited states, as well as to the calculation of FCF's assuming a Morse potential shape for all the implied states. 
		</p>
		
		<!---
		<p>
			The importance of controlling diatomic molecules is growing in chemical physics, owing to their applications in quantum information, ultracold chemistry, and the study of physics beyond the standard model. The majority of these applications rely on laser cooling and trapping techniques, which have been achieved for a few molecules. These techniques are suitable for molecules showing almost vertical Franck-Condon factors (FCF's), which depend directly on the spectroscopic constants of the ground and excited states. Thereby, developing a database with the spectroscopic constants as well as Franck-Condon information will help the research to target the perfect candidates for molecular laser cooling. 
		</p>
		<p>
			This database is devoted to the spectroscopic constants of polar diatomic molecules, taken from Herzberg[6], for the ground state and first excited states, as well as to the calculation of FCF's assuming a Morse potential shape for all the implied states. 
		</p>
		
		<div style="font-size:12px; color:#444">
			[1] <a href="https://journals.aps.org/prl/abstract/10.1103/PhysRevLett.88.067901">DeMille D. Quantum computation with trapped polar molecules[J]. Physical Review Letters, 2002, 88(6): 067901.</a>)
			[2] <a href="https://journals.aps.org/prl/abstract/10.1103/PhysRevLett.121.073202">Blasing D B, Pérez-Ríos J, Yan Y, et al. Observation of quantum interference and coherent control in a photochemical reaction[J]. Physical review letters, 2018, 121(7): 073202.</a>
			[3] <a href="https://iopscience.iop.org/article/10.1088/1367-2630/11/5/055049/meta">Carr L D, DeMille D, Krems R V, et al. Cold and ultracold molecules: science, technology and applications[J]. New Journal of Physics, 2009, 11(5): 055049.</a>
			[4] <a href="https://arxiv.org/abs/1907.07682">Essig R, Pérez-Ríos J, Ramani H, et al. Direct Detection of Spin-(In) dependent Nuclear Scattering of Sub-GeV Dark Matter Using Molecular Excitations[J]. arXiv preprint arXiv:1907.07682, 2019.</a>
			[5] <a href="https://journals.aps.org/rmp/abstract/10.1103/RevModPhys.90.025008">Safronova M S, Budker D, DeMille D, et al. Search for new physics with atoms and molecules[J]. Reviews of Modern Physics, 2018, 90(2): 025008.</a>
			[6] Herzberg G. Molecular spectra and molecular structure
		</div>
		
		</div>
		--->

	
	<!----------------Statistics-------------------------------------------->
	<br>
	<div class="placeholder_statistics">

<?php
	// Connect to database
	include('connect.php');
	
	
	echo '<p>Throughout the periodic table, we can have 6903 diatomic polar molecules, 1879 of which should have a \(\Sigma\) ground state, 3064 a \(\Pi\) ground state, 1568 a \(\Delta\) ground state and 392 a \(\Phi\) ground state. ';
	
	
	$sql =  'select distinct Molecule from molecule_data where State like "%X%Sigma%"';
	mysqli_select_db($conn, 'rios');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	$N_results_sigma = $retval->num_rows;
	//echo "Sigma:".$N_results_sigma;
	
	$sql =  'select distinct Molecule from molecule_data where State like "%X%Pi%"';
	mysqli_select_db($conn, 'rios');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	$N_results_pi = $retval->num_rows;
	//echo "Pi:".$N_results_pi;
	
	
	$sql =  'select distinct Molecule from molecule_data where State like "%X%Delta%"';
	mysqli_select_db($conn, 'rios');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	$N_results_delta = $retval->num_rows;
	//echo "Delta:".$N_results_delta;

	$sql =  'select distinct Molecule from molecule_data where State like "%X%Phi%"';
	
	mysqli_select_db($conn, 'rios');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  . mysqli_error($conn));
	}
	$N_results_phi = $retval->num_rows;
	//echo "Phi:".$N_results_phi;
		
	echo 'In the current database, we have '.$N_results_sigma.' molecules with \(\Sigma\) ground state, '.$N_results_pi.' molecules with \(\Pi\) ground state, '.$N_results_delta.' molecules with \(\Delta\) ground state and '.$N_results_phi.' molecules with \(\Phi\) ground state. </p>';
	
	if($N_results_phi > 0)
	{
		echo '<script>var data_available = {Sigma: '.$N_results_sigma.', Pi: '.$N_results_pi.', Delta:'.$N_results_delta.', Phi:'.$N_results_phi.'};</script>';
	}
	else
	{
		echo '<script>var data_available = {Sigma: '.$N_results_sigma.', Pi: '.$N_results_pi.', Delta:'.$N_results_delta.'};</script>';
	}
	// Free memory
	mysqli_free_result($retval);

	mysqli_close($conn);

?>

	<!-- Load d3.js -->
	<script src="js/d3.v4.js"></script>

	<!-- Create a div where the graph will take place -->
	<table>
	<tr>
		<td>
			<div id="div_pie"></div>
		</td>
		<td>
			<div id="div_bar">
				<img src="imgs/statistics_bar.png" height="300"/>
			</div>
			
		</td>
	</tr>
	</table>


	<script>
		
		// set the dimensions and margins of the graph
		var width = 250
			height = 250
			margin = 30;

		// The radius of the pieplot is half the width or half the height (smallest one). I subtract a bit of margin.
		var radius = Math.min(width, height) / 2 - margin;

		// append the svg object to the div called 'my_dataviz'
		var svg = d3.select("#div_pie")
		  .append("svg")
			.attr("width", width)
			.attr("height", height)
		  .append("g")
			.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

		// Create dummy data
		var data = data_available;
		//alert("data: " + JSON.stringify(data));
		// set the color scale
		var color = d3.scaleOrdinal()
		  .domain(["Sigma", "Pi", "Delta","Phi"])
		  .range(["#E3E9E7", "#BABEBD", "#CEC3B2", "#EBEFEE"]);

		// Compute the position of each group on the pie:
		var pie = d3.pie()
		  .value(function(d) {return d.value; })
		var data_ready = pie(d3.entries(data))
		// Now I know that group A goes from 0 degrees to x degrees and so on.

		// shape helper to build arcs:
		var arcGenerator = d3.arc()
		  .innerRadius(0)
		  .outerRadius(radius)

		// Build the pie chart: Basically, each part of the pie is a path that we build using the arc function.
		svg
		  .selectAll('mySlices')
		  .data(data_ready)
		  .enter()
		  .append('path')
			.attr('d', arcGenerator)
			.attr('fill', function(d){ return(color(d.data.key)) })

		// Now add the annotation. Use the centroid method to get the best coordinates
		svg
		  .selectAll('mySlices')
		  .data(data_ready)
		  .enter()
		  .append('text')
		  .text(function(d){ return d.data.key + ": " + d.data.value})
		  .attr("transform", function(d) { return "translate(" + arcGenerator.centroid(d) + ")";  })
		  .style("text-anchor", "middle")
		  .style("font-size", 13)
		var text = svg.select(".labels").selectAll("text")
		.data(pie(data), key);



	</script>
	
	</div>

	
	<br><br><br>



	<!---------------Search------------------>

	<script>
		function get_selected_molecule(selection)
		{
			var selected_molecule = selection.value;
			document.getElementById("input_query").value = selected_molecule;
		}
	
	</script>
	
	</div>
	<div style="width:100%; margin-top:30px;">
		
		
		<div class="placeholder_search">
			<h1>Search in the database</h1>
			<div class="search_container_main">
				<form action="search_data.php" method="GET">
					
					<input type="text" placeholder="Try a molecule (e.g. AlF)..." name="query" id="input_query" style="font-size: 16px; font-family:'Times New Roman', Times, serif;">
					
					Or select a molecule here
					<select id="select_molecule" name="query_molecule_select" onchange="get_selected_molecule(this)" style="font-family:'Times New Roman', Times, serif; option:focus{background-color:#FFF; boder-color:#007367;outline:none;border:1px solid #007367;box-shadow:none;}">		
	
<?php
	// Connect to database
	include('connect.php');
	mysqli_select_db($conn, 'rios');
	$sql = 'SELECT distinct Molecule from molecule_data;';
	mysqli_select_db($conn, 'rios');
	$retval = mysqli_query($conn, $sql);
	if(! $retval)
	{
		die('Error: can not read data: '  .$sql. mysqli_error($conn));
	}
	$N_results = $retval->num_rows;
	$molecules = array();
	while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
	{
		array_push($molecules, $row['Molecule']);
		echo "<option>".$row['Molecule']."</option>\n";
	}
	// Free memory
	mysqli_free_result($retval);

	mysqli_close($conn);	
?>			
			
			
		</select>
					
					
					&nbsp;&nbsp;&nbsp;<button type="submit" class="button">Search</button>
				</form>
			</div>
		</div>
	</div>
	<br><br>
	
	<!------Dropdown menu----
	
	<script type="text/javascript" 
        src="js/gentleSelect/jquery.min.js"></script>
	<script type="text/javascript" src="js/gentleSelect/jquery-gentleSelect.js"></script>

	<link type="text/css" href="js/gentleSelect/jquery-gentleSelect.css" rel="stylesheet" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('#select_molecule').gentleSelect({
					columns: 10,
					//itemWidth: 30,
				});
		});
	</script>
	--->
	<div style="height:100px;width:100%">
	</div>
	<div style="height:500px;width:100%">
		
	</div>




</div>



<?php 
	include('foot.php');
?>
