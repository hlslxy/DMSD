<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('connect.php');



    $atomicMasses = [
        "H" => 1.00784,
        "D" => 2.01410,
        "T" => 3.01605,
        "He" => 4.0026,
        "Li" => 6.94,
        "Be" => 9.0122,
        "B"  => 10.81,
        "C"  => 12.011,
        "N"  => 14.007,
        "O"  => 15.999,
        "F"  => 18.998,
        "Ne" => 20.180,
        "Na" => 22.990,
        "Mg" => 24.305,
        "Al" => 26.982,
        "Si" => 28.085,
        "P"  => 30.974,
        "S"  => 32.06,
        "Cl" => 35.45,
        "Ar" => 39.948,
        "K"  => 39.098,
        "Ca" => 40.078,
        "Sc" => 44.956,
        "Ti" => 47.867,
        "V"  => 50.942,
        "Cr" => 51.996,
        "Mn" => 54.938,
        "Fe" => 55.845,
        "Co" => 58.933,
        "Ni" => 58.693,
        "Cu" => 63.546,
        "Zn" => 65.38,
        "Ga" => 69.723,
        "Ge" => 72.630,
        "As" => 74.922,
        "Se" => 78.971,
        "Br" => 79.904,
        "Kr" => 83.798,
        "Rb" => 85.468,
        "Sr" => 87.62,
        "Y"  => 88.906,
        "Zr" => 91.224,
        "Nb" => 92.906,
        "Mo" => 95.95,
        "Tc" => 98,
        "Ru" => 101.07,
        "Rh" => 102.91,
        "Pd" => 106.42,
        "Ag" => 107.87,
        "Cd" => 112.41,
        "In" => 114.82,
        "Sn" => 118.71,
        "Sb" => 121.76,
        "Te" => 127.60,
        "I"  => 126.90,
        "Xe" => 131.29,
        "Cs" => 132.91,
        "Ba" => 137.33,
        "La" => 138.91,
        "Ce" => 140.12,
        "Pr" => 140.91,
        "Nd" => 144.24,
        "Pm" => 145,
        "Sm" => 150.36,
        "Eu" => 151.96,
        "Gd" => 157.25,
        "Tb" => 158.93,
        "Dy" => 162.50,
        "Ho" => 164.93,
        "Er" => 167.26,
        "Tm" => 168.93,
        "Yb" => 173.05,
        "Lu" => 174.97,
        "Hf" => 178.49,
        "Ta" => 180.95,
        "W"  => 183.84,
        "Re" => 186.21,
        "Os" => 190.23,
        "Ir" => 192.22,
        "Pt" => 195.08,
        "Au" => 196.97,
        "Hg" => 200.59,
        "Tl" => 204.38,
        "Pb" => 207.2,
        "Bi" => 208.98,
        "Po" => 209,
        "At" => 210,
        "Rn" => 222,
        "Fr" => 223,
        "Ra" => 226,
        "Ac" => 227,
        "Th" => 232.04,
        "Pa" => 231.04,
        "U"  => 238.03,
        "Np" => 237,
        "Pu" => 244,
        "Am" => 243,
        "Cm" => 247,
        "Bk" => 247,
        "Cf" => 251,
        "Es" => 252,
        "Fm" => 257,
        "Md" => 258,
        "No" => 259,
        "Lr" => 266,
        "Rf" => 267,
        "Db" => 268,
        "Sg" => 269,
        "Bh" => 270,
        "Hs" => 277,
        "Mt" => 278,
        "Ds" => 281,
        "Rg" => 282,
        "Cn" => 285,
        "Fl" => 289,
        "Lv" => 293,
        "Ts" => 294,
        "Og" => 294
    ];

    $list_MOT = ['AlF', 'CaF', 'YO', 'SrF', 'BaF', 'YbF'];
    $list_may_MOT = ['AlCl', 'CuF', 'TlF', 'CaH', 'MgF', 'NH', 'RaF', 'BaH', 'CH'];

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


    
    mysqli_select_db($conn, 'rios');


    function fetch_data_by_state($conn, $stateFilter, $atomicMasses, $list_MOT, $list_may_MOT) {
        $data = [];
        $sql = "SELECT * FROM molecule_data WHERE State LIKE '%X%$stateFilter%' AND D0 IS NOT NULL";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            die("Query failed for state $stateFilter: " . mysqli_error($conn));
        }

        // Store all molecules in array
        // TODO: Handle multiple records for the same molecule

        while ($row = mysqli_fetch_assoc($result)) {
            $state = replace_latex($row['State']);
            $D0 = $row['D0'];
            $molecule = $row['Molecule'];
            $IP = $row['IP'];
            // Check if it's null
            if (is_null($IP) || !is_numeric($IP)) {
                $IP = 9999;
            } else {
                $IP = floatval($IP);
            }
            // Extract elements from molecule
            $elements = [];
            $tmp = '';
            for ($i = 0; $i < strlen($molecule); $i++) {
                $ch = $molecule[$i];
                if (ctype_upper($ch)) {
                    $tmp = $ch;
                    $elements[] = $tmp;
                } elseif (ctype_lower($ch)) {
                    $tmp .= $ch;
                    $elements[count($elements)-1] = $tmp;
                }
            }

            $mass = 0.0;
            foreach ($elements as $element) {
                $mass += $atomicMasses[$element] ?? 0.0;
            }

            $color = in_array($molecule, $list_MOT) ? 'rgba(21, 134, 103,1)' :
                (in_array($molecule, $list_may_MOT) ? 'rgba(204, 148, 0, 1)' : 'rgba(0,0,0,0.3)');
            
            $marker = "circle";
            if ($IP < $D0) 
            {
                $marker = "triangle";
                $D0 = $IP;
            }
            $data[] = [
                "mass" => $mass,
                "D0" => $D0,
                "state" => $state,
                "molecule" => $molecule,
                "color" => $color,
                "marker" => $marker
            ];
        }

        mysqli_free_result($result);
        return $data;
    }


    // Fetch categorized data
    $data_D0_1Sigma = fetch_data_by_state($conn, '^1%Sigma', $atomicMasses, $list_MOT, $list_may_MOT);
    $data_D0_2Sigma = fetch_data_by_state($conn, '^2%Sigma', $atomicMasses, $list_MOT, $list_may_MOT);
    $data_D0_3Sigma = fetch_data_by_state($conn, '^3%Sigma', $atomicMasses, $list_MOT, $list_may_MOT);
    $data_D0_Pi    = fetch_data_by_state($conn, 'Pi', $atomicMasses, $list_MOT, $list_may_MOT);
    $data_D0_Delta = fetch_data_by_state($conn, 'Delta', $atomicMasses, $list_MOT, $list_may_MOT);
    mysqli_close($conn);

    // Return combined JSON
    $data_D0_Pi_Delta = array_merge($data_D0_Pi, $data_D0_Delta);
    header('Content-Type: application/json');
    echo json_encode([
    'data_D0_1Sigma' => $data_D0_1Sigma,
    'data_D0_2Sigma' => $data_D0_2Sigma,
    'data_D0_3Sigma' => $data_D0_3Sigma,
    'data_D0_Pi_Delta' => $data_D0_Pi_Delta,
    ]);









?>

