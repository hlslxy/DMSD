<?php
include('connect.php');



mysqli_select_db($conn, 'rios');

// Get all distinct molecules
$sql = 'select Distinct Molecule from molecule_data';
$retval = mysqli_query($conn, $sql);

if (!$retval) {
    die('Error: cannot read data: ' . mysqli_error($conn));
}



// Store all molecules in array
$molecules = array();
while ($row = mysqli_fetch_assoc($retval)) {
    array_push($molecules, $row['Molecule']);
}

// Define known elements (can be dynamic too)
$element_symbols = [
    "H",  "He", "Li", "Be", "B",  "C",  "N",  "O",  "F",  "Ne",
    "Na", "Mg", "Al", "Si", "P",  "S",  "Cl", "Ar", "K",  "Ca",
    "Sc", "Ti", "V",  "Cr", "Mn", "Fe", "Co", "Ni", "Cu", "Zn",
    "Ga", "Ge", "As", "Se", "Br", "Kr", "Rb", "Sr", "Y",  "Zr",
    "Nb", "Mo", "Tc", "Ru", "Rh", "Pd", "Ag", "Cd", "In", "Sn",
    "Sb", "Te", "I",  "Xe", "Cs", "Ba", "La", "Ce", "Pr", "Nd",
    "Pm", "Sm", "Eu", "Gd", "Tb", "Dy", "Ho", "Er", "Tm", "Yb",
    "Lu", "Hf", "Ta", "W",  "Re", "Os", "Ir", "Pt", "Au", "Hg",
    "Tl", "Pb", "Bi", "Po", "At", "Rn", "Fr", "Ra", "Ac", "Th",
    "Pa", "U",  "Np", "Pu", "Am", "Cm", "Bk", "Cf", "Es", "Fm",
    "Md", "No", "Lr", "Rf", "Db", "Sg", "Bh", "Hs", "Mt", "Ds",
    "Rg", "Cn", "Nh", "Fl", "Mc", "Lv", "Ts", "Og"
];

// Build element => [molecules...] map
$elementMap = [];

foreach ($element_symbols as $symbol) {
    $elementMap[$symbol] = array(); // Initialize empty array

    foreach ($molecules as $molecule) {
        
        // Check if the molecule contains the element symbol as a substring (case-sensitive)
        // More robust parsing may be needed if e.g., "C" in "Ca" is a false match
        //if (preg_match('/\b' . preg_quote($symbol) . '\b/', $molecule)) {
        //    $elementMap[$symbol][] = $molecule;
        //}
        //Find the upper letters in the chemical formula
        $elements = [];
        $tmp_element = '';
        $N_elements = 0;
        for ($i =0; $i < strlen($molecule); $i++)
        {
            $letter = substr($molecule,$i,1);
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
        if (in_array($symbol, $elements)) {
            array_push($elementMap[$symbol], $molecule);
     
        }
    }
}



// Return as JSON
header('Content-Type: application/json');
echo json_encode($elementMap);
?>

