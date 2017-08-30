<?php

/*
 * Renders carnie gig post stiff
 */
class carnieGigView {

	private $nonce;

	/*
	 * content filter for gigs.
	 */
	function the_content($content, $metadata_prefix, $published_post_ID) { 
		$postid = get_the_id();
		$render_attendees = true;
		
		// We may be in the context of the Subscribe2 plugin
		// creating email.  Try the ID stashed when
		// transitioning status.
		if (! $postid) {
			$postid = $published_post_ID;
			$render_attendees = false;
		}

		// Pull data from the meta of the post or $_POST,
		// depending.

		$cancelled = get_post_meta($postid, $metadata_prefix . 'cancelled', true);
		$tentative = get_post_meta($postid, $metadata_prefix . 'tentative', true);
		$closedcall = get_post_meta($postid, $metadata_prefix . 'closedcall', true);
		$privateevent = get_post_meta($postid, $metadata_prefix . 'privateevent', true);
		$date = get_post_meta($postid, $metadata_prefix . 'date', true);
		$calltime = get_post_meta($postid, $metadata_prefix . 'calltime', true);
		$eventstart = get_post_meta($postid, $metadata_prefix . 'eventstart', true);
		$performancestart = get_post_meta($postid, $metadata_prefix . 'performancestart', true);
		$location = get_post_meta($postid, $metadata_prefix . 'location', true);
		$costume = get_post_meta($postid, $metadata_prefix . 'costume', true);
		$url = get_post_meta($postid, $metadata_prefix . 'url', true);
		$coordinator = get_post_meta($postid, $metadata_prefix . 'coordinator', true);

		// Not in the loop...
		// do we have the data we need in $_POST?  Do we have date?
		if (! $date && $_POST[$metadata_prefix . 'date']) {
			// pull data from $_POST instead
			$date = $_POST[ $metadata_prefix . 'date' ];

			$cancelled = $_POST[ $metadata_prefix . 'cancelled' ];
			$tentative = $_POST[ $metadata_prefix . 'tentative' ];
			$closedcall = $_POST[ $metadata_prefix . 'closedcall' ];
			$privateevent = $_POST[ $metadata_prefix . 'privateevent' ];
			$calltime = $_POST[ $metadata_prefix . 'calltime' ];
			$eventstart = $_POST[ $metadata_prefix . 'eventstart' ];
			$performancestart = $_POST[ $metadata_prefix . 'performancestart' ];
			$location = $_POST[ $metadata_prefix . 'location' ];
			$costume = $_POST[ $metadata_prefix . 'costume' ];
			$url = $_POST[ $metadata_prefix . 'url' ];
			$coordinator = $_POST[ $metadata_prefix . 'coordinator' ];

			$render_attendees = false;
		}

		$content = $content . ' <h2>Details</h2> ';

		// Tentative, cancelled, closed call
		if ($cancelled) {
			$content = $content . '<h3>Cancelled</h3>';
		} else {
			if ($tentative) {
				$content = $content . '<h3>Tentative</h3>';
			}
			if ($closedcall) {
				$content = $content . '<p>Closed Call</p>';
			}
			if ($privateevent) {
				$content = $content . '<p>Private Event</p>';
			}
		}

		$content = $content . ' <dl> ';

		// Time details
		$content = $content . '<dt>When:</dt> ';
		$content = $content . ' <dd> ';


		if (strtotime($date)) {
			$content = $content . '<strong>' . date('D, d M Y', strtotime($date)) . '</strong> ';
		}
		if (strlen($calltime)) {
			$content = $content .
				"<br/>Call: " .
				date('g:i a', strtotime($calltime));
		}
		if (strlen($eventstart)) {
			$content = $content .
				"<br/>Event Start: " .
				date('g:i a', strtotime($eventstart));
		}
		if (strlen($performancestart)) {
			$content = $content .
				"<br/>Performance Start: " .
				date('g:i a', strtotime($performancestart));
		}
		$content = $content . "<br/><a title=\"Download iCal entry\" href=\"" . 
			carnieUtil::get_url() . 
			"ical.php?id=" . $postid .  "\"> <img height=\"38px\" src=\"" .  
			carnieUtil::get_url() . 
			"images/calendar.jpg\"></a> ";
		$content = $content . ' </dd> ';

		// Location
		if (strlen($location)) {
			$location = htmlentities(stripslashes($location));
			$content = $content . ' <dt>Where:</dt> ';
			$content = $content . ' <dd> ';
			$content = $content . $location ;
			$content = $content . ' </dd> ';
		}

		// URL
		if (strlen($url)) {
			$content = $content . ' <dt>Link:</dt> ';
			$content = $content . ' <dd> ';
			
			if ((strncasecmp($url, 'http://', 7) != 0) &&
				(strncasecmp($url, 'https://', 8) != 0)) {
					$url = "http://" . $url;
			}
			$content = $content .  '<a href="' . $url . '">' .
				htmlentities(stripslashes($url)) .
				"</a>";
			$content = $content . ' </dd> ';
		}

		// Costume
		if (strlen($costume)) {
			$costume = htmlentities(stripslashes($costume));
			$content = $content . ' <dt>Costume:</dt> ';
			$content = $content . ' <dd> ';
			$content = $content . $costume ;
			$content = $content . ' </dd> ';
		}
		
		// Co-ordinator
		if (strlen($coordinator)) {
			$coordinator = htmlentities(stripslashes($coordinator));
			$content = $content . ' <dt>Coordinator:</dt> ';
			$content = $content . ' <dd> ';
			$content = $content . $coordinator ;
			$content = $content . ' </dd> ';
		}
		
		// Contact
		$contact = get_post_meta($postid, $metadata_prefix . 'contact', true);
		if (strlen($contact)) {
			$contact = htmlentities(stripslashes($contact));
			$content = $content . ' <dt>Contact:</dt> ';
			$content = $content . ' <dd> ';
			$content = $content . $contact ;
			$content = $content . ' </dd> ';
		}
		
		if ($render_attendees) {
			// Attendees
			$content = $this->attendees($content, $metadata_prefix, $postid);
			$content = $this->verified_attendees($content, $metadata_prefix, $postid);
		}
		return $content;
	}

