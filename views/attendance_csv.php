<?php

function carnieUserInList($user, $others) {
	return NULL;
}

function carnieSanitizeCsvField($field) {
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

	foreach ($blogusers as $bloguser) {
		$user = get_userdata($bloguser->user_id); // get actual data
		$name = $user->display_name;

		if ($user->user_lastname && 
			strlen($user->user_lastname) &&
			$user->user_firstname &&
			strlen($user->user_firstname)) {
		
			$name = $user->user_firstname . " " . $user->user_lastname;
		}

		echo carnieSanitizeCsvField($name) . ", ";
	}

	echo "\"Others\"\n";
	
	// Data
	foreach ($gigs as $gig) {
		// Date
		echo carnieSanitizeCsvField($gig['date']) . ", ";

		// Title
		echo carnieSanitizeCsvField($gig['title']) . ", ";
		
		$others = explode(",", $gig['attendees']);

		// Carnies
		foreach ($blogusers as $bloguser) {
			$user = get_userdata($bloguser->user_id); // get actual data
			$match = carnieUserInList($user, $others);
	       
			if ($match) {
				echo " 1";
				// TODO: remove match from array
			} 
			echo ",";		
		}
		
		// Others
		echo carnieSanitizeCsvField(implode("," $others);

		echo "\n";
	}
}
?>
