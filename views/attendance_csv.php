<?php

function sanitizeCsvField($field) {
	if ($field != NULL) {
		// escape " character in field
		$field = str_replace("\"", "\"\"", $field);
		// strip newlines in field
		$field = str_replace(array('\n', '\r'), " ", $field);

	}
	$field = "\"" . stripslashes($field) . "\"";

	return $field;
}

function carnieGigsCsvAttendance($gigs) {

	// Date, Gig Name, Carnies, Others
	echo "\"date\", \"title\", ";

	$blogusers = get_users_of_blog();

	foreach ($blogusers as $user) {
		echo sanitizeCsvField($user->nicename) . ", ";
	}

	echo "\"Others\"\n";
	
	// Data
	foreach ($gigs as $gig) {
		// Date
		echo sanitizeCsvField($gig['date']) . ", ";

		// Title
		echo sanitizeCsvField($gig['title']) . ", ";
		
		// Carnies
		
		// Others
		echo sanitizeCsvField($gig['attendees']);

		echo "\n";
	}
}
?>
