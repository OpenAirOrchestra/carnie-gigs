<?php

/*
 * This is the controller for the new-carnie-gig page.
 */
class carnieGigNewController {

	private $gigsView, $message, $model;
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->model = new carnieGigModel;
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
	function render_form($errors) {
		print '<div class="wrap">';
		echo "<h2>New Carnie Gig";

		if ($this->message) {
				print '<div id="message" class="updated fade">';
				print "<p>" . $this->message . "</p>";
				print '</div>';
		}
		echo "</h2>";
		
		if (! $errors) {
			$this->gigsView->form(array( "date" => date('d M Y', time())), array());
		} else {
			$this->gigsView->form($_POST, $errors);
		}

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

		$errors = NULL;

		if ($_POST['_submit_check']) {
			
				
			// Verify nonce.
			if ( wp_verify_nonce($_POST['carnie-gigs-csv-verify-key'], 'carnie-gigs') ) {
				$errors = $this->model->validate_post();
				if ($errors) {
					$this->create($_POST);
				} else {
					$this->message = 'Error';
				}
			} else {
				$this->message = '"security failure", "nonce"';
			}
		}

		// Render form in page
		$this->render_form($errors);
	}
}
?>
