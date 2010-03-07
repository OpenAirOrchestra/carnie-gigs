<?php

/*
 * This class handles rendering gig attendance widget.
 */
class carnieGigAttendanceView {

	private $nonce;

	/*
	 * return body of gig attendance widget
	 */
	function content($gigid, $attendees) {

		$content = "<ul>";
		
		global $current_user;
		get_currentuserinfo();
		$display_name = $current_user->display_name;
		if (! $display_name) {
			$display_name = $current_user->user_login;
		}

		$found = false;
		foreach ($attendees as $value) {
			if (trim($value) == $display_name) {
				$found = true;
			}
			$content = $content . "<li>" . $value . "</li>";
		}
								                
		$content = $content . "</ul>";
		
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
			<input class="button" type="submit" name="Add Me" value="Add Me" />';
		}
		$content = $content . "</form>";
		return $content;
	}

	/*
	 * return header of gig attendance widget
	 */
	function title($gig) {
		return "<h3>" . $gig['title'] . " Attendees</h3>";
	}

	/*
	 * return full gig attendance widget
	 */
	function widget($gig) {
		// Split attendees into attendees array
		$attendees = preg_split("/[,\r\n\t\f]+/",$gig['attendees']);

		$widget = $this->title($gig);
		$widget = $widget . $this->content($gig['id'], $attendees);
		return $widget;
	}
}
?>
