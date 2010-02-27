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
		$this->nonce = wp_create_nonce('carnie-gigs');
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
			<form name="editform" class="edit" method="post" action="">
				<input type="hidden" name="method" value="edit">
				<input type="hidden" name="gigid" value="<?php echo $gig['id']; ?>">
				<input type="hidden" name="carnie-gigs-csv-verify-key"
				value="<?php echo $this->nonce; ?>"/>
				<input class="button" type="submit" name="Edit" value="Edit" />

			</form>
			<form class="trash" name="deleteform" method="post" action="">
				<input type="hidden" name="method" value="delete">
				<input type="hidden" name="gigid" value="<?php echo $gig['id']; ?>">
				<input type="hidden" name="carnie-gigs-csv-verify-key"
				value="<?php echo $this->nonce; ?>"/>
				<input class="button" type="submit" name="Delete" value="Delete" />

			</form>
			<span class="view">
			<a href="" class="button" title="View this gig">View</a>
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

	/*
	 * Render a single gig in a form
	 */
	function form($gig, $Errors) {
		// refresh nonce
		$this->nonce = wp_create_nonce('carnie-gigs');
?>

<form method="POST"  action="">
	<input type="hidden" name="carnie-gigs-csv-verify-key" value="<?php echo $this->nonce; ?>"/>

	<table>
		<tr>
			<td valign="top">Date: </td>
			<td valign="top"><?php carnieForms::input_date("date", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['date'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Title: </td>
			<td valign="top"><?php carnieForms::input_text("title", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['title'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">URL: </td>
			<td valign="top"><?php carnieForms::input_text("url", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['url'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Description: </td>
			<td valign="top"><?php carnieForms::input_textarea("description", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['description'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Location: </td>
			<td valign="top"><?php carnieForms::input_textarea("location", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['location'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Call Time: </td>
			<td valign="top"><?php carnieForms::input_time("calltime", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['calltime'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Event Start Time: </td>
			<td valign="top"><?php carnieForms::input_time("eventstart", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['eventstart'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Performance Start Time: </td>
			<td valign="top"><?php carnieForms::input_time("performancestart", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['performancestart'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Gig Co-ordinator: </td>
			<td valign="top"><?php carnieForms::input_text("coordinator", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['coordinator'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Event Contact: </td>
			<td valign="top"><?php carnieForms::input_textarea("contact", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['contact'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Costume: </td>
			<td valign="top"><?php carnieForms::input_text("costume", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['costume'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">Attendees: </td>
			<td valign="top"><?php carnieForms::input_textarea("attendees", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['attendees'] ?></span></td>
		</tr>
		<tr>

			<td valign="top">Private Event: </td>
			<td valign="top"><?php carnieForms::input_radiocheck('checkbox','privateevent', $gig, '1'); ?></td>
		</tr>
		<tr>
			<td valign="top">Closed Call: </td>
			<td valign="top"><?php carnieForms::input_radiocheck('checkbox','closedcall', $gig, '1'); ?></td>
		</tr>
		<tr>
			<td valign="top">Advertise: </td>
			<td valign="top"><?php carnieForms::input_radiocheck('checkbox','advertise', $gig, '1'); ?></td>
		</tr>
		<tr>
			<td valign="top">Cancelled: </td>
			<td valign="top"><?php carnieForms::input_radiocheck('checkbox','cancelled', $gig, '1'); ?></td>
		</tr>
		<tr>
			<td valign="top">Tentative: </td>
			<td valign="top"><?php carnieForms::input_radiocheck('checkbox','tentative', $gig, '1'); ?></td>
		</tr>
		<tr>
			<td valign="top">Fee: </td>
			<td valign="top"><?php carnieForms::input_text("fee", $gig); ?> </td>
			<td valign="top"><span class="error"><?php echo $Errors['fee'] ?></span></td>
		</tr>
		<tr>
			<td valign="top">	<input type="hidden" name="_submit_check" value="1"/> 
				<?php echo "<input type=\"hidden\" name=\"id\" value=\"" . $gig['id'] . "\"/>"; ?>
				<?php carnieForms::input_submit("Submit", "Publish"); ?> 
			</td>
		</tr>
	</table>

</form>


<?php
	}

}
?>
