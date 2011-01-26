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
		$name = $user->display_name;

		if ($user->user_lastname && 
			strlen($user->user_lastname) &&
			$user->user_firstname &&
			strlen($user->user_firstname)) {
		
			$name = $user->user_firstname . " " . $user->user_lastname;
		}

		echo sanitizeCsvField($user->user_nicename) . ", ";
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