	/*
	 * Return body of gig attendance widget.
	 */
	function attendees($content, $metadata_prefix, $postid) { 
		global $current_user;
		get_currentuserinfo();
		$display_name = $current_user->display_name; 
		if (! $display_name) {
			$display_name = $current_user->user_login;
		}
		$found = false;

		$attendees = get_post_meta($postid, $metadata_prefix . 'attendees');
		sort($attendees);
		$content = $content . ' <dt>Attendees:</dt> ';
		$content = $content . ' <dd> ';
		foreach ($attendees as $attendee) {
			$content = $content . $sep;

			if ($attendee == $current_user->display_name ||
			    $attendee == $current_user->user_login) {
				    $found = true;
				    $content = $content . '<span style=\"font-weight:bolder\">';
			} else {
				    $content = $content . '<span>';
			}

			$attendee = htmlentities(stripslashes($attendee));

		        $content = $content . $attendee;
			$content = $content . '</span>';
			$sep = ', ';
		}

		// Don't display add me button for old or closec call gigs.
		$closedcall = get_post_meta($postid, $metadata_prefix . 'closedcall', true);
		$days = 0;
		$date = get_post_meta($postid, $metadata_prefix . 'date', true);
		if ($date) {
			$seconds = time() - strtotime($date);
			$days = $seconds / ( 60 * 60 * 24 );

			// TODO: The server is in Saskatchewen, so convert 
			// to PST (2 hour offset)
		}

		if (($days <= 1.4) && (! $closedcall)) {
			// refresh nonce
			$this->nonce = wp_create_nonce('carnie-gig-attendance');

			$content = $content . '
			<form method="POST"  action="">
				<input type="hidden" name="carnie-gigs-csv-verify-key" value="' .
					$this->nonce . '"/>
					<input type="hidden" name="gigattendance" value="';

				if ($found) {
					$content = $content . 'remove"/>
					<input class="button" type="submit" name="Remove Me" value="Remove Me" />';
				} else {
					$content = $content . 'add"/>
					<input class="button" type="submit" name="Add Me" value= "Add Me" />';
				}
				$content = $content . '<input type="hidden" name="gigid" value=" ' .
					$postid . '"/>';
				$content = $content . "</form>";
		}

		$content = $content . ' </dd> ';
		return $content;
	}

