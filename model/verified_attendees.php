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
	 * Return verified attendees associated with a post
	 */
	function verified_attendees ($post_id) {
		global $wpdb;
		$wpdb->show_errors();

		$sql = $wpdb->prepare("SELECT * FROM `$this->table_name` WHERE gigid = %d ORDER BY `lastname`", $post_id);


		$attendees = $wpdb->get_results( $sql, ARRAY_A );

		return $attendees;
	}
}

?>
