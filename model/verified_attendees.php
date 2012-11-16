<?php

class verifiedAttendeesDatabase {

	private $table_name;

	/*
	 * Constructor
	 */
	function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . "gig_attendance";
	}

	/*
	 * Delete verified attendees associated with a post
	 */
	function delete_post($post_id) {
		global $wpdb;
		$wpdb->show_errors();
		$sql = $wpdb->prepare("DELETE FROM `$this->table_name` WHERE gigid = %d", $post_id);
		$wpdb->query($sql);
	}

	/*
	 * Mark verified attendies associated with a post as deleted (in trash)
	 */
	function trash_post($post_id) {
		global $wpdb;
		$wpdb->show_errors();

		$wpdb->update(
			$this->table_name,
			array( 'deleted' => 1),
			array( 'gigid' => $post_id ),
			array( '%d'),
			array( '%d')
		);
	}

	/*
	 * Mark verified attendies associated with a post as not deleted (not in trash)
	 */
	function untrash_post($post_id) {
		global $wpdb;
		$wpdb->show_errors();

		$wpdb->update(
			$this->table_name,
			array( 'deleted' => 0),
			array( 'gigid' => $post_id ),
			array( '%d'),
			array( '%d')
		);
	}
	
	
	/*
	 * Return verified attendees associated with a post
	 */
	function verified_attendees ($post_id) {
		global $wpdb;
		$wpdb->show_errors();

		$sql = $wpdb->prepare("SELECT * FROM `$this->table_name` WHERE gigid = %d ORDER BY `lastname`", $post_id);


		$attendees = $wpdb->get_results( $sql, ARRAY_A );

		return $attendees;
	}

	/* 
	 * Save verified attendees to mirror database
	 */
	function mirror_post ($post_id) {
		global $wpdb;

		$verified_attendees = $this->verified_attendees($post_id);
		$mirror_table = get_option('carniegigs_mirror_table');
		if ($mirror_table && strlen($mirror_table)) {
			if (count($verified_attendees)) {
				// Loop through verified attendees to build a comma separated list
				$attendees = array();
				foreach($verified_attendees as $attendee) {
					$fullname = $attendee['firstname'] . ' ' . $attendee['lastname'];
					$fullname = str_replace(",", " ", $fullname);
					array_push($attendees, $fullname);
				}
				$verified_attendees_str = implode(",", $attendees);

				// update the field!
				$wpdb->update(
					$mirror_table,
					array( 'verifiedattendees' => $verified_attendees_str ),
					array( 'gigid' => $post_id ),
					array( '%s' ),
					array( '%d' )
				);
			}
		}
	}
}

?>
