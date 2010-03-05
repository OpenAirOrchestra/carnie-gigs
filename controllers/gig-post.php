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

		$post_content = "<p>" . 
			htmlentities(stripslashes($gig['description'])) . 
			"</p>";

		$post = array(
			'post_status' => 'publish',
			'post_title' => ($gig['date'] . " " . $gig['title']),
			'post_content' => $post_content
			);

		if ($gig['postid']) {
			$post['ID'] = $gig['postid'];
			// Update the post
			$postid = wp_update_post( $post );
		} else {
			// Insert the post
			$postid = wp_insert_post( $post );
			
			// Put the postid into the gig database record.
			$wpdb->update( $table_name, array( 'postid' => $postid ), array ( 'id' => $gig['id'] ), array('%d'), array ('%d') );
		}


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

		$postid = $gig['postid'];
		if ($postid) {
			wp_delete_post( $postid );
		}
	}
}
?>
