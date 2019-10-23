<?php

	/*
	 * Possible query keywords:
	 * 		?query=name_of_property
	 * 			name_of_property must be in ['Te', 'omega_e', 'omega_ex_e', 'Be', 'alpha_e', 'De', 'Re', 'D0', 'IP']
	 * 
	 * 			return: all the molecules with corresponding information (e.g. 'Te') about their states
	 * 
	 * 
	 * 		?query=list_molecules
	 * 			return: list of molecules in the database
	 * 
	 * 
	 * 		?chemical_formula='AA'
	 * 			return: all information about AA
	 * 
	 * 
	 * 		?chemical_formula='AA'&query='name_of_parameter'
	 * 			return: parameter of molecule AA
	 */


	function query_chemical_formula($chemical_formula, $query_keyword)
	{
		/*
		 * function query_chemical_formula($chemical_formula, $query_keyword)
		 *     returns a json object that contains information of $query_keyword of molecule with $chemical_formula.
		 * 
		 * $chemical_formula: string
		 * $query_keyword: string, 'all' or in ['Te', 'omega_e', 'omega_ex_e', 'Be', 'alpha_e', 'De', 'Re', 'D0', 'IP']
		 * 
		 */
		// Connect to database
		include('../connect.php');
	
		// Read database

		$sql = 'SELECT * from molecule_data WHERE BINARY Molecule="'.$chemical_formula.'"';
		////echo "<p>".$sql."</p>";
		mysqli_select_db($conn, 'molecule_database');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}

		// Get the number of query results
		$N_results = $retval->num_rows;
		
		
		if($N_results < 1)
		{
			die('No results found for "'.$chemical_formula.'". Please check the chemical formula.');
		}

		// Get the results
		$idAll_in = array(); 
		$idMol = array();
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
			array_push($idAll_in, $row['idAll_in']);
			array_push($idMol, $row['idMol']);
			array_push($molecules, $row['Molecule']);
			array_push($states, $row['State']);
			array_push($masses, $mass_au);
			array_push($Te, $row['Te']);
			array_push($omega_e, $row['omega_e']);
			array_push($omega_ex_e, $row['omega_ex_e']);
			array_push($Be, $row['Be']);
			array_push($alpha_e, $row['alpha_e']);
			array_push($De, $row['De']);
			array_push($Re, $row['Re']);
			array_push($D0, $row['D0']);
			array_push($IPs, $row['IP']);
			array_push($dates, $row['reference_date']);
		}
		
		
		// Output the results
		
		$accessed_date = date('Y-m-d H:i:s');
		$id_molecule = (int)$idMol[0];
		$output_obj = array('id_molecule' => $id_molecule, 'chemical_formula' => $chemical_formula, 'accessed' => $accessed_date, 'n_records' => $N_results);
		
		
		$output_obj_main = array();
		
		/*
		if($query_keyword == 'states')
		{
			$output_obj_main['states'] = $states;
		}
		*/
		
		//die("query_keyword=".$query_keyword.";if query_keyword == omega_ex_e:".($query_keyword=='Te'));
		
		for($i = 0; $i < $N_results; $i++)
		{
			
			$output_obj_i = array();
			$output_obj_i['Reference_date'] = $dates[$i];
			$output_obj_i['id_record'] = (int)$idAll_in[$i];
			$output_obj_i['state'] = $states[$i];
			$output_obj_i['mass'] = $masses[$i];
			
			
			
			if(($query_keyword == 'Te') || ($query_keyword == 'all'))
			{ 
				$output_obj_i['Te'] = $Te[$i];
			}
			if(($query_keyword == 'omega_e') || ($query_keyword == 'all'))
			{
				$output_obj_i['omega_e'] = $omega_e[$i];
			}
			if(($query_keyword == 'omega_ex_e') || ($query_keyword == 'all'))
			{
				$output_obj_i['omega_ex_e'] = $omega_ex_e[$i];
			}
			if(($query_keyword == 'Be') || ($query_keyword == 'all'))
			{
				$output_obj_i['Be'] = $Be[$i];
			}
			if(($query_keyword == 'alpha_e') || ($query_keyword == 'all'))
			{
				$output_obj_i['alpha_e'] = $alpha_e[$i];
			}
			if(($query_keyword == 'De') || ($query_keyword == 'all'))
			{
				$output_obj_i['De'] = $De[$i];
			}
			if(($query_keyword == 'Re') || ($query_keyword == 'all'))
			{
				$output_obj_i['Re'] = $Re[$i];
			}
			if(($query_keyword == 'D0') || ($query_keyword == 'all'))
			{
				$output_obj_i['D0'] = $D0[$i];
			}
			if(($query_keyword == 'IP') || ($query_keyword == 'all'))
			{
				$output_obj_i['IP'] = $IPs[$i];
			}
			
			
			
			array_push($output_obj_main, $output_obj_i);
		}

		$output_obj['data'] = $output_obj_main;
		

		// Free memory
		mysqli_free_result($retval);

		mysqli_close($conn);	
		
		return($output_obj);
	}
	
	
	function query_list_molecules()
	{
		/*
		 * function query_list_molecules()
		 * 
		 *     returns a json object that contains a list of all the molecules in the database
		 */
		
		// Connect to database
		include('../connect.php');
	
		// Read database

		$sql = 'SELECT DISTINCT idMol,Molecule from molecule_data';
		mysqli_select_db($conn, 'molecule_database');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}

		// Get the number of query results
		$N_results = $retval->num_rows;
		
		
		if($N_results < 1)
		{
			die('No record found.');
		}
		
		// Read data
		$idMol = array();
		$molecules = array();
		
		while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC))
		{
			array_push($idMol, $row['idMol']);
			array_push($molecules, $row['Molecule']);
		}
		
		// Output data
		
		$accessed_date = date('Y-m-d H:i:s');
		$output_obj = array('accessed' => $accessed_date, 'n_records' => $N_results);
		$output_obj_main = array();
		for($i = 0; $i < $N_results; $i ++)
		{
			$output_obj_i = array();
			$output_obj_i['id_molecule'] = (int)$idMol[$i];
			$output_obj_i['chemical_formula'] = $molecules[$i];
			array_push($output_obj_main, $output_obj_i);
		}
		$output_obj["data"] = $output_obj_main;
		
		
		// Free memory
		mysqli_free_result($retval);
		mysqli_close($conn);	
		
		return $output_obj;
	}

	
	function query_property_all_molecules($query_keyword)
	{
		/*
		 * function query_property_all_molecules($query_keyword)
		 * 
		 *     Returns all the molecules containing the $query_keyword property.
		 * 
		 * $query_keyword: string, in ['Te', 'omega_e', 'omega_ex_e', 'Be', 'alpha_e', 'De', 'Re', 'D0', 'IP'].
		 */
		 
		// Connect to database
		include('../connect.php');
	
		// Read database: skip the "NULL" values

		$sql = 'SELECT idAll_in,reference_date,idMol,Molecule,State,mass,'.$query_keyword.' from molecule_data WHERE '.$query_keyword.' IS NOT NULL';
		mysqli_select_db($conn, 'molecule_database');
		$retval = mysqli_query($conn, $sql);
		if(! $retval)
		{
			die('Error: can not read data: '  . mysqli_error($conn));
		}

		// Get the number of query results
		$N_results = $retval->num_rows;
		
		
		if($N_results < 1)
		{
			die('No record found.');
		}
		
		// Read data
		$idAll_in = array(); 
		$idMol = array();
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
			
			array_push($idAll_in, $row['idAll_in']);
			array_push($idMol, $row['idMol']);
			array_push($molecules, $row['Molecule']);
			array_push($states, $row['State']);
			array_push($masses, $mass_au);
			array_push($Te, $row['Te']);
			array_push($omega_e, $row['omega_e']);
			array_push($omega_ex_e, $row['omega_ex_e']);
			array_push($Be, $row['Be']);
			array_push($alpha_e, $row['alpha_e']);
			array_push($De, $row['De']);
			array_push($Re, $row['Re']);
			array_push($D0, $row['D0']);
			array_push($IPs, $row['IP']);
			array_push($dates, $row['reference_date']);
		}
		
		
		
		// Output data
		
		$accessed_date = date('Y-m-d H:i:s');
		$output_obj = array('accessed' => $accessed_date, 'n_records' => $N_results);
		$output_obj_main = array();

		for($i = 0; $i < $N_results; $i++)
		{
			
			$output_obj_i = array();
			$output_obj_i['Reference_date'] = $dates[$i];
			$output_obj_i['id_record'] = (int)$idAll_in[$i];
			$output_obj_i['chemical_formula'] = $molecules[$i];
			$output_obj_i['state'] = $states[$i];
			$output_obj_i['mass'] = $masses[$i];
			
			if($query_keyword == 'Te')
			{ 
				$output_obj_i['Te'] = $Te[$i];
			}
			if($query_keyword == 'omega_e')
			{
				$output_obj_i['omega_e'] = $omega_e[$i];
			}
			if($query_keyword == 'omega_ex_e')
			{
				$output_obj_i['omega_ex_e'] = $omega_ex_e[$i];
			}
			if($query_keyword == 'Be')
			{
				$output_obj_i['Be'] = $Be[$i];
			}
			if($query_keyword == 'alpha_e')
			{
				$output_obj_i['alpha_e'] = $alpha_e[$i];
			}
			if($query_keyword == 'De')
			{
				$output_obj_i['De'] = $De[$i];
			}
			if($query_keyword == 'Re')
			{
				$output_obj_i['Re'] = $Re[$i];
			}
			if($query_keyword == 'D0')
			{
				$output_obj_i['D0'] = $D0[$i];
			}
			if($query_keyword == 'IP')
			{
				$output_obj_i['IP'] = $IPs[$i];
			}
			array_push($output_obj_main, $output_obj_i);
		}

		$output_obj['data'] = $output_obj_main;
		
		// Free memory
		mysqli_free_result($retval);
		mysqli_close($conn);	
		
		return $output_obj;		 
		 
	}






	//======================main========================

	// Get the query keywords
	
	
	$keyword_properties = array('Te', 'omega_e', 'omega_ex_e', 'Be', 'alpha_e', 'De', 'Re', 'D0', 'IP');
	
	$if_set_chemical_formula = false;
	$if_set_query_list_molecules = false;
	$if_set_query = false;
	$if_set_query_chemical_formula = false;
	
	
	if(isset($_GET['chemical_formula']))
	{
		$if_set_chemical_formula = true;
		$chemical_formula = $_GET['chemical_formula'];
	}
	if(isset($_GET['query']))
	{
		$if_set_query = true;
		$query_keyword = $_GET['query'];
	}
	else
	{
		if($if_set_chemical_formula)
		{
			$query_keyword = 'all';
		}
		else
		{
			die('Error: please input the query keyword.');
		}
	}
	if(($if_set_chemical_formula) and ($query_keyword == ''))
	{
		$query_keyword = 'all';
	}
	
	if($query_keyword == 'list_molecules')
	{
		$if_set_query_list_molecules = true;
	}
	
	
	if((!$if_set_chemical_formula) and (!$if_set_query) and (!$if_set_query_list_molecules))
	{
		die("Error: Please input '?query=list_of_molecules' to get a list of all the molecules in the datbase, or '?query=name_of_property', e.g. '?query=Te', or a chemical formula as keyword (e.g. ?chemical_formula='HF').");
	}
	

	//die($chemical_formula.$query_keyword);
	
	

	if($if_set_chemical_formula)
	{
		if(strlen($chemical_formula)<1)
		{
			die('Error: Please check the chemical formula.');
		}
		else
		{
			$output_obj = query_chemical_formula($chemical_formula, $query_keyword);
		}
	}
	
	if((!$if_set_chemical_formula) and (!$if_set_query_list_molecules))
	{
		if(in_array($query_keyword, $keyword_properties))
		{
			$output_obj = query_property_all_molecules($query_keyword);
		}
		else
		{
			die("Error: Please check the query keyword.");
		}
	}

	if($if_set_query_list_molecules)
	{
		$output_obj = query_list_molecules();
	}
	
	exit(json_encode($output_obj));
		


?>

