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

	$table_name = get_option('carniegigs_mirror_table');

	$sql = $wpdb->prepare("
					SELECT * FROM `$table_name` 
					WHERE `date` >= %s 
					AND `date` <= %s ORDER BY `date`
					", $from, $to);

	$gigs = $wpdb->get_results( $sql, ARRAY_A );
	carnieGigsCsv($gigs);
} else {
	echo '"security failure", "nonce"';
}
