<?php include("head.php"); ?>

<!-------------------
	About the API
-------------------->
<style>
tr,td,table {
	vertical-align: top;
    text-align: left;
}

</style>

<!---------------
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
---------------->



<div style="margin-left:20px">
	<h2>APIs Explorer</h2>
</div>


<script>
	function show_query_result(query_command)
	{
		/*
			Get the query results and show in the iframe
		*/
		document.getElementById("div_result").style.visibility = "visible";
		document.getElementById("iframe_result").src=query_command;
		document.getElementById("input_query_command").value = query_command;
	}

</script>

<!--------------------div for the query result---------------------->	

<div id="div_result" style="position:fixed; top:10%; left:10%; width:80%; height:80%; background-color:rgba(251, 253, 252,0.95); box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1); visibility:hidden;" >

	<div style="float:left;text-align:right;width:90%;height:30px;top:5px;">
		<div style="float:right;width:100%;margin-top:10px">
			<button class="button" onclick='document.getElementById("div_result").style.visibility = "hidden";'>CLOSE</button>
		</div> 
	</div>

	<br>
	<div id="div_query_command" style="width:100%;">
		<br><br><b>&nbsp;&nbsp;URL</b>: &nbsp;
		<input type="text" id="input_query_command" style="width: 60%">
		<button class="button" onclick='show_query_result(getElementById("input_query_command").value);'>Get</button>
		<br>
	</div>
	<div style="width:100%; height:90%; margin-top:10px;">
		<b>&nbsp;&nbsp;Results:</b><br>
		<iframe id="iframe_result" style="margin-top:20px; margin-left:2%; width:95%; height:85%"></iframe>
	</div>
</div>

