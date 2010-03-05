<?php

/*
 * This is the controller for the edit-carnie-gigs page.
 */
class carnieGigEditController {

	private $gigsView, $model, $message, $gigPostController;
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->model = new carnieGigModel;
		$this->message = null;
		$this->gigPostController = new carnieGigPostController;
	}
	   
	/*
	 * Update a gig record
	 */
	function update($gig) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		
		if (! current_user_can('edit_pages')) {
			$this->message = 'Current user cannot edit pages';
		} else {
			// Update gig in the database.
			$this->message = $this->model->commit_form($table_name);
			// Update post associated with gig.
			$this->gigPostController->update($gig['id']);
		}
	}
	   
	/*
	 * render gigs page
	 */
	function render_gigs_page() {
		print '<div class="wrap">';
		echo "<h2>Edit Carnie Gigs";

		if ($this->message) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $this->message . "</p>";
				print '</div>';
		}
		echo '<a href="admin.php?page=new-carnie-gig" class="button add-new-h2">Add New</a>';
		echo "</h2>";
		
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";
		
		$select = "SELECT * FROM " . $table_name .
			" ORDER BY `date` DESC";

		$results = $wpdb->get_results( $select, ARRAY_A );

		$this->gigsView->shortGigs($results);
		print "</div>";
	}

	   
	/*
	 * render gig page
	 */
	function render_gig_page($gig, $errors) {
		print '<div class="wrap">';
		echo "<h2>Edit Carnie Gig";

		if ($this->message) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $this->message . "</p>";
				print '</div>';
		}
		echo "</h2>";
		

		$this->gigsView->form($gig, $errors);
		print "</div>";
	}

	/*
	 * delete a gig
	 */
	function delete($gigid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		$title = $wpdb->get_var($wpdb->prepare("SELECT title FROM $table_name WHERE id = %d;", $gigid));


		if ($wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d;", $gigid))) {
			$this->message = 'Deleted "' . stripslashes($title) .'"';
		} else {
			$this->message = "Error deleting " . $gigid;
			$wpdb->print_error();
		}

	}

	/*
	 * edit gigs page
	 */
	function edit_gigs_page() {
		// massage POST data
		$folder = carnieUtil::get_url();

		if ( get_magic_quotes_gpc() ) {
			$_POST      = array_map( 'stripslashes_deep', $_POST );
			$_GET       = array_map( 'stripslashes_deep', $_GET );
			$_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
			$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
		}
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		if ($_POST['method'] || $_POST['_submit_check']) {

			$gig = NULL;
			$errors = array();
			
			$this->message = "DEBUG " . $_POST['method'] . " " . $_POST['gigid'];
				
			// Verify nonce.
			if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
				if ($_POST['method'] == 'delete' && $_POST['gigid']) {
					$this->delete($_POST['gigid']);
				} else if ($_POST['method'] == 'edit' && $_POST['gigid']) {
					$gig = $this->model->gig($table_name, $_POST['gigid']);
					if ($gig) {
						$this->message = NULL;
					}
				} else if ($_POST['_submit_check'])  {
					$errors = $this->model->validate_post();
					if ($errors) {
						$gig = $_POST;
						$this->message = 'Please correct errors below.';
					} else {
						$this->update($_POST);
						$gig = $this->model->gig($table_name, $_POST['id']);
						$errors = array();
					}
				}
			} else {
				$this->message = '"security failure", "nonce"';
			}
		}

		if ($gig) {
			$this->render_gig_page($gig, $errors);
		} else {
			// Render list
			$this->render_gigs_page();
		}
	}

	/*
	 * Edit gigs admin page head
	 */
	function edit_gigs_head() {
		$folder = carnieUtil::get_url();
		echo "<link rel='stylesheet' href='$folder/css/admin.css' type='text/css' />\n";
	}
}
?>
