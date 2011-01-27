<?php

function carnieUserCmp($a, $b)  {
	return strcmp($a->user_login, $b->user_login);
}

function carnieTrimValue(&$value) 
{ 
	    $value = trim($value); 
}

$carnieFirstnamesCounts = array();

function carnieMatchFirstnameLastInitial($user, $attendee) {
	$match = 0;

	if ($user->user_firstname && $user->user_lastname) {
		$name = trim($user->user_firstname . " " . substr($user->user_lastname, 0, 1));
		$match = (strcasecmp($name, $attendee) == 0);
	}

	return $match;
}

function carnieMatchFullname($user, $attendee) {
	$match = 0;

	if ($user->user_firstname && $user->user_lastname) {
		$fullname = trim($user->user_firstname . " " . $user->user_lastname);
		$match = (strcasecmp($fullname, $attendee) == 0);
	}

	return $match;
}

function carnieMatchFirstname($user, $attendee) {
	$attendee = explode(" ", $attendee);
	$attendee = $attendee[0];
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

		if (strlen($attendee) > 0) {
			if ( call_user_func($matchFunction, $user, $attendee)) {
				array_push($match["match"], $attendee);
			} else {
				array_push($match["others"], $attendee);
			}
		}

	}

	return $match;
}

function carnieUserInList($user, $others) {

	global $carnieFirstnamesCounts;

	if ($user->user_firstname && strlen($user->user_firstname)) {
		// Don't match firstname on users that have dupe first names
		if ($carnieFirstnamesCounts[$user->user_firstname] == 1) {
			$match = carnieUserInListMatch($user, $others, 'carnieMatchFirstname');
			if (count($match["match"]) == 1) {
				return $match;
			}
		}
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchLogin');
	if (count($match["match"]) == 1) {
		return $match;
	}

	$match = carnieUserInListMatch($user, $others, 'carnieMatchFullname');
	if (count($match["match"]) == 1) {
		return $match;
	}

	$match = carnieUserInListMatch($user, $others, 'carnieMatchDisplayname');
	if (count($match["match"]) == 1) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchNicename');
	if (count($match["match"]) == 1) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchNickname');
	if (count($match["match"]) == 1) {
		return $match;
	}
	$match = carnieUserInListMatch($user, $others, 'carnieMatchFirstnameLastInitial');
	if (count($match["match"]) == 1) {
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
	
	global $carnieFirstnamesCounts;
	$carnieFirstnames = array();

	// Date, Gig Name, Carnies, Others
	echo "\"date\", \"title\", ";

	$blogusers = get_users_of_blog();
	usort($blogusers, "carnieUserCmp");

	foreach ($blogusers as $bloguser) {
		$user = get_userdata($bloguser->user_id); // get actual data
		$name = $user->display_name;

		if ($user->user_lastname && 
			strlen($user->user_lastname) &&
			$user->user_firstname &&
			strlen($user->user_firstname)) {
		
			$name = $user->user_firstname . " " . $user->user_lastname;
		}

		if ($user->user_firstname && strlen($user->user_firstname)) {
			array_push($carnieFirstnames, $user->user_firstname);	
		}

		echo carnieSanitizeCsvField($name) . ", ";
	}

	$carnieFirstnamesCounts = array_count_values($carnieFirstnames);

	echo "\"Others\"\n";
	
	// Data
	foreach ($gigs as $gig) {
		// Date
		echo carnieSanitizeCsvField($gig['date']) . ", ";

		// Title
		$title = str_replace(",", " ", $gig['title']);
		echo carnieSanitizeCsvField($title) . ", ";
		
		$others = explode(",", $gig['attendees']);
		array_walk($others, 'carnieTrimValue');
		$others = array_unique($others);
		$others = array_values($others);

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
