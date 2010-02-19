<?php

/*
 * This class handles simple rendering of individual gigs.
 */
class carnieGigViews {

	/*
	 * Render short view of gigs from database results
	 * in an HTML table.
	 */
	function shortGigs($gigs) {

		print '<table class="gigs">';
		$even = false;
		foreach ($gigs as $gig) {
			$this->shortGig($gig, $even);
			$even = ! $even;
		}
		print "</table>";
	}

	/*
	 * Render a single short gig table row
	 * in an HTML table row
	 */
	function shortGig($gig, $even = false) {
		if ($even) {
			print '<tr class="even gig y' . date('Y', strtotime($gig['date'])) . '">';
		} else {
			print '<tr class="gig y' . date('Y', strtotime($gig['date'])) . '">';
		}

		echo '\t<td class="date">' . date('d M Y', strtotime($gig['date'])) . "</td>\n";
		echo '\t<td class="title">' . stripslashes($gig['title']) . "</td>\n";
		print '\t<td class="status">';
		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "\t</td>";
		print '\t<td class="ical">';
		print "\t\t<a href=\"" . carnieUtil::get_url() . "ical.php?id=" . $gig[id] . 
			"\"> <img src=\"" .  carnieUtil::get_url() . "images/calendar.jpg\"></a>";
		print "\t</td>";
		print "</tr>";
	}
}
?>
