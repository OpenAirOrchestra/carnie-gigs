<?php

/*
 * This class handles simple rendering of individual gigs.
 */
class carnieGigViews {

	private $nonce;

	/*
	 * Render short view of gigs from database results
	 * in an HTML table.
	 */
	function shortGigs($gigs) {

		// refresh nonce
		$this->$nonce = wp_create_nonce('carnie-gigs');
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
		if ($even) {
			$classstr = $classstr . " alternate";
		}
		if (is_admin()) {
			$classstr = $classstr . " iedit";
		}

		print '<tr id="'. $idstr . '" class="' . $classstr . '">';

		echo '<td class="column-title">';
		echo '<strong><a class="row-title" href="">' . stripslashes($gig['title']) . "</a></strong>";
		if (is_admin()) {
?>
			<div class="row-actions">
			<span class="edit">
			<a href="" title="Edit this gig">Edit</a> | 
			</span>
			<span class="trash">
			<form name="deleteform" method="post" action="">
				<input type="hidden" name="CRUD" value="delete">
				<input type="hidden" name="gigid" value="<?php echo $gig['id']; ?>">
				<input type="hidden" name="carnie-gigs-csv-verify-key"
				value="<?php echo $this->$nonce; ?>"/>
				<input class="button" type="submit" name="Delete" value="Delete" />

			</form>
		        	| 
			</span>
			<span class="view">
			<a href="" title="View this gig">View</a>
			</span>
			</div>
<?php
		}
		echo "</td>";
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
