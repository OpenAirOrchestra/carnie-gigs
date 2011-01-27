<?php

/*
 * This php is called directly with GET to get carnie gigs attendance as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'views/attendance_csv.php';

global $wpdb;

$Filename = "CarnieGigsAttendance.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if ( wp_verify_nonce($_POST['carnie-gigs-attendance-csv-verify-key'], 'carnie-gigs-attendance') ) {
	$table_name = get_option('carniegigs_mirror_table');
	$select = "SELECT * FROM " . $table_name;

	$gigs = $wpdb->get_results( $select, ARRAY_A );
	carnieGigsCsvAttendance($gigs);
} else {
	echo '"security failure", "nonce"';
}
?>
