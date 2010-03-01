<?php

/*
 * This is a gig model object.
 */
class carnieGigModel {

	/*
	 * Validate $_POST as a gig.  Returns associative array of
	 * form errors
	 */
	function validate_post() {
		$form_errors = NULL;

		if (strlen($_POST['title']) == 0) {
			$form_errors['title'] = "Title is a required field";
		}
		if (strlen($_POST['date']) == 0) {
			$form_errors['date'] = "Date is a required field";
		}
		if (strlen($_POST['location']) == 0) {
			$form_errors['location'] = "Location is a required field";
		}
		if ((strlen($_POST['calltime']) != 0) && (! check_time($_POST['calltime']))) {
			$form_errors['calltime'] = "Did not recognize \"" . 
				$_POST['calltime'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['calltime'] = "";
		}
		if ((strlen($_POST['eventstart']) != 0) && (! check_time($_POST['eventstart']))) {
			$form_errors['eventstart'] = "Did not recognize \"" . 
				$_POST['eventstart'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['eventstart'] = "";
		}
		if ((strlen($_POST['performancestart']) != 0) && (! check_time($_POST['performancestart']))) {
			$form_errors['performancestart'] = "Did not recognize \"" . 
				$_POST['performancestart'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['performancestart'] = "";
		}
		return $form_errors;
	}
}

?>