<!------------Main div----------------------------->
<div style="margin-left:50px; margin-top: 20px;">


	<table style="border-bottom:1px solid #777; border-collapse:collapse;">
		<tr style="height: 40px; text-align: left;">
			<th style="width:30%;  margin-right: 30px; border-bottom-style: solid; border-width:thin;">
				<p style="color: #000;font-size: 20px; font-family: Times, 'Times New Roman', serif; font-weight: 350;">Description</p>
			</th>
			<th style="width: 4%; border-bottom-style: solid; border-width:thin;"></th>
			<th style="width: 66%; border-bottom-style: solid; border-width:thin;">
				<p  style="color: #000;font-size: 20px; font-family: Times, 'Times New Roman', serif; font-weight: 350;">Return</p>
			</th>
		</tr>


		<tr>
			<td>
				
				<h3> List of molecules in the database</h3>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<p>/api/?query=list_molecules</p> 
				
				<button class="button" onclick="show_query_result('api/?query=list_molecules');">Try</button>
				
				<br>
				<br>
				<p>View the list of all the molecules in the database.</p>	
			</td>
			<td></td>
			<td>
				<p>A json object containing a list of molecules in the database.</p>
				<pre><code>
{
	"accessed": STRING. Time of access, with the format of "date-and-time",
	"n_records": INT. Number of records (molecules).	
	"data":
	[						
		{		
			"id_molecule": INT. A unique ID of the molecule,
			"chemical_formula": STRING. The chemical formula of the molecule.
		},
		...
	]
}
				</code></pre>
	
				<p>Example (/api/?query=list_molecules):</p>
				<pre>
					<code>
{
	"accessed":"2019-10-23 10:19:51",
	"n_records":179,	
	"data":
	[						
		{		
			"id_molecule":1,
			"chemical_formula":"AgAl"
		},
		{
			"id_molecule":2,
			"chemical_formula":"AgBi"
		},
		...
	]
}
					</code>
				</pre>
				
			</td>
		</tr>
		
		

		<tr>
			<td>
				<h3> Query for a spectroscopy constant </h3>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<p>/api/?query=name_of_spectroscopy_constant</p>
				<br>
				<p>Example: </p>
				<p>/api/?query=Te</p>
				
				
				<button class="button" onclick="show_query_result('/api/?query=Te');">Try</button>
				
				<br>
				<p>Get a spectroscopy constant of the ground and excited states of all the molecules having the information of this constant in the database. </p>	
				
				<p>Supported spectroscopy constants:</p>
				<div style="margin-left:20px;">
					<p>$T_e$: Te   </p>
					<p>$\omega_e$:  omega_e   </p>
					<p>$\omega_e x_e$:  omega_ex_e   </p>
					<p>$B_e$:  Be   </p>
					<p>$\alpha_e$:  alpha_e   </p>
					<p>$D_e$:  De   </p>
					<p>$R_e$:  Re   </p>
					<p>$D_0$:  D0   </p>
					<p>IP:  IP</p>
				</div>
				
				
			</td>
			
			<td></td>
			<td>
				<p>A json object containing the queried spectroscopy constant of the ground and excited states of all the molecules in the database. The information about the molecules (chemical formula), their states (in Latex) and masses (in the a.u. unit) are also given.
				</p>
				
				<pre><code>
{
	"accessed": STRING. Time of access, with the format of "date-and-time",
	"n_records": INT. Number of records (molecules).	
	"data":
	[						
		{		
			"Reference_date": STRING. Date of the reference where the 
			    spectroscopy constant was given, with the format of "Month Year".
			"id_record": INT. A unique ID of the record.
			"id_molecule": INT. A unique ID of the molecule,
			"chemical_formula": STRING. The chemical formula of the molecule.
			"state": STRING. The state symbol, in the Latex format.
			"mass": FLOAT. Mass of the molecule in the corresponding state, in the a.u. unit.
			"name_of_spectroscopy_constant": FLOAT. The value of the queried 
			    spectroscopy constant.
		},
		...
	]
}
				</code></pre>
	
				<p>Example (api/?query=omega_ex_e):</p>
				<pre>
					<code>
{
	"accessed":"2019-10-22 20:54:55",
	"n_records":562,
	"data":
	[
		{
			"Reference_date":"OCT 1974",
			"id_record":1,
			"id_molecule":1,
			"chemical_formula":"AgAl",
			"state":"X $^1\\Sigma^+$",
			"mass":0,
			"omega_ex_e":"1.13"},
		...
	]
}
					</code>
				</pre>
				
			</td>
		</tr>
		




		

		<tr>
			<td>
				<h3> Query for a spectroscopy constant of a given molecule</h3>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<p>/api/?chemical_formula=chemical_formula&query=name_of_spectroscopy_constant</p>
				<br>
				<p>Example: </p>
				<p>api/?chemical_formula=AlF&query=Be</p>
				
				
				<button class="button" onclick="show_query_result('/api/?chemical_formula=AlF&query=Be');">Try</button>
				
				
				<br>
				<p>Get a spectroscopy constant of the ground and excited states of a given molecule.</p>	
				
				<p>Supported spectroscopy constants:</p>
				<div style="margin-left:20px;">
					<p>$T_e$: Te   </p>
					<p>$\omega_e$:  omega_e   </p>
					<p>$\omega_e x_e$:  omega_ex_e   </p>
					<p>$B_e$:  Be   </p>
					<p>$\alpha_e$:  alpha_e   </p>
					<p>$D_e$:  De   </p>
					<p>$R_e$:  Re   </p>
					<p>$D_0$:  D0   </p>
					<p>IP:  IP</p>
				</div>
				
				
			</td>
			
			
			<td></td>
			
			<td>
				<p>A json object containing of a given molecule. When "name_of_spectroscopy_constant" is undefined (e.g. api/?chemical_formula=AlF, or api/?chemical_formula=AlF&query=), the query returns all the spectroscopy constants of the given molecule. The information about the molecules (chemical formula), their states (in Latex) and masses (in the a.u. unit) are also given.
				</p>
				
				<pre><code>
{
	"accessed": STRING. Time of access, with the format of "date-and-time",
	"id_molecule": INT. A unique ID of the molecule,
	"chemical_formula": STRING. The chemical formula of the molecule.
	"n_records": INT. Number of records (molecules).	
	"data":
	[						
		{		
			"Reference_date": STRING. Date of the reference where the 
			    spectroscopy constant was given, with the format of "Month Year".
			"id_record": INT. A unique ID of the record.
			"state": STRING. The state symbol, in the Latex format.
			"mass": FLOAT. Mass of the molecule in the corresponding state, in the a.u. unit.
			"name_of_spectroscopy_constant": FLOAT. The value of the queried
			    spectroscopy constant.
		},
		...
	]
}
				</code></pre>
	
				<p>Example (api/?chemical_formula=AlF&query=Be):</p>
				<pre>
					<code>
{
	"accessed":"2019-10-23 11:25:14",
	"id_molecule":11,
	"chemical_formula":"AlF",
	"n_records":3,
	"data":
	[
		{
			"Reference_date":"MAR 1976",
			"id_record":33,
			"state":"X $^1\\Sigma^+$",
			"mass":20322.423999999999,
			"Be":"0.55248"
		},
		......
	]
}
					</code>
				</pre>
				
			</td>
		</tr>
		
	
		
		
	</table>

</div>


</body>
</html>
