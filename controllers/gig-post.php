<?php

/*
 * This controller handles creating and updating posts that 
 * correspond to carnie gigs.
 */
class carnieGigPostController {

	private $gigsView, $model;

	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->model = new carnieGigModel;
	}
	   
	/*
	 * Update post associated with gig in the database
	 */
	function update($gigid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		$gig = $this->model->gig($table_name, $gigid);

		echo "I will update the post for" . $gig['title'];
	}

	/*
	 * Delete post associated with gig in the database.
	 * Must be called *before* database record is deleted.
	 */
	function delete($gigid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		$gig = $this->model->gig($table_name, $gigid);

		echo "I will delete the post for " . $gig['title'];
	}
}
?>
