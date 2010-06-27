<?php

/*
 * Renders carnie gig post stiff
 */
class carnieGigView {

	/*
	 * content filter for gigs.
	 */
	function the_content($content, $metadata_prefix) { 
		$post = get_post(get_the_id());

		$content = $content . ' <h2>Details</h2> ';

		// Tentative, cancelled, closed call
		$cancelled = get_post_meta($post->ID, $metadata_prefix . 'cancelled', true);
		$tentative = get_post_meta($post->ID, $metadata_prefix . 'tentative', true);
		$closedcall = get_post_meta($post->ID, $metadata_prefix . 'closedcall', true);
		$privateevent = get_post_meta($post->ID, $metadata_prefix . 'privateevent', true);
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

		// Time details
		$content = $content . '<h3>When</h3> ';

		$date = get_post_meta($post->ID, $metadata_prefix . 'date', true);
		$content = $content . '<strong>' . date('D, d M Y', strtotime($date)) . '</strong> ';
		$calltime = get_post_meta($post->ID, $metadata_prefix . 'calltime', true);
		if (strlen($calltime)) {
			$content = $content .
				"<br/>Call: " .
				date('g:ia', strtotime($calltime));
		}
		$eventstart = get_post_meta($post->ID, $metadata_prefix . 'eventstart', true);
		if (strlen($eventstart)) {
			$content = $content .
				"<br/>Event Start: " .
				date('g:ia', strtotime($eventstart));
		}
		$performancestart = get_post_meta($post->ID, $metadata_prefix . 'performancestart', true);
		if (strlen($performancestart)) {
			$content = $content .
				"<br/>Performance Start: " .
				date('g:ia', strtotime($performancestart));
		}

		// Location
		$location = get_post_meta($post->ID, $metadata_prefix . 'location', true);
		if (strlen($location)) {
			$location = htmlentities(stripslashes($location));
			$content = $content . ' <h3>Where</h3> ';
			$content = $content . $location ;
		}

		// URL
		$url = get_post_meta($post->ID, $metadata_prefix . 'url', true);
		if (strlen($url)) {
			$content = $content . ' <h3>Link</h3> ';
			
			if ((strncasecmp($url, 'http://', 7) != 0) &&
				(strncasecmp($url, 'https://', 8) != 0)) {
					$url = "http://" . $url;
			}
			$content = $content .  '<a href="' . $url . '">' .
				htmlentities(stripslashes($url)) .
				"</a>";
		}

		// Costume
		$costume = get_post_meta($post->ID, $metadata_prefix . 'costume', true);
		if (strlen($costume)) {
			$costume = htmlentities(stripslashes($costume));
			$content = $content . ' <h3>Costume</h3> ';
			$content = $content . $costume ;
		}
		
		// Co-ordinator
		$coordinator = get_post_meta($post->ID, $metadata_prefix . 'coordinator', true);
		if (strlen($coordinator)) {
			$coordinator = htmlentities(stripslashes($coordinator));
			$content = $content . ' <h3>Coordinator</h3> ';
			$content = $content . $coordinator ;
		}
		
		// Contact
		$contact = get_post_meta($post->ID, $metadata_prefix . 'contact', true);
		if (strlen($contact)) {
			$contact = htmlentities(stripslashes($contact));
			$content = $content . ' <h3>Contact</h3> ';
			$content = $content . $contact ;
		}
		
		// Attendees
		$attendees = get_post_meta($post->ID, $metadata_prefix . 'attendees', true);
		if (strlen($attendees)) {
			$attendees = htmlentities(stripslashes($attendees));
			$content = $content . ' <h3>Attendees</h3> ';
			$content = $content . $attendees ;
		}

		return $content;
	}
}
?>
