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

			$wpdb->show_errors();

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
		}
	}

	function mirror_specified() {
		$this->get_options();
		return $this->table && strlen($this->table);
	}
}

?>
