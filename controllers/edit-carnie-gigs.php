<?php

/*
 * This is the controller for the edit-carnie-gigs page.
 */
class carnieGigEditController {

	private $gigsView ;
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
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

		if ($_POST['CRUD']) {
			// Verify nonce.
			if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $_POST['CRUD'] . "</p>";
				print '</div>';
			} else {
				echo '"security failure", "nonce"';
			}
		}

		// Render list
		print '<div class="wrap">';
		echo "<h2>Edit Carnie Gigs";
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
}
?>
