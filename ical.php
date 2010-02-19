<?php

/*
 * this php is called directly with a GET to get carnie gigs as iCal entries
 */
require_once 'ajaxSetup.php';
require_once 'version.php';
require_once 'views/ical.php';

global $wpdb;
$gigid = $wpdb->escape($_GET['id']);

header("Content-Type: text/x-vCalendar");
header("Content-Disposition: inline; filename=carnieGigs" . $gigid );

$table_name = $wpdb->prefix . "carniegigs";

$select = "SELECT * FROM " . $table_name . " ";

if ($gigid) {
   $select = $select . "WHERE id = " . $gigid . " ";
}

$select = $select . "ORDER BY `date` DESC";

$gigs = $wpdb->get_results( $select, ARRAY_A );

carnieGigsIcal($gigs);

?>
