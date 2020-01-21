function download_table_as_csv(table_id) {
	// By Calumah@Stackoverflow: https://stackoverflow.com/questions/15547198/export-html-table-to-csv
    // Select rows from table_id
    //alert("download: "+table_id);
    var rows = document.querySelectorAll('table#' + table_id + ' tr');
    // Construct csv
    var csv = [];
    row = ['Molecule', 'Electronic state', 'Mass (au)', 'Te (cm^{-1})', '\Omega_e (cm^{-1})', '\Omega_ex_e (cm^{-1})', 'B_e (cm^{-1})', '\alpha_e (cm^{-1})', 'D_e (10^{-7}cm^{-1})', 'R_e (\AA)', 'D_0 (eV)', 'IP (eV)', 'Date of reference'];
    csv.push(row.join(','));
    for (var i = 1; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        for (var j = 0; j < cols.length; j++) {
            // Clean innertext to remove multiple spaces and jumpline (break csv)
            var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ')
            // Escape double-quote with double-double-quote (see https://stackoverflow.com/questions/17808511/properly-escape-a-double-quote-in-csv)
            data = data.replace(/"/g, '""');
            data = data.replace('\(', '$');
            data = data.replace('\)', '$');
            // Push escaped string
            row.push('' + data + '');
        }
        csv.push(row.join(','));
    }
    //alert("write csv");
    var csv_string = csv.join('\n');
    // Download it
    var filename = 'export_' + table_id + '_' + new Date().toLocaleDateString() + '.csv';
    var link = document.createElement('a');
    link.style.display = 'none';
    link.setAttribute('target', '_blank');
    link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
