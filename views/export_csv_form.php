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
		<h3>Fields</h3>
		<form name="export_csv_form"
			method="POST"
<?php
		echo 'action="' . $action . '">';
		echo '<input type="hidden" name="carnie-gigs-csv-verify-key" id="carnie-gigs-csv-verify-key"
						value="' . wp_create_nonce('carnie-gigs') . '" />';
		print '<table class="form-table">';
		foreach ($gig as $fieldname=>$field)
		{
			print "<tr>";
			echo "<td><label>" . $fieldname . "</label></td>";
			echo "<td>";
			echo '<input type="checkbox" name="' . $fieldname .
				'" value="yes" checked="checked" /> ';
			echo "</td>";
			print "</tr>";
		}
		print "</td></tr></table>\n";
?>
		<p class="submit"><input type="submit" name="submit" class="button" value="Download Export File" />
<?php

		print "</form>\n";
	}

	function exportAttendanceForm() {

		$action = carnieUtil::get_url() . "attendance_csv.php"
?>
		<form name="export_csv_form"
			method="POST"
<?php
		echo 'action="' . $action . '">';
		echo '<input type="hidden" name="carnie-gigs-attendance-csv-verify-key" id="carnie-gigs-attendance-csv-verify-key"
						value="' . wp_create_nonce('carnie-gigs-attendance') . '" />';
?>
		<p class="submit"><input type="submit" name="submit" class="button" value="Download Exported Attendance File" />
<?php

		print "</form>\n";
	}

	function exportVerifiedAttendanceForm() {
		$action = carnieUtil::get_url() . "verified_attendance_csv.php"
?>
		<form name="export_csv_form"
			method="POST"
<?php
		echo 'action="' . $action . '">';
		echo '<input type="hidden" name="verified-attendance-csv-verify-key" id="verified-attendance-csv-verify-key"
						value="' . wp_create_nonce('verified-attendance') . '" />';
?>
		<p class="submit"><input type="submit" name="submit" class="button" value="Download Exported Verified Attendance File" />
<?php

		print "</form>\n";
	}
}
?>
