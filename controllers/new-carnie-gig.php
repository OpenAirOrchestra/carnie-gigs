<?php

/*
 * This is the controller for the new-carnie-gig page.
 */
class carnieGigNewController {

	private $gigsView, $message;
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->message = null;
	}
	   
	/*
	 * create new gig in the database
	 */
	function create($gig) {
	}
	   
	/*
	 * render new gig form
	 */
	function render_form() {
		print '<div class="wrap">';
		echo "<h2>New Carnie Gig";

		if ($this->message) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $this->message . "</p>";
				print '</div>';
		}
		echo "</h2>";
		
		$this->gigsView->form(array( "date" => date('d M Y', time()), array());
		print "</div>";
	}

	/*
	 * new gig page
	 */
	function new_gig_page() {
		// massage POST data
		$folder = carnieUtil::get_url();

		if ( get_magic_quotes_gpc() ) {
			$_POST      = array_map( 'stripslashes_deep', $_POST );
			$_GET       = array_map( 'stripslashes_deep', $_GET );
			$_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
			$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
		}

		if ($_POST['_submit_check']) {
			
			$this->message = "DEBUG " . $_POST['_submit_check'] . " " . $_POST['gigid'];
				
			// Verify nonce.
			if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
				$this->create($_POST);
			} else {
				$this->message = '"security failure", "nonce"';
			}
		}

		// Render form in page
		$this->render_form();
	}
}
?>
