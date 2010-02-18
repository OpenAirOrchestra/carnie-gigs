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
		if (mysql_num_rows($gigs) > 0) {

			print "<table>";
			while ($gig = mysql_fetch_assoc($result)) {
				$this->shortGig($gig);
			}
			print "</table>";
		}
	}

	/*
	 * Render a single short gig table row
	 * in an HTML table row
	 */
	function shortGig($gig) {
		print "<tr>";
		echo "\t<td>" . date('d M Y', strtotime($gig['date'])) . "</td>\n";
		echo "\t<td>" . stripslashes($gig['title']) . "</td>\n";
		print "\t<td>";
		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "\t</td>";
		print "</tr>";
	}
}
?>
