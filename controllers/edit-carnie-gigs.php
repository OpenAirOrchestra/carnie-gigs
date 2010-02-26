<?php

/*
 * This is the controller for the edit-carnie-gigs page.
 */
class carnieGigEditController {

	private $gigsView, $message;
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->message = null;
	}
	   
	   
	/*
	 * render gigs page
	 */
	function render_gigs_page() {
		print '<div class="wrap">';
		echo "<h2>Edit Carnie Gigs";

		if ($message) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $message . "</p>";
				print '</div>';
		}
		echo '<a href="carnie-gig-new.php" class="button add-new-h2">Add New</a>';
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
	 * delete a gig
	 */
	function delete($gigid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		$title = $wpdb->get_var($wpdb->prepare("SELECT title FROM $table_name WHERE id = %d;", $gigid));


		if ($wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d;", $gigid)) {
			$this->message = "deleted " . $title;
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

		if ($_POST['method']) {
			// Verify nonce.
			if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
				if ($_POST['method'] == 'delete' && $_POST['gigid']) {
					$this->delete($_POST['gigid']);
				}

			} else {
				$this->message = '"security failure", "nonce"';
			}
		}

		// Render list
		$this->render_gigs_page();
	}
}
?>
