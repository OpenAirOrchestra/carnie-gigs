<?php

/*
 * Renders carnie gig post stiff
 */
class carnieGigView {

	private $nonce;


	/*
	 * Render accessibility summary
	 */
	function accessiblity_summary($content, $metadata_prefix, $published_post_ID) {
		$postid = get_the_id();

		$carnie_fields = new carnieFields;
		$separator = '';
		foreach ($carnie_fields->metadata_fields as $field) {
			if (isset( $field['category']) && $field['category'] == 'gig_accessibility') {
				$value = get_post_meta($postid, $field['id'], true);

				if (isset($value) && strlen($value) > 0) {
					if ($field['type'] == 'checkbox') {
						$content = $content . $separator . $field['name'];
					} else {
						$content = $content . $separator . $field['name'] . ': ' . htmlentities(stripslashes($value));
					}
					$separator = ', ';
				}
			}
		}


		return $content;
	}

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
		$greenroom = get_post_meta($postid, $metadata_prefix . 'greenroom', true);
		$url = get_post_meta($postid, $metadata_prefix . 'url', true);
		$coordinator = get_post_meta($postid, $metadata_prefix . 'coordinator', true);
		$contact = get_post_meta($postid, $metadata_prefix . 'contact', true);
		$categories = get_the_terms($postid, 'events');

		// Not in the loop...
		// do we have the data we need in $_POST?  Do we have date?
		if (! $date && isset($_POST[$metadata_prefix . 'date']) && $_POST[$metadata_prefix . 'date']) {
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
			$greenroom = $_POST[ $metadata_prefix . 'greenroom' ];
			$url = $_POST[ $metadata_prefix . 'url' ];
			$coordinator = $_POST[ $metadata_prefix . 'coordinator' ];
			$contact = $_POST[ $metadata_prefix . 'contact' ];

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

		// Categories
		if (isset($categories) && is_countable($categories) && count($categories) > 0) {
			$content = $content . '<dt>Categories:</dt>';
			$content = $content . ' <dd> ';
			$sep = '';
			foreach ($categories as $category) {
				$content = $content . $sep;
				$content = $content . $category->name;
				$sep = ', ';
			}

			$content = $content . '</dd> ';
		}

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

		// Green Room
		if (strlen($greenroom)) {
			$greenroom = htmlentities(stripslashes($greenroom));
			$content = $content . ' <dt>Green Room:</dt> ';
			$content = $content . ' <dd> ';
			$content = $content . $greenroom ;
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

		// Accessability
		$content = $content . ' <dt>Accessability:</dt> ';
		$content = $content . ' <dd> ';

		$content = $this->accessiblity_summary($content, $metadata_prefix, $published_post_ID);

		$content = $content . ' </dd> ';

		return $content;
	}

	/*
	 * Return body of gig attendance widget.
	 */
	function attendees($content, $metadata_prefix, $postid) { 
	    $current_user = wp_get_current_user();
		$display_name = $current_user->display_name; 
		if (! $display_name) {
			$display_name = $current_user->user_login;
		}
		$found = false;

		$attendees = get_post_meta($postid, $metadata_prefix . 'attendees');
		sort($attendees);
		$content = $content . ' <dt>Attendees:</dt> ';
		$content = $content . ' <dd> ';
		$sep = ' ';
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
	    $current_user = wp_get_current_user();
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

				if ($attendee['user_id'] == $current_user->user_ID) {
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
				$attendance_nonce = wp_create_nonce('');
				$wp_rest_nonce = wp_create_nonce( 'wp_rest' );

				$attendance_react_url = get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(dirname(__FILE__))) . '/attendance/';

				$content = $content . '<form action="' . $attendance_react_url . '" method = "get">';
				$content = $content . '<p><input name="the_submit" type="submit" value="Verify Attendance"/></p>';
				$content = $content . '<input name="_wpnonce" type="hidden" value="' . $wp_rest_nonce. '"/>';
				$content = $content . '<input name="event_id" type="hidden" value="' . $postid. '"/>';

				$max_recents = get_option('carniegigs_recents_history_length');
				if (isset($max_recents) && is_numeric($max_recents)) {
					$content = $content . '<input name="max_recents" type="hidden" value="' . $max_recents. '"/>';
				}

				$content = $content . '</form>';
			}

			$content = $content . ' </dd> ';

		}
		return $content;
	}

	/*
	 * Render short view of gigs from database results
	 * in an HTML table.
	 * Returns the rendered table
	 */
	function shortcodeGigs($gigs, $check_post_status) {

		$result = '<table class="gigs">';
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
				$result .= $this->shortcodeGig($gig, $even);
				$even = ! $even;
			}
		}
		$result .= "</table>";
		return $result;
	}

	/*
	 * Render a single short gig table row
	 * in an HTML table row
	 * Returns the rendered row
	 */
	function shortcodeGig($gig, $even = false) {
		$result = "";

		$idstr = "gig-" . $gig['id'];
		$classstr = "gig y" . date('Y', strtotime($gig['date']));
		if ($even) {
			$classstr = $classstr . " alternate";
		}

		$permalink = NULL;
		if ($gig['gigid']) {
			$permalink = get_permalink($gig['gigid']);
		}

		$result .= '<tr id="'. $idstr . '" class="' . $classstr . '">';

		$result .= '<td class="column-title">';
		if ($permalink) {
			$result .= '<strong><a class="row-title" href="' . $permalink . '">' . stripslashes($gig['title']) . "</a></strong>";
		} else {
			$result .= '<strong><span class="row-title">' . stripslashes($gig['title']) . "</span></strong>";
		}
		$result .= "</td>";
		$time = strtotime($gig['date']);
		$result .= '<td class="date">' . 
			date('d', $time) . '&nbsp;' .
			date('M', $time) . '&nbsp;' .
			date('Y', $time) . 
			"</td>";

		$result .= '<td class="calltime">';
		
		$calltime = $gig['calltime'];
		if (strlen($calltime) && $calltime != '00:00:00') {
			$time = strtotime($calltime);
			$calltime = date('g:i', $time) . '&nbsp;' .
				date('a', $time);
			$result .= $calltime;
		}
		$result .= "</td>";

		$result .= '<td class="status">';

		if ($gig['cancelled']) {
			$result .= "(cancelled)";
		}
		if ($gig['tentative']) {
			$result .= "(tentative)";
		
		}       
		$result .= "</td>";
		if (! is_admin()) {
			$result .= '<td>';
			$result .= "<a title=\"Download iCal entry\" href=\"" . carnieUtil::get_url() . "ical.php?id=" . $gig['gigid'] . 
				"\"> <img style=\"vertical-align:middle;max-width:19px\" src=\"" .  carnieUtil::get_url() . "images/calendar.jpg\"></a>";
				$result .= "</td>";
		}
		$result .= "</tr>\n";

		return $result;
	}

}
?>
