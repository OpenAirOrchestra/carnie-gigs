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

		echo '<td class="date">' . date('d M Y', strtotime($gig['date'])) . "</td>";
		echo '<td class="title">' . stripslashes($gig['title']) . "</td>";
		print '<td class="status">';
		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "</td>";
		print '<td class="ical">';
		print "<a href=\"" . carnieUtil::get_url() . "ical.php?id=" . $gig[id] . 
			"\"> <img src=\"" .  carnieUtil::get_url() . "images/calendar.jpg\"></a>";
		print "</td>";
		print "</tr>\n";
	}
}
?>