	/*
	 * Return rendered verified attendees.
	 */
	function verified_attendees($content, $metadata_prefix, $postid) { 
		global $current_user;
		get_currentuserinfo();
		$display_name = $current_user->display_name; 
		if (! $display_name) {
			$display_name = $current_user->user_login;
		}
		$found = false;

		$verified_attendees_database = new verifiedAttendeesDatabase;
		$attendees = $verified_attendees_database->verified_attendees($postid);

		if ((count($attendees) > 0) || (current_user_can('edit_post', $postid))) {

			$content = $content . ' <dt>Verified Attendees:</dt> ';
			$content = $content . ' <dd> ';

			$content = $content . '<ul>';
			foreach ($attendees as $attendee) {
				$content = $content . '
<li>';

				if ($attendee['user_id'] == $user_ID) {
			    		$found = true;
                                        $content = $content . '<span style="font-weight:bolder">';
				} else if ($attendee['user_id']) {
	   				$content = $content . '<span>';
				} else {
                                        $content = $content . '<span style="font-style:oblique">';
				}

				$content = $content . htmlentities(stripslashes($attendee['firstname']));
				$content = $content . ' ';

				// trunc lastname to first initial for users who don't have sufficient privs.
				if (current_user_can('read_private_posts')) {
					$content = $content . htmlentities(stripslashes($attendee['lastname']));
                        	} else {
					$content = $content . substr(htmlentities(stripslashes($attendee['lastname'])), 0, 1);
                        	}

				$content = $content . '</span>';

				$notes = $attendee['notes'];
				
				if ($attendee['user_id'] && (!$notes || !strlen($notes))) {
					$user_info = get_userdata($attendee['user_id']);
					if ($user_info) {
						$notes = $user_info->user_description;
					}
				}

				if ($notes && strlen($notes)) {
					$content = $content . '<div style="font-size:smaller;height:1.5em;overflow:hidden">' . htmlentities(stripslashes($notes)) . '</div>';
				}
				$content = $content . '
</li>';
			}

			$content = $content . '</ul>';

			if (current_user_can('edit_post', $postid)) {
				// button/form to verify attendees
				$attendance_nonce = wp_create_nonce('attendance_nonce');
                        	$attendance_url = get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(dirname(__FILE__))) . "/verified_attendance.php";

				$content = $content . '<form action="' . $attendance_url . '" method = "post">';
				$content = $content . '<p><input name="the_submit" type="submit" value="Verify Attendance"/></p>';
				$content = $content . '<input name="attendance_nonce" type="hidden" value="' . $attendance_nonce. '"/>';
				$content = $content . '<input name="gig" type="hidden" value="' . $postid. '"/>';
				$content = $content . '</form>';

			}

			$content = $content . ' </dd> ';

		}
		return $content;
	}

	/*
	 * Render short view of gigs from database results
	 * in an HTML table.
	 */
	function shortGigs($gigs, $check_post_status) {

		echo '<table class="gigs">';
		$even = false;
		foreach ($gigs as $gig) {
			
			$valid = true;

			if ($check_post_status) {
				$gigid = $gig['gigid'];
				$post = get_post($gigid);
				if ((! $post) ||  
				    (! $post->post_status)  ||
				    (strcasecmp($post->post_status, "publish") != 0)) {
					$valid = false;
				}
			}

			if ($valid) {
				$this->shortGig($gig, $even);
				$even = ! $even;
			}
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

		$permalink = NULL;
		if ($gig['gigid']) {
			$permalink = get_permalink($gig['gigid']);
		}

		print '<tr id="'. $idstr . '" class="' . $classstr . '">';

		echo '<td class="column-title">';
		if ($permalink) {
			echo '<strong><a class="row-title" href="' . $permalink . '">' . stripslashes($gig['title']) . "</a></strong>";
		} else {
			echo '<strong><span class="row-title">' . stripslashes($gig['title']) . "</span></strong>";
		}
		echo "</td>";
		$time = strtotime($gig['date']);
		echo '<td class="date">' . 
			date('d', $time) . '&nbsp;' .
			date('M', $time) . '&nbsp;' .
			date('Y', $time) . 
			"</td>";

		print '<td class="calltime">';
		$calltime = date('g:i a', strtotime($gig['calltime']));
		echo $calltime;
		echo "</td>";

		print '<td class="status">';

		if ($gig['cancelled']) {
			echo "(cancelled)";
		}
		if ($gig['tentative']) {
			echo "(tentative)";
		
		}       
		print "</td>";
		if (! is_admin()) {
			print '<td>';
			print "<a title=\"Download iCal entry\" href=\"" . carnieUtil::get_url() . "ical.php?id=" . $gig['gigid'] . 
				"\"> <img style=\"vertical-align:middle;max-width:19px\" src=\"" .  carnieUtil::get_url() . "images/calendar.jpg\"></a>";
			print "</td>";
		}
		print "</tr>\n";
	}

}
?>
