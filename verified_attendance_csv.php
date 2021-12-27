<?php

/*
 * This php is called directly with POST to get verified attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'views/csv.php';

global $wpdb;
global $current_user;

$Filename = "VerifiedAttendance.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if (wp_verify_nonce($_POST['verified-attendance-csv-verify-key'], 'verified-attendance')) {

	$to = $_POST['to'];
	$from = $_POST['from'];

	// transform dates.
	$time = strtotime($from);
	if ($time > 0) {
		$from = date('Y-m-d', $time);
	} else {
		$from = "0000-00-00";
	}
	$time = strtotime($to);
	if ($time > 0) {
		$to = date('Y-m-d', $time);
	} else {
		$to = date('Y-m-d');
	}

	$gig_table = get_option('carniegigs_mirror_table');
	$attendance_table = $wpdb->prefix . "gig_attendance";

	$sql = $wpdb->prepare("
	SELECT $gig_table.date, $attendance_table . *, $gig_table.categories
	FROM  `$gig_table` ,  `$attendance_table` 
	WHERE $gig_table.gigid = $attendance_table.gigid
	AND
	$gig_table.date >= %s AND $gig_table.date <= %s 
		ORDER BY $gig_table.date
	", $from, $to);

	$results = $wpdb->get_results($sql, ARRAY_A);
	
	$separator = "";

	foreach ($results[0] as $fieldname => $field) {
		echo $separator;
		echo "\"" . stripslashes($fieldname) . "\"";
		$separator = ",";
	}
	echo "\n";

	foreach ($results as $row) {
		$separator = "";
		foreach ($row as $fieldname => $field) {
			echo $separator;

			// handle NULL
			if ($field != NULL) {
				// escape " character in field
				$field = str_replace("\"", "\"\"", $field);
				// strip newlines in field
				$field = str_replace(array('\n', '\r'), " ", $field);
			}
			echo "\"" . stripslashes($field) . "\"";
			$separator = ",";
		}
		echo "\n";
	}
} else {
	echo '"security failure", "nonce"';
}
