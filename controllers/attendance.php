<?php

/*
 * This class is the controller for the gig attendance widget.
 */
class carnieGigAttendanceController {

	/*
	 * handle post.
	 */
	function handle_post($gigid, $metadata_fields, $metadata_prefix) {
		$postid = $gigid;

		// Verify nonce. and gigid
		if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gig-attendance')  && 
		($_POST['gigid'] == $gigid)) {
			global $current_user;
			get_currentuserinfo();
			$display_name = $current_user->display_name;
			if (! $display_name) {
				$display_name = $current_user->user_login;
			}
			
			$attendees = get_post_meta($postid, $metadata_prefix . 'attendees');

			delete_post_meta($postid, $metadata_prefix . 'attendees');

			// Remove any previous occurrence of display_name
			foreach ($attendees as $value) {
				$value = trim($value);
				if ($value != $current_user->display_name
				       && value != $current_user->user_login	&& strlen($value)) {
					add_post_meta($postid, $metadata_prefix . 'attendees', $value);
				}
			}

			if ($_POST['gigattendance'] == 'add') {
				// add
				add_post_meta($postid, $metadata_prefix . 'attendees', $display_name);
			}


			// Update the database.
			$post = get_post($postid);
			$carnie_mirror_database = new carnieMirrorDatabase;
			$carnie_mirror_database->save_post($post, 
				$metadata_fields,
				$metadata_prefix);

		}
	}
}
?>
