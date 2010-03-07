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
			echo " widefat ";
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

		$permalink = NULL;
		if ($gig['postid']) {
			$permalink = get_permalink($gig['postid']);
		}

		print '<tr id="'. $idstr . '" class="' . $classstr . '">';

		if (is_admin()) {
			echo '<td>' . $gig['id'] . "</td>";
		}

		echo '<td class="column-title">';
		if ($permalink) {
			echo '<strong><a class="row-title" href="' . $permalink . '">' . stripslashes($gig['title']) . "</a></strong>";
		} else {
			echo '<strong><span class="row-title">' . stripslashes($gig['title']) . "</span></strong>";
		}
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
	<input type="hidden" name="_submit_check" value="1"/> 
	<?php echo "<input type=\"hidden\" name=\"id\" value=\"" . $gig['id'] . "\"/>"; ?>

	<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div id="side-info-column" class="inner-sidebar">
		<div id="submitdiv" class="postbox">
			<div class="handlediv" title="Click to toggle"><br/></div>
			<h3 class="hndle"><span>Publish</span></h3>
			<div class="inside">
				<div class="submitbox" id="submitbox">
					<div id="major-publishing-actions">
						<div id="publishing-action">
<?php carnieForms::input_submit("Submit", "Publish"); ?> 
						</div>
					<div style="clear:left">Note: This gig will only appear on public pages if "advertise" checkbox is selected.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="post-body">
		<div id="post-body-content">
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br/></div>
				<h3 class="hndle"><span>Gig</span></h3>
				<div class="inside">

	<table>
		<tr>
			<td valign="top">Date: </td>
			<td valign="top"><?php carnieForms::input_date("date", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['date']) {
				?> <p class="error"><?php echo $Errors['date'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Title: </td>
			<td valign="top"><?php carnieForms::input_text("title", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['title']) {
				?> <p class="error"><?php echo $Errors['title'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">URL: </td>
			<td valign="top"><?php carnieForms::input_text("url", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['url']) {
				?> <p class="error"><?php echo $Errors['url'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Description: </td>
			<td valign="top"><?php carnieForms::input_textarea("description", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['description']) {
				?> <p class="error"><?php echo $Errors['description'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Location: </td>
			<td valign="top"><?php carnieForms::input_textarea("location", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['location']) {
				?> <p class="error"><?php echo $Errors['location'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Call Time: </td>
			<td valign="top"><?php carnieForms::input_time("calltime", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['calltime']) {
				?> <p class="error"><?php echo $Errors['calltime'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Event Start Time: </td>
			<td valign="top"><?php carnieForms::input_time("eventstart", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['eventstart']) {
				?> <p class="error"><?php echo $Errors['eventstart'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Performance Start Time: </td>
			<td valign="top"><?php carnieForms::input_time("performancestart", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['performancestart']) {
				?> <p class="error"><?php echo $Errors['performancestart'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Gig Co-ordinator: </td>
			<td valign="top"><?php carnieForms::input_text("coordinator", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['coordinator']) {
				?> <p class="error"><?php echo $Errors['coordinator'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Event Contact: </td>
			<td valign="top"><?php carnieForms::input_textarea("contact", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['contact']) {
				?> <p class="error"><?php echo $Errors['contact'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Costume: </td>
			<td valign="top"><?php carnieForms::input_text("costume", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['costume']) {
				?> <p class="error"><?php echo $Errors['costume'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Attendees: </td>
			<td valign="top"><?php carnieForms::input_textarea("attendees", $gig); ?> </td>
			<td valign="top">
				<?php 
				if ($Errors['attendees']) {
				?> <p class="error"><?php echo $Errors['attendees'] ?></p>
				<?php } ?>
		</td>
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
			<td valign="top">
				<?php 
				if ($Errors['fee']) {
				?> <p class="error"><?php echo $Errors['fee'] ?></p>
				<?php } ?>
			</td>
		</tr>
	</table>

				</div>
			</div>
		</div>
	</div>
	</div>
</form>


<?php
	}

	/*
	 *
	 * Return post content for a gig record.
	 */
	function post_content($gig) {
		$post_content = "<div class=\"gig-post gig\" id=\"gig-" . $gig['id'] . "\">";
		if ($gig['tentative']) {
			$post_content = $post_content . "<h3>Tentative</h3>";
		}
		if ($gig['cancelled']) {
			$post_content = $post_content . "<h3>Cancelled</h3>";
		}
		if ($gig['closedcall']) {
			$post_content = $post_content . "<p>Closed Call</p>";
		}
		if ($gig['privateevent']) {
			$post_content = $post_content . "<p>Private Event</p>";
		}
		$post_content = $post_content . "
			<table>
			<tbody>
			<tr>
			<td valign=\"top\"><strong>What</strong></td>
			<td valign=\"top\">";

		if ($gig['url']) {
			$url = $gig['url'];
			if ((strncasecmp($gig['url'], 'http://', 7) != 0) &&
				(strncasecmp($gig['url'], 'https://', 8) != 0)) {
				$url = "http://" . $url;
			}
			$post_content = $post_content .	
				'<a href="' . $url . '">' .
				htmlentities(stripslashes($gig['title'])) .
				"</a>";
		} else {
			$post_content = $post_content .	
				htmlentities(stripslashes($gig['title'])) ;
		}

		$post_content = $post_content .	
			"</td>
			</tr>
			<tr>
			<td valign=\"top\"><strong>When</strong></td>
			<td valign=\"top\">" .
			date('D, d M Y', strtotime($gig['date']));

		if (strlen($gig['calltime'])) {
			$post_content = $post_content .	
				"<br/>Call: ".
				date('g:ia', strtotime($gig['calltime']));
		}
		if (strlen($gig['eventstart']) > 0) {
			$post_content = $post_content .	
				"<br/>Event Start: ".
				date('g:ia', strtotime($gig['eventstart']));
		}
		if (strlen($gig['performancestart']) > 0) {
			$post_content = $post_content .	
				"<br/>Performance Start: ".
				date('g:ia', strtotime($gig['performancestart']));
		}
		
		$post_content = $post_content .	
			"</td>
			</tr>
			<tr>
			<td valign=\"top\"><strong>Where</strong></td>
			<td valign=\"top\">" .
			stripslashes($gig['location']);

		if (strlen($gig['description']) > 0) {
			$post_content = $post_content .	
				"</td>
				</tr>
				<tr>
				<td valign=\"top\"><strong>Info</strong></td>
				<td valign=\"top\">" .
				stripslashes($gig['description']);
		}
		if (strlen($gig['costume']) > 0) {
			$post_content = $post_content .	
				"</td>
				</tr>
				<tr>
				<td valign=\"top\"><strong>Costume</strong></td>
				<td valign=\"top\">" .
				stripslashes($gig['costume']);
		}
		
		if (strlen($gig['coordinator']) > 0) {
			$post_content = $post_content .	
				"</td>
				</tr>
				<tr>
				<td valign=\"top\"><strong>Coordinator</strong></td>
				<td valign=\"top\">" .
				stripslashes($gig['coordinator']);
		}
		
		if (strlen($gig['contact']) > 0) {
			$post_content = $post_content .	
				"</td>
				</tr>
				<tr>
				<td valign=\"top\"><strong>Contact</strong></td>
				<td valign=\"top\">" .
				stripslashes($gig['contact']);
		}
		
		$post_content = $post_content .	
			"</td>
			</tr>
			<tr>
			<td valign=\"top\"><strong>Attendees</strong></td>
			<td valign=\"top\">\n" .
			"[gigattendance gigid=\"" .  $gig['id'] . "\"]" . 
			stripslashes($gig['attendees']) .
			"[/gigattendance]";

		$post_content = $post_content .	
			"</td>
			</tr>".
			"	</tbody>
			</table>
			</div>";


		return $post_content;
	}

}
?>
