<?php

function carnieGigsCsv($gigs)
{
	// Field names
	$separator = "";
	foreach ($gigs[0] as $fieldname => $field) {
		echo $separator;
		echo "\"" . stripslashes($fieldname) . "\"";
		$separator = ",";
	}
	echo "\n";

	// Data
	foreach ($gigs as $gig) {
		$separator = "";
		foreach ($gig as $fieldname => $field) {
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
}
