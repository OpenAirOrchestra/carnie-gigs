<?php

class carnieMirrorDatabase {

	private $host, 
		$database, 
		$table;

	/*
	 * Constructor
	 */
	function __construct() {
	}

	/*
	 * Get basic options into member variables
	 */
	function get_options() {
		$this->host = get_option('carniegigs_mirror_host');
		$this->database = get_option('carniegigs_mirror_database');
		$this->table = get_option('carniegigs_mirror_table');
	}

	/*
	 * Rebuild the mirror database table
	 */
	function rebuild() {
	}

	function mirror_specified() {
		$this->get_options();
		return $this->host && strlen($this->host) &&
			$this->database && strlen($this->database) &&
			$this->table && strlen($this->table);
	}
}

?>
