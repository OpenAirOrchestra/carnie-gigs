<?php

/*
 * This class handles simple rendering the form for exporting to CSV.
 */
class carnieCsvExportView {

	/*
	 * Given a record, render a form for exporting the
	 * table in csv form
	 */
	function exportForm ($gig) {
		$action = carnieUtil::get_url() . "csv.php"
?>
		<form name="export_csv_form"
			method="POST"
<?php
		echo 'action="' . $action . '">';
		print "\n";
		foreach ($gig as $fieldname=>$field)
		{
			echo '<input type="checkbox" name="' . $fieldname .
				'" value="yes" checked="checked" /> ' . $fieldname . '<br/>';
			print "\n";
		}
		print "</form>\n";
	}
}
?>
