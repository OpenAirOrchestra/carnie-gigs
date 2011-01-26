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

$table_name = get_option('carniegigs_mirror_table');
$select = "SELECT * FROM " . $table_name;

$gigs = $wpdb->get_results( $select, ARRAY_A );
carnieGigsCsvAttendance($gigs);
?>
