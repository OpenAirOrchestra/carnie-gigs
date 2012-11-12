<?php

class carnieMirrorDatabase {

	private $table,
		$verified_attendees_database;

	/*
	 * Constructor
	 */
	function __construct() {
	}

	/*
	 * Get basic options into member variables
	 */
	function get_options() {
		$this->table = get_option('carniegigs_mirror_table');
	}

	/*
 	 * Ensure mirror database table exists, rebuild from posts
	 * if it doesn't exist
 	 */
	function ensure_exists($metadata_fields, $metadata_prefix) {

		if ($this->mirror_specified()) {
			global $wpdb;

			// Create new table if needed
			$query =  "CREATE TABLE " . $this->table . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				gigid mediumint(9),
				title text,
				description text,
				";

			foreach ($metadata_fields as $field) {
				$key = $field['id'];
				$key = str_replace($metadata_prefix, '', $key);
				$query = $query . $key;

				if ($field['type'] == 'time') {
					$query = $query . " time,";
				} else if ($field['type'] == 'date') {
					$query = $query . " date,";
				} else if ($field['type'] == 'checkbox') {
					$query = $query . "  TINYINT(1),";
				} else {
					$query = $query . " text,";
				}
				$query = $query . "
				";
			}
			$query = $query . "UNIQUE KEY id (id)";
			$query = $query . ");";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			dbDelta($query);
		}
	}

	/*
	 * Rebuild the mirror database table
	 */
	function rebuild($metadata_fields, $metadata_prefix) {
		if ($this->mirror_specified()) {
			global $wpdb;

			// Drop existing 

			$query = 'DROP TABLE IF EXISTS  ' . $this->table;
			$wpdb->query($query); 

			// Create new table
			$this->ensure_exists($metadata_fields, $metadata_prefix);

			// Populate new table
			// get_posts() only returns number_posts at a time.
			// set numberposts to -1 to remove this limit
			$gig_posts = get_posts('post_type=gig&numberposts=-1');
			foreach($gig_posts as $post) {
				$this->save_post($post, $metadata_fields, $metadata_prefix);
			}
		}
	}

	function mirror_specified() {
		$this->get_options();
		return $this->table && strlen($this->table);
	}

	function save_post($post, $metadata_fields, $metadata_prefix) {
		global $wpdb;

		$wpdb->show_errors();

		$this->get_options();

		$this->ensure_exists($metadata_fields, $metadata_prefix);

		$data = array( 'gigid' => $post->ID,
			'title' => $post->post_title,
			'description' => $post->post_content);
		$format = array( '%d', '%s', '%s');

		foreach ($metadata_fields as $field) {
			$meta = "";
			if ($field['type'] == 'list') {
				// assemble lists into comma separated strings
				$meta = get_post_meta($post->ID, $field['id']);
				$list = "";
				$sep = "";
				foreach ($meta as $value) {
					$value = trim($value);
					$list = $list . $sep . $value;
					$sep = ", ";
				}
				$meta = $list;
			} else {
				$meta = get_post_meta($post->ID, $field['id'], true);
			}


			$key = $field['id'];
			$key = str_replace($metadata_prefix, '', $key);

			// Special handling for checkboxes
			if ($field['type'] == 'checkbox') {
				$data[$key] = $meta && strlen($meta) ? 1 : 0;
				array_push($format, '%d');
			} else {
				$data[$key] = $meta;
				array_push($format, '%s');
			}
		}

		// Special handling for verified attendees.
		// Get verified attendees from verified attendees table
		// instead of post metadata.
		if (! $this->verified_attendees_database) {
			$this->verified_attendees_database = new verifiedAttendeesDatabase;
		}
		$verified_attendees = $this->verified_attendees_database->verified_attendees($post->ID);
		if (count($verified_attendees)) {
			// Loop through verified attendees to build a comma separated list
			$attendees = array();
			foreach($verified_attendees as $attendee) {
				$fullname = $attendee['firstname'] . ' ' . $attendee['lastname'];
				$fullname = str_replace(",", " ", $fullname);
				array_push($attendees, $fullname);
			}
			$verified_attendees_str = implode(",", $attendees);
			$data['verifiedattendees'] = $verified_attendees_str;
			array_push($format, '%s');
		}

		// do we insert or update?
		$query = 'SELECT ID FROM ' . $this->table . 
			' WHERE gigid = \'' . $post->ID . '\'';
		$id = $wpdb->get_var($query);
		if ($id) {
			$where = array( 'gigid' => $post->ID );
			$wpdb->update( $this->table, $data, $where, $format );
		} else {
			$wpdb->insert( $this->table, $data, $format );
		}
	}

	/*
	 * Delete post data from mirror database
	 */
	function delete_post($post_id) {
		if ($this->mirror_specified()) {
			global $wpdb;
			
			$query = 'DELETE FROM ' . $this->table . 
				' WHERE gigid = \'' . $post_id . '\'';
			$wpdb->query($query);
		}
	}

	/*
	 * Return past gigs in the mirror database
	 */
	function past_gigs () {
		$results = array();

		// SELECT DATE(DATE_SUB(NOW(), INTERVAL 2 HOUR));
		//
		if ($this->mirror_specified()) {
			global $wpdb;
			// The server is in Saskatchewan, so convert 
			// to PST
			// SELECT DATE(DATE_SUB(NOW(), INTERVAL 2 HOUR));
			$select = "SELECT * FROM " . $this->table .
			   ' WHERE `date` < DATE(DATE_SUB(NOW(), INTERVAL 2 HOUR)) ORDER BY `date` DESC';
			$results = $wpdb->get_results( $select, ARRAY_A );
		}
		return $results;
	}

	/*
	 * Return future gigs in the mirror database
	 */
	function future_gigs () {
		global $wpdb;
		$results = array();

		// We covert the date becase the server is in Saskatchewan
		// and we are in PST Vancouver
		// DATE(DATE_SUB(NOW(), INTERVAL 2 HOUR))

		if ($this->mirror_specified()) {
			$select = "SELECT * FROM " . $this->table .
				   ' WHERE `date` >= DATE(DATE_SUB(NOW(), INTERVAL 2 HOUR)) ORDER BY `date`';

			$results = $wpdb->get_results( $select, ARRAY_A );
		}
		return $results;
	}

	/*
	 * Return all gigs in the mirror database
	 */
	function all_gigs () {
		$results = array();

		if ($this->mirror_specified()) {
			global $wpdb;
			$select = "SELECT * FROM " . $this->table .
			   " ORDER BY `date` DESC";
			$results = $wpdb->get_results( $select, ARRAY_A );
		}
		return $results;
	}

	/*
	 * Return one gig in the mirror database
	 */
	function one_gig () {
		$results = array();

		if ($this->mirror_specified()) {
			global $wpdb;
			$select = "SELECT * FROM " . $this->table .
			   " LIMIT 1";
			$results = $wpdb->get_results( $select, ARRAY_A );
		}
		return $results;
	}
}

?>
