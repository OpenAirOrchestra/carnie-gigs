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
	 * Categories ids to associate gig post with
	 * TODO: make this an option instead of hardcoded
	 */
	function category_ids () {
		$gig_categories = array();
		$category_ids = get_all_category_ids();
		foreach($category_ids as $cat_id) {
			$cat_name = get_cat_name($cat_id);
			
			if ($cat_name == "Gigs") {
				array_push($gig_categories, $cat_id);
			}
		}
		return $gig_categories;
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
			'post_content' => $post_content,
			'post_category' => $this->category_ids()
			);

		$gigtime = strtotime($gig['date']);
		if ($gigtime < time()) {
			$post['post_date'] = date("Y-m-d H:i:s", $gigtime);
		}

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
