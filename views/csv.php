<?php

function carnieGigsCsv($gigs) {
	// Field names
	$separator = "";
	foreach ($gigs[0] as $fieldname=>$field) {
		if ($_POST[$fieldname]) {
			echo $separator;
			echo "\"" . stripslashes($fieldname) . "\"";
			$separator = ",";
		}
	}
	
	// Data
	foreach ($gigs as $gig) {
		$separator = "";
		foreach ($gig as $fieldname=>$field) {
			if ($_POST[$fieldname]) {
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
		}
		echo "\n";
	}
}
?>
