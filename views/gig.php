<?php

require_once('../utility.php');

/*
 * This class handles simple rendering of individual gigs.
 */
class carnieGigViews {

	/*
	 * Render short view of gigs from database results
	 * in an HTML table.
	 */
	function shortGigs($gigs) {

		print "<table>";
		foreach ($gigs as $gig) {
			$this->shortGig($gig);
		}
		print "</table>";
	}

	/*
	 * Render a single short gig table row
	 * in an HTML table row
	 */
	function shortGig($gig) {
		print "<tr>";
		echo "\t<td>" . date('d-M-Y', strtotime($gig['date'])) . "</td>\n";
		echo "\t<td>" . stripslashes($gig['title']) . "</td>\n";
		print "\t<td>";
		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "\t</td>";
		print "\t<td>";
		print "\t\t<a href=\"" . carnieUtil::get_url() . "/ical.php?id=" . $gig[id] . 
			"\" img src=\"" .  carnieUtil::get_url() . "/images/calendar.jpg\"></a>"
		print "\t</td>";
		print "</tr>";
	}
}
?>
