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
		echo '<table class="gigs';
		if (is_admin()) {
			echo " widefat fixed";
		}
		print '">';
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
		$idstr = "gig-" . $gig['id'];
		$classstr = "gig y" . date('Y', strtotime($gig['date']));
		if ($even {
			$classstr = $classstr . " alternate";
		}
		if (is_admin()) {
			$classstr = $classstr . " iedit";
		}

		print '<tr id="'. $idstr . '" class="' . $classstr . '">';

		echo '<td class="title">' . stripslashes($gig['title']) . "</td>";
		echo '<td class="date">' . date('d M Y', strtotime($gig['date'])) . "</td>";
		print '<td class="status">';
		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "</td>";
		if (! is_admin()) {
			print '<td class="icon">';
			print "<a title=\"Download iCal entry\" href=\"" . carnieUtil::get_url() . "ical.php?id=" . $gig[id] . 
				"\"> <img src=\"" .  carnieUtil::get_url() . "images/calendar.jpg\"></a>";
			print "</td>";
		}
		print "</tr>\n";
	}
}
?>
