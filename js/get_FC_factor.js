function calculate_FC_factor(n, omega_e, omega_ex_e, Re)
//function calculate_FC_factor(n, omega_e)
{
	var FC = n+omega_e+omega_ex_e+Re;
	FC = FC.toFixed(3);
	//document.cookie = 'FC_value='+FC.toString();  
	document.getElementById("FC_factor").innerHTML = FC.toString();
}
