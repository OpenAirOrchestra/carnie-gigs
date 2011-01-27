<?php


function carnieMatchFirstname($user, $attendee) {
	return $user->user_firstname ? strcasecmp($user->user_firstname , $attendee) == 0 : 0;
}

function carnieMatchDisplayname($user, $attendee) {
	return $user->display_name ? strcasecmp($user->display_name , $attendee) == 0 : 0;
}

function carnieMatchNickname($user, $attendee) {
	return $user->nickname ? strcasecmp($user->nickname , $attendee) == 0 : 0;
}

function carnieMatchNicename($user, $attendee) {
	return $user->user_nicename ? strcasecmp($user->user_nicename , $attendee) == 0 : 0;
}

function carnieMatchLogin($user, $attendee) {
	return $user->user_login ? strcasecmp($user->user_login , $attendee) == 0 : 0;
}

function carnieUserInListMatch($user, $others, $matchFunction) {
	$match = array( "match" => array(), "others" => array() );

	foreach ($others as $attendee) {
		$attendee = trim($attendee);
		if (call_user_func($matchFunction, $user, $attendee)) {
			array_push($match["match"], $attendee);
		} else {
			array_push($match["others"], $attendee);
		}

	}

	return $match;
}

function carnieUserInList($user, $others) {

	$match = carnieUserInListMatch($user, $others, 'carnieMatchFirstname');
	if (count($match["match"] == 1)) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchDisplayname');
	if (count($match["match"] == 1)) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchLogin');
	if (count($match["match"] == 1)) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchNicename');
	if (count($match["match"] == 1)) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchNickname');
	if (count($match["match"] == 1)) {
		return $match;
	}

	// Fallback
	$match = array( "match" => array(), "others" => $others );

	return $match;
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
		$title = str_replace(",", " ", $gig['title']);
		echo carnieSanitizeCsvField($title) . ", ";
		
		$others = explode(",", $gig['attendees']);

		// Carnies
		foreach ($blogusers as $bloguser) {
			$user = get_userdata($bloguser->user_id); // get actual data
			$match = carnieUserInList($user, $others);
	       
			if (count($match["match"]) == 1) {
				echo "1";
				$others = $match["others"];
			} 
			echo ",";		
		}
		
		// Others
		echo carnieSanitizeCsvField(implode(",", $others));

		echo "\n";
	}
}
?>
