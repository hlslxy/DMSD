

<?php include("head.php"); ?>


<div class="main">
	<div class="placeholder_introduction" >
		<h1>About the database</h1>
		
		<p>
			The importance of controlling diatomic molecules is growing in chemical physics, owing to their applications in quantum information, ultracold chemistry, and the study of physics beyond the standard model. The majority of these applications rely on laser cooling and trapping techniques, which have been achieved for a few molecules. These techniques are suitable for molecules showing almost vertical Franck-Condon factors (FCF's), which depend directly on the spectroscopic constants of the ground and excited states. Thereby, developing a database with the spectroscopic constants as well as Franck-Condon information will help the research to target the perfect candidates for molecular laser cooling. 
		</p>
		<p>
			This database is devoted to the spectroscopic constants of polar diatomic molecules, taken from Huber and Herzberg's book <sup>1</sup>, for the ground state and first excited states, as well as to the calculation of FCF's assuming a Morse potential shape for all the implied states. 
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

		<br>
		<p style="font-size:10pt;color:#666666">[1] K.P.Huber and G.Herzberg, Molecular Spectra and Molecular Structure. Springer-Verlag, Berlin, Germany, 1979.</p>
	
	
	<!----------------Statistics-------------------------------------------->
	<br>
	<div class="placeholder_statistics">
	<h1>Statistics</h1>
		<?php
			// Connect to database
			include('connect.php');
			
			
			//echo '<p>Throughout the periodic table, we can have 6903 diatomic polar molecules, 1879 of which should have a \(\Sigma\) ground state, 3064 a \(\Pi\) ground state, 1568 a \(\Delta\) ground state and 392 a \(\Phi\) ground state. ';
			
			
			$sql =  'select distinct Molecule from molecule_data where State like "%X%Sigma%"';
			mysqli_select_db($conn, 'rios');
			$retval = mysqli_query($conn, $sql);
			if(! $retval)
			{
				die('Error: cannot read data: '  . mysqli_error($conn));
			}
			$N_results_sigma = $retval->num_rows;
			//echo "Sigma:".$N_results_sigma;
			
			$sql =  'select distinct Molecule from molecule_data where State like "%X%Pi%"';
			mysqli_select_db($conn, 'rios');
			$retval = mysqli_query($conn, $sql);
			if(! $retval)
			{
				die('Error: cannot read data: '  . mysqli_error($conn));
			}
			$N_results_pi = $retval->num_rows;
			//echo "Pi:".$N_results_pi;
			
			
			$sql =  'select distinct Molecule from molecule_data where State like "%X%Delta%"';
			mysqli_select_db($conn, 'rios');
			$retval = mysqli_query($conn, $sql);
			if(! $retval)
			{
				die('Error: cannot read data: '  . mysqli_error($conn));
			}
			$N_results_delta = $retval->num_rows;
			//echo "Delta:".$N_results_delta;

			$sql =  'select distinct Molecule from molecule_data where State like "%X%Phi%"';
			
			mysqli_select_db($conn, 'rios');
			$retval = mysqli_query($conn, $sql);
			if(! $retval)
			{
				die('Error: cannot read data: '  . mysqli_error($conn));
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


		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<style>
		canvas {
			max-width: 1200px;
			margin: 0 auto;
			display: block;
		}
		</style>

		<canvas id="periodicChart" width="730" height="450"></canvas>
		<!-------------
		<script>
		// Periodic table data
		const elements = [
			{ symbol: "H", name: "Hydrogen", x: 1, y: 1 },
			{ symbol: "He", name: "Helium", x: 18, y: 1 },
			{ symbol: "Li", name: "Lithium", x: 1, y: 2 },
			{ symbol: "Be", name: "Beryllium", x: 2, y: 2 },
			{ symbol: "B", name: "Boron", x: 13, y: 2 },
			{ symbol: "C", name: "Carbon", x: 14, y: 2 },
			{ symbol: "N", name: "Nitrogen", x: 15, y: 2 },
			{ symbol: "O", name: "Oxygen", x: 16, y: 2 },
			{ symbol: "F", name: "Fluorine", x: 17, y: 2},
			{ symbol: "Ne", name: "Neon", x: 18, y: 2 },

			{ symbol: "Na", name: "Sodium", x: 1, y: 3 },
			{ symbol: "Mg", name: "Magnesium", x: 2, y: 3 },
			{ symbol: "Al", name: "Aluminium", x: 13, y: 3 },
			{ symbol: "Si", name: "Silicon", x: 14, y: 3 },
			{ symbol: "P", name: "Phosphorus", x: 15, y: 3 },
			{ symbol: "S", name: "Sulfur", x: 16, y: 3 },
			{ symbol: "Cl", name: "Chlorine", x: 17, y: 3 },
			{ symbol: "Ar", name: "Argon", x: 18, y: 3 },

			{ symbol: "K", name: "Potassium", x: 1, y: 4 },
			{ symbol: "Ca", name: "Calcium", x: 2, y: 4 },
			{ symbol: "Sc", name: "Scandium", x: 3, y: 4 },
			{ symbol: "Ti", name: "Titanium", x: 4, y: 4 },
			{ symbol: "V", name: "Vanadium", x: 5, y: 4 },
			{ symbol: "Cr", name: "Chromium", x: 6, y: 4 },
			{ symbol: "Mn", name: "Manganese", x: 7, y: 4 },
			{ symbol: "Fe", name: "Iron", x: 8, y: 4 },
			{ symbol: "Co", name: "Cobalt", x: 9, y: 4 },
			{ symbol: "Ni", name: "Nickel", x: 10, y: 4 },
			{ symbol: "Cu", name: "Copper", x: 11, y: 4 },
			{ symbol: "Zn", name: "Zinc", x: 12, y: 4 },
			{ symbol: "Ga", name: "Gallium", x: 13, y: 4 },
			{ symbol: "Ge", name: "Germanium", x: 14, y: 4 },
			{ symbol: "As", name: "Arsenic", x: 15, y: 4 },
			{ symbol: "Se", name: "Selenium", x: 16, y: 4 },
			{ symbol: "Br", name: "Bromine", x: 17, y: 4 },
			{ symbol: "Kr", name: "Krypton", x: 18, y: 4 },

			{ symbol: "Rb", name: "Rubidium", x: 1, y: 5 },
			{ symbol: "Sr", name: "Strontium", x: 2, y: 5 },
			{ symbol: "Y", name: "Yttrium", x: 3, y: 5 },
			{ symbol: "Zr", name: "Zirconium", x: 4, y: 5 },
			{ symbol: "Nb", name: "Niobium", x: 5, y: 5 },
			{ symbol: "Mo", name: "Molybdenum", x: 6, y: 5 },
			{ symbol: "Tc", name: "Technetium", x: 7, y: 5 },
			{ symbol: "Ru", name: "Ruthenium", x: 8, y: 5 },
			{ symbol: "Rh", name: "Rhodium", x: 9, y: 5 },
			{ symbol: "Pd", name: "Palladium", x: 10, y: 5 },
			{ symbol: "Ag", name: "Silver", x: 11, y: 5 },
			{ symbol: "Cd", name: "Cadmium", x: 12, y: 5 },
			{ symbol: "In", name: "Indium", x: 13, y: 5 },
			{ symbol: "Sn", name: "Tin", x: 14, y: 5 },
			{ symbol: "Sb", name: "Antimony", x: 15, y: 5 },
			{ symbol: "Te", name: "Tellurium", x: 16, y: 5 },
			{ symbol: "I", name: "Iodine", x: 17, y: 5 },
			{ symbol: "Xe", name: "Xenon", x: 18, y: 5 },

			{ symbol: "Cs", name: "Cesium", x: 1, y: 6 },
			{ symbol: "Ba", name: "Barium", x: 2, y: 6 },
			// Lanthanides (placed separately)
			{ symbol: "La", name: "Lanthanum", x: 3, y: 9 },
			{ symbol: "Ce", name: "Cerium", x: 4, y: 9 },
			{ symbol: "Pr", name: "Praseodymium", x: 5, y: 9 },
			{ symbol: "Nd", name: "Neodymium", x: 6, y: 9 },
			{ symbol: "Pm", name: "Promethium", x: 7, y: 9 },
			{ symbol: "Sm", name: "Samarium", x: 8, y: 9 },
			{ symbol: "Eu", name: "Europium", x: 9, y: 9 },
			{ symbol: "Gd", name: "Gadolinium", x: 10, y: 9 },
			{ symbol: "Tb", name: "Terbium", x: 11, y: 9 },
			{ symbol: "Dy", name: "Dysprosium", x: 12, y: 9 },
			{ symbol: "Ho", name: "Holmium", x: 13, y: 9 },
			{ symbol: "Er", name: "Erbium", x: 14, y: 9 },
			{ symbol: "Tm", name: "Thulium", x: 15, y: 9 },
			{ symbol: "Yb", name: "Ytterbium", x: 16, y: 9 },
			{ symbol: "Lu", name: "Lutetium", x: 17, y: 9 },

			{ symbol: "Hf", name: "Hafnium", x: 4, y: 6 },
			{ symbol: "Ta", name: "Tantalum", x: 5, y: 6 },
			{ symbol: "W", name: "Tungsten", x: 6, y: 6 },
			{ symbol: "Re", name: "Rhenium", x: 7, y: 6 },
			{ symbol: "Os", name: "Osmium", x: 8, y: 6 },
			{ symbol: "Ir", name: "Iridium", x: 9, y: 6 },
			{ symbol: "Pt", name: "Platinum", x: 10, y: 6 },
			{ symbol: "Au", name: "Gold", x: 11, y: 6 },
			{ symbol: "Hg", name: "Mercury", x: 12, y: 6 },
			{ symbol: "Tl", name: "Thallium", x: 13, y: 6 },
			{ symbol: "Pb", name: "Lead", x: 14, y: 6 },
			{ symbol: "Bi", name: "Bismuth", x: 15, y: 6 },
			{ symbol: "Po", name: "Polonium", x: 16, y: 6 },
			{ symbol: "At", name: "Astatine", x: 17, y: 6 },
			{ symbol: "Rn", name: "Radon", x: 18, y: 6 },

			{ symbol: "Fr", name: "Francium", x: 1, y: 7 },
			{ symbol: "Ra", name: "Radium", x: 2, y: 7 },
			// Actinides (placed separately)
			{ symbol: "Ac", name: "Actinium", x: 3, y: 10 },
			{ symbol: "Th", name: "Thorium", x: 4, y: 10 },
			{ symbol: "Pa", name: "Protactinium", x: 5, y: 10 },
			{ symbol: "U", name: "Uranium", x: 6, y: 10 },
			{ symbol: "Np", name: "Neptunium", x: 7, y: 10 },
			{ symbol: "Pu", name: "Plutonium", x: 8, y: 10 },
			{ symbol: "Am", name: "Americium", x: 9, y: 10 },
			{ symbol: "Cm", name: "Curium", x: 10, y: 10 },
			{ symbol: "Bk", name: "Berkelium", x: 11, y: 10 },
			{ symbol: "Cf", name: "Californium", x: 12, y: 10 },
			{ symbol: "Es", name: "Einsteinium", x: 13, y: 10 },
			{ symbol: "Fm", name: "Fermium", x: 14, y: 10 },
			{ symbol: "Md", name: "Mendelevium", x: 15, y: 10 },
			{ symbol: "No", name: "Nobelium", x: 16, y: 10 },
			{ symbol: "Lr", name: "Lawrencium", x: 17, y: 10 }
		];

		// Custom plugin for rectangles around bubbles
		const rectanglePointPlugin = {
			id: "rectanglePointPlugin",
			afterDatasetsDraw(chart) {
			const ctx = chart.ctx;
			const dataset = chart.data.datasets[0];
			dataset.data.forEach((point, index) => {
				const element = elements[index];
				if (!element) return;

				// Calculate pixel positions
				const meta = chart.getDatasetMeta(0);
				const pointElement = meta.data[index];
				if (!pointElement) return;

				const x = pointElement.x;
				const y = pointElement.y;
				const size = 33;

				ctx.save();
				ctx.strokeStyle = element.color || "rgba(0,0,0,0.1)";
				ctx.lineWidth = 1;
				ctx.fillStyle = element.color || "rgba(0,0,0,0.0)";
				ctx.beginPath();
				ctx.rect(x - size / 2, y - size / 2, size, size);
				ctx.fill();
				ctx.stroke();
				ctx.restore();

				// Draw the element symbol inside the rectangle
				ctx.fillStyle = "black";
				ctx.textAlign = "center";
				ctx.textBaseline = "middle";
				ctx.font = "12px Arial";
				ctx.fillText(element.symbol, x, y);
			});
			}
		};

		// Fetch molecule data from server and then create chart
		fetch('get_molecules.php')
			.then(response => response.json())
			.then(moleculeMap => {
			// Add molecule data to elements
			elements.forEach(el => {
				el.molecule = moleculeMap[el.symbol] || [];
			});
			
			console.log("Elements with molecules:", elements);


			// Prepare data points for chart
			const dataPoints = elements.map(el => ({
				x: el.x,
				y: -el.y,  // Invert y for chart.js coordinate system
				r: 10,
				label: el.symbol
			}));

			const ctx = document.getElementById('periodicChart').getContext('2d');
			new Chart(ctx, {
				type: 'bubble',
				data: {
				datasets: [{
					label: 'Elements',
					data: dataPoints,
					backgroundColor: 'rgba(0, 0, 0, 0.1)',
					borderColor: 'rgba(0, 0, 0, 0.0)',
					borderWidth: 0,
				}]
				},
				options: {
				plugins: {
					tooltip: {
					backgroundColor: 'white',//'rgba(21, 134, 103,0.1)',
					borderColor: 'rgba(0, 0, 0, 0.1)',
					titleColor: '#000',
					bodyColor: '#000',
					filter: tooltipItem => {
						// Show tooltip only if element has molecules
						const element = elements[tooltipItem.dataIndex];
						return element.molecule && element.molecule.length > 0;
					},
					callbacks: {
						label: context => {
						const element = elements.find(el => el.symbol === context.raw.label);
						if (element && element.molecule?.length) {
							return element.molecule;//.join(', ');
						}
						return '';
						}
					}
					},
					legend: { display: false }
				},
				scales: {
					x: {
					min: 0,
					max: 19,
					ticks: { stepSize: 1 },
					display: false,
					grid: { display: false }
					},
					y: {
					min: -11,
					max: 0,
					ticks: {
						callback: val => -val,
						stepSize: 1
					},
					display: false,
					grid: { display: false }
					}
				}
				},
				plugins: [rectanglePointPlugin]
			});
			})
			.catch(error => {
			console.error('Failed to fetch molecule data:', error);
			});
		</script>
		------>
		<script>
		// Periodic table data
		const elements = [
			{ symbol: "H", name: "Hydrogen", x: 1, y: 1 },
			{ symbol: "He", name: "Helium", x: 18, y: 1 },
			{ symbol: "Li", name: "Lithium", x: 1, y: 2 },
			{ symbol: "Be", name: "Beryllium", x: 2, y: 2 },
			{ symbol: "B", name: "Boron", x: 13, y: 2 },
			{ symbol: "C", name: "Carbon", x: 14, y: 2 },
			{ symbol: "N", name: "Nitrogen", x: 15, y: 2 },
			{ symbol: "O", name: "Oxygen", x: 16, y: 2 },
			{ symbol: "F", name: "Fluorine", x: 17, y: 2},
			{ symbol: "Ne", name: "Neon", x: 18, y: 2 },

			{ symbol: "Na", name: "Sodium", x: 1, y: 3 },
			{ symbol: "Mg", name: "Magnesium", x: 2, y: 3 },
			{ symbol: "Al", name: "Aluminium", x: 13, y: 3 },
			{ symbol: "Si", name: "Silicon", x: 14, y: 3 },
			{ symbol: "P", name: "Phosphorus", x: 15, y: 3 },
			{ symbol: "S", name: "Sulfur", x: 16, y: 3 },
			{ symbol: "Cl", name: "Chlorine", x: 17, y: 3 },
			{ symbol: "Ar", name: "Argon", x: 18, y: 3 },

			{ symbol: "K", name: "Potassium", x: 1, y: 4 },
			{ symbol: "Ca", name: "Calcium", x: 2, y: 4 },
			{ symbol: "Sc", name: "Scandium", x: 3, y: 4 },
			{ symbol: "Ti", name: "Titanium", x: 4, y: 4 },
			{ symbol: "V", name: "Vanadium", x: 5, y: 4 },
			{ symbol: "Cr", name: "Chromium", x: 6, y: 4 },
			{ symbol: "Mn", name: "Manganese", x: 7, y: 4 },
			{ symbol: "Fe", name: "Iron", x: 8, y: 4 },
			{ symbol: "Co", name: "Cobalt", x: 9, y: 4 },
			{ symbol: "Ni", name: "Nickel", x: 10, y: 4 },
			{ symbol: "Cu", name: "Copper", x: 11, y: 4 },
			{ symbol: "Zn", name: "Zinc", x: 12, y: 4 },
			{ symbol: "Ga", name: "Gallium", x: 13, y: 4 },
			{ symbol: "Ge", name: "Germanium", x: 14, y: 4 },
			{ symbol: "As", name: "Arsenic", x: 15, y: 4 },
			{ symbol: "Se", name: "Selenium", x: 16, y: 4 },
			{ symbol: "Br", name: "Bromine", x: 17, y: 4 },
			{ symbol: "Kr", name: "Krypton", x: 18, y: 4 },

			{ symbol: "Rb", name: "Rubidium", x: 1, y: 5 },
			{ symbol: "Sr", name: "Strontium", x: 2, y: 5 },
			{ symbol: "Y", name: "Yttrium", x: 3, y: 5 },
			{ symbol: "Zr", name: "Zirconium", x: 4, y: 5 },
			{ symbol: "Nb", name: "Niobium", x: 5, y: 5 },
			{ symbol: "Mo", name: "Molybdenum", x: 6, y: 5 },
			{ symbol: "Tc", name: "Technetium", x: 7, y: 5 },
			{ symbol: "Ru", name: "Ruthenium", x: 8, y: 5 },
			{ symbol: "Rh", name: "Rhodium", x: 9, y: 5 },
			{ symbol: "Pd", name: "Palladium", x: 10, y: 5 },
			{ symbol: "Ag", name: "Silver", x: 11, y: 5 },
			{ symbol: "Cd", name: "Cadmium", x: 12, y: 5 },
			{ symbol: "In", name: "Indium", x: 13, y: 5 },
			{ symbol: "Sn", name: "Tin", x: 14, y: 5 },
			{ symbol: "Sb", name: "Antimony", x: 15, y: 5 },
			{ symbol: "Te", name: "Tellurium", x: 16, y: 5 },
			{ symbol: "I", name: "Iodine", x: 17, y: 5 },
			{ symbol: "Xe", name: "Xenon", x: 18, y: 5 },

			{ symbol: "Cs", name: "Cesium", x: 1, y: 6 },
			{ symbol: "Ba", name: "Barium", x: 2, y: 6 },
			// Lanthanides (placed separately)
			{ symbol: "La", name: "Lanthanum", x: 3, y: 9 },
			{ symbol: "Ce", name: "Cerium", x: 4, y: 9 },
			{ symbol: "Pr", name: "Praseodymium", x: 5, y: 9 },
			{ symbol: "Nd", name: "Neodymium", x: 6, y: 9 },
			{ symbol: "Pm", name: "Promethium", x: 7, y: 9 },
			{ symbol: "Sm", name: "Samarium", x: 8, y: 9 },
			{ symbol: "Eu", name: "Europium", x: 9, y: 9 },
			{ symbol: "Gd", name: "Gadolinium", x: 10, y: 9 },
			{ symbol: "Tb", name: "Terbium", x: 11, y: 9 },
			{ symbol: "Dy", name: "Dysprosium", x: 12, y: 9 },
			{ symbol: "Ho", name: "Holmium", x: 13, y: 9 },
			{ symbol: "Er", name: "Erbium", x: 14, y: 9 },
			{ symbol: "Tm", name: "Thulium", x: 15, y: 9 },
			{ symbol: "Yb", name: "Ytterbium", x: 16, y: 9 },
			{ symbol: "Lu", name: "Lutetium", x: 17, y: 9 },

			{ symbol: "Hf", name: "Hafnium", x: 4, y: 6 },
			{ symbol: "Ta", name: "Tantalum", x: 5, y: 6 },
			{ symbol: "W", name: "Tungsten", x: 6, y: 6 },
			{ symbol: "Re", name: "Rhenium", x: 7, y: 6 },
			{ symbol: "Os", name: "Osmium", x: 8, y: 6 },
			{ symbol: "Ir", name: "Iridium", x: 9, y: 6 },
			{ symbol: "Pt", name: "Platinum", x: 10, y: 6 },
			{ symbol: "Au", name: "Gold", x: 11, y: 6 },
			{ symbol: "Hg", name: "Mercury", x: 12, y: 6 },
			{ symbol: "Tl", name: "Thallium", x: 13, y: 6 },
			{ symbol: "Pb", name: "Lead", x: 14, y: 6 },
			{ symbol: "Bi", name: "Bismuth", x: 15, y: 6 },
			{ symbol: "Po", name: "Polonium", x: 16, y: 6 },
			{ symbol: "At", name: "Astatine", x: 17, y: 6 },
			{ symbol: "Rn", name: "Radon", x: 18, y: 6 },

			{ symbol: "Fr", name: "Francium", x: 1, y: 7 },
			{ symbol: "Ra", name: "Radium", x: 2, y: 7 },
			// Actinides (placed separately)
			{ symbol: "Ac", name: "Actinium", x: 3, y: 10 },
			{ symbol: "Th", name: "Thorium", x: 4, y: 10 },
			{ symbol: "Pa", name: "Protactinium", x: 5, y: 10 },
			{ symbol: "U", name: "Uranium", x: 6, y: 10 },
			{ symbol: "Np", name: "Neptunium", x: 7, y: 10 },
			{ symbol: "Pu", name: "Plutonium", x: 8, y: 10 },
			{ symbol: "Am", name: "Americium", x: 9, y: 10 },
			{ symbol: "Cm", name: "Curium", x: 10, y: 10 },
			{ symbol: "Bk", name: "Berkelium", x: 11, y: 10 },
			{ symbol: "Cf", name: "Californium", x: 12, y: 10 },
			{ symbol: "Es", name: "Einsteinium", x: 13, y: 10 },
			{ symbol: "Fm", name: "Fermium", x: 14, y: 10 },
			{ symbol: "Md", name: "Mendelevium", x: 15, y: 10 },
			{ symbol: "No", name: "Nobelium", x: 16, y: 10 },
			{ symbol: "Lr", name: "Lawrencium", x: 17, y: 10 }
		];
		// Colors
		const defaultColor = 'rgba(0,0,0,0.08)';
		const hoveredColor = 'rgba(21, 134, 103,0.5)';
		const relatedColor = 'rgba(21, 134, 103 ,0.25)';

		// Plugin for hover highlighting
		const hoverHighlightPlugin = {
		id: 'hoverHighlight',
		beforeEvent(chart, args) {
			const event = args.event;
			if (event.type === 'mousemove') {
			const points = chart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);
			if (points.length) {
				const hoveredIndex = points[0].index;
				const hoveredSymbol = elements[hoveredIndex].symbol;

				const relatedElements = chart.moleculeMap[hoveredSymbol] || [];

				let changed = false;
				chart.data.datasets.forEach(dataset => {
				const newColors = dataset.data.map((point, i) => {
					const sym = elements[i].symbol;
					if (sym === hoveredSymbol) return hoveredColor;
					if (relatedElements.includes(sym)) return relatedColor;
					return defaultColor;
				});

				// Only update if colors actually changed
				if (JSON.stringify(dataset.backgroundColor) !== JSON.stringify(newColors)) {
					dataset.backgroundColor = newColors;
					changed = true;
				}
				});

				if (changed) {
				chart.render();
				}
			} else {
				resetColors(chart);
			}
			} else if (event.type === 'mouseout') {
			resetColors(chart);
			}

			function resetColors(chart) {
			let changed = false;
			chart.data.datasets.forEach(dataset => {
				const newColors = dataset.data.map(() => defaultColor);
				if (JSON.stringify(dataset.backgroundColor) !== JSON.stringify(newColors)) {
				dataset.backgroundColor = newColors;
				changed = true;
				}
			});
			if (changed) {
				chart.render();
			}
			}
		}
		};


		const rectanglePointPlugin = {
		id: "rectanglePointPlugin",
		afterDatasetsDraw(chart) {
			const ctx = chart.ctx;
			const dataset = chart.data.datasets[0];
			const meta = chart.getDatasetMeta(0);

			dataset.data.forEach((point, index) => {
			const element = elements[index];
			if (!element) return;

			const pointElement = meta.data[index];
			if (!pointElement) return;

			const x = pointElement.x;
			const y = pointElement.y;
			const size = 33;

			ctx.save();

			// Use dataset backgroundColor for fill
			const fillColor = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor[index] : dataset.backgroundColor;
			ctx.fillStyle = fillColor || "white";//"rgba(0,0,0,0)";
			ctx.strokeStyle = "black"; // Or any color you want for border
			ctx.lineWidth = 0.5;

			ctx.beginPath();
			ctx.rect(x - size / 2, y - size / 2, size, size);
			ctx.fill();
			ctx.stroke();

			// Draw symbol text inside rectangle
			ctx.fillStyle = "black";
			ctx.textAlign = "center";
			ctx.textBaseline = "middle";
			ctx.font = "12px Arial";
			ctx.fillText(element.symbol, x, y);

			ctx.restore();
			});
		}
		};


		fetch('get_molecules.php')
		.then(res => res.json())
		.then(moleculeMap => {
			const data = elements.map(el => ({
			x: el.x,
			y: el.y,
			r: 10,
			symbol: el.symbol,
			name: el.name
			}));

			const backgroundColors = elements.map(() => defaultColor);

			const ctx = document.getElementById('periodicChart').getContext('2d');

			const bubbleDataset = {
			label: 'Elements',
			data,
			backgroundColor: backgroundColors
			};

			const chart = new Chart(ctx, {
			type: 'bubble',
			data: {
				labels: elements.map(e => e.symbol),
				datasets: [bubbleDataset],
			},
			options: {
				plugins: {
					tooltip: 
					{ 
						enabled: false 
					},
					legend:
					{
						display: false
					},
				},
				scales: 
				{
					x: 
					{
						min: 0,
						max: 19,
						display:false,
						ticks: { display: false },
						grid: { drawTicks: false, drawBorder: false }
					},
					y: 
					{
						min: 0,
						max: 11,
						display:false,
						ticks: { display: false },
						grid: { drawTicks: false, drawBorder: false },
						reverse: true
					}
				},
				responsive: false,
				maintainAspectRatio: false
			},
			plugins: [hoverHighlightPlugin, rectanglePointPlugin]
			});

			// Save molecule map on chart instance for plugin access
			chart.moleculeMap = moleculeMap;
		})
		.catch(err => console.error('Failed to load molecule data:', err));


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
	
	<div style="width:100%; margin-top:30px;">
		
		
		<div class="placeholder_search">
			<h1>Search in the database</h1>
			<div class="search_container_main">
				<form action="search_data.php" method="GET">
					
					<input type="text" placeholder="Try a molecule (e.g. AlF)..." name="query" id="input_query" style="font-size: 16px; font-family:'Times New Roman', Times, serif; width:220px;">
					
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
		die('Error: cannot read data: '  .$sql. mysqli_error($conn));
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
				
				
				<form method="post" action="export_table.php" class="row">
					<input type="submit" value="Download the whole dataset" name="export" class="button" />
				</form>
				
			</div>
		</div>
		
	</div>
	<br><br>
	
	<div style="height:100px;width:100%">
	</div>
	<div style="height:200px;width:100%">
		
	</div>




</div>







<?php 
	include('foot.php');
?>
