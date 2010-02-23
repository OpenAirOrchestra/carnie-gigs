<?php

/*
 * This php is called directly with POST to get carnie gigs as a spreadsheet.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'views/csv.php';

global $wpdb;

$Filename = "CarnieGigs.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$Filename");

// Verify nonce.
if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
	$table_name = $wpdb->prefix . "carniegigs";

	$select = "SELECT * FROM " . $table_name;
	if (! current_user_can('read_private_pages') {
		$select = "SELECT id date title description location url calltime eventstart performancestart coordinator costume  FROM " . $table_name;
	}

	$gigs = $wpdb->get_results( $select, ARRAY_A );
	carnieGigsCsv($gigs);
} else {
	echo '"security failure", "nonce"';
}

?>
