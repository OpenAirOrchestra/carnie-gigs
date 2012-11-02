<?php

class verifiedAttendeesDatabase {

	private $table;

	/*
	 * Constructor
	 */
	function __construct() {
		global $wpdb;
		$table_name = $wpdb->prefix . "gig_attendance";
	}

	/*
	 * Delete verified attendees associated with a post
	 */
	function delete($post_id) {
		global $wpdb;
		$wpdb->show_errors();
	}

	/*
	 * Return verified attendees associated with a post
	 */
	function verified_attendees ($post_id) {
		global $wpdb;
		$wpdb->show_errors();

		$sql = $wpdb->prepare("SELECT * FROM `$this->table_name` WHERE gigid = %d ORDER BY `lastname`", $postid);
		$attendees = $wpdb->get_results( $sql, ARRAY_A );

		return attendees;
	}
}

?>
