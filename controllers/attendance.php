<?php

/*
 * This class is the controller for the gig attendance widget.
 */
class carnieGigAttendanceController {

	/*
	 * handle post.
	 */
	function handle_post($gigid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		// Verify nonce.
		if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gig-attendance') ) {
			global $current_user;
			get_currentuserinfo();
			$display_name = $current_user->display_name;
			if (! $display_name) {
				$display_name = $current_user->user_login;
			}

			$model = new carnieGigModel;
			$gig = $model->gig($table_name, $gigid);
			$old_attendees = preg_split("/[,\r\n\t\f]+/",$gig['attendees']);
			$new_attendees = array();

			// Remove any previous occurrence of display_name
			foreach ($old_attendees as $key => $value) {
				$value = trim($value);
				if ($value != $display_name && strlen($value)) {
					array_push($new_attendees, trim($value));
				}
			}

			if ($_POST['gigattendance'] == 'add') {
				// add
				array_push($new_attendees, $display_name);
			}

			sort($new_attendees);
			
			// update database
			$update = array();
			$update['id'] = $gig['id'];
			$update['attendees'] =  implode(", ", $new_attendees);
			$model->update($table_name, $update);

			// update post
			$gigPostController = new carnieGigPostController;
			$gigPostController->update($gigid);
		}
	}
}
?>
