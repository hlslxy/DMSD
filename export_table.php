<?php
	// Connect to database
	include('connect.php');
	mysqli_select_db($conn, 'rios');
	
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=moleculedata.csv');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	
	$output = fopen("php://output", "w");  
	fputcsv($output, array('Molecule', 'Electronic state', 'Reduced mass', 'Te (cm^{-1})', 'omega_e (cm^{-1})','omega_ex_e (cm^{-1})','Be (cm^{-1})','alpha_e (cm^{-1})','De (10^{-7}cm^{-1})','Re (\AA)','D0 (eV)','IP (eV)','Reference','Date of reference'));  
	
	$sql = "select Molecule,State,Mass,Te,omega_e,omega_ex_e,Be,alpha_e,De,Re,D0,IP,reference,reference_date from molecule_data";  
	$retval = mysqli_query($conn, $sql);  
	if(! $retval)
	{
		die('Error: cannot read data: '  . mysqli_error($conn));
	}	
	while($row = mysqli_fetch_assoc($retval))  
	{  
		$row['reference'] = '"'.$row['reference'].'"';
		fputcsv($output, $row);  
	}  
	fclose($output);  
	
	// Free memory
	mysqli_free_result($retval);

	mysqli_close($conn);
?>
