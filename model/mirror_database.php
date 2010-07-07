<?php

class carnieMirrorDatabase {

	private $table;

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
	 * Rebuild the mirror database table
	 */
	function rebuild($metadata_fields, $metadata_prefix) {
		if ($this->mirror_specified()) {
			global $wpdb;

			// Drop existing 

			$query = 'DROP TABLE IF EXISTS  ' . $this->table;
			$wpdb->query($query); 
			
			// Create new table
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
			$query = $query . " UNIQUE KEY id (id)";
			$query = $query . ");";
			$wpdb->query($query); 

			// Populate new table
			$gig_posts = get_posts('post_type=gig');
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

		$data = array( 'gigid' => $post->ID,
			'title' => $post->post_title,
			'description' => $post->post_content);
		$format = array( '%d', '%s', '%s');

		foreach ($metadata_fields as $field) {
			$meta = get_post_meta($post->ID, $field['id'], true);
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

		// do we insert or update?
		$query = 'SELECT ID FROM ' . $this->table . 
			' WHERE gigid = ' . $post->ID;
		$id = $wpdb->get_var($query);
		if ($id) {
			$where = array( 'id' => $id );
			$wpdb->insert( $this->table, $data, $where, $format );
		} else {
			$wpdb->insert( $this->table, $data, $format );
		}
	}
}

?>
