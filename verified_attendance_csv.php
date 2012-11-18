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
if ( wp_verify_nonce($_POST['verified-attendance-csv-verify-key'], 'verified-attendance') ) {


	$table_name = $wpdb->prefix . "gig_attendance";
	$select = "SELECT * FROM " . $table_name;

	if (! current_user_can('read_private_posts')) {
		$select = "SELECT * FROM " . $table_name . " WHERE `userid` = " . $current_user->ID;
	}

	$results = $wpdb->get_results( $select, ARRAY_A );

       foreach ($results[0] as $fieldname=>$field) {
		echo $separator;
		echo "\"" . stripslashes($fieldname) . "\"";
		$separator = ",";
        }
        echo "\n";


        foreach ($results as $row) {
                $separator = "";
                foreach ($row as $fieldname=>$field) {
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

?>
