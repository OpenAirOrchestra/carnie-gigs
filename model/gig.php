<?php

/*
 * This is a gig model object.
 */
class carnieGigModel {

	/*
	 * Read a gig from a table
	 */
	function gig($table_name, $gigid) {
		global $wpdb;

		$query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d;", $gigid);
		$gig = $wpdb->get_row($query, ARRAY_A);

		return $gig;
	}

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
		if ((strlen($_POST['calltime']) != 0) && (! carnieForms::check_time($_POST['calltime']))) {
			$form_errors['calltime'] = "Did not recognize \"" . 
				$_POST['calltime'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['calltime'] = "";
		}
		if ((strlen($_POST['eventstart']) != 0) && (! carnieForms::check_time($_POST['eventstart']))) {
			$form_errors['eventstart'] = "Did not recognize \"" . 
				$_POST['eventstart'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['eventstart'] = "";
		}
		if ((strlen($_POST['performancestart']) != 0) && (! carnieForms::check_time($_POST['performancestart']))) {
			$form_errors['performancestart'] = "Did not recognize \"" . 
				$_POST['performancestart'] . "\" as a time.  Please enter time in 12 hour format (HH:MMam/pm), like 3:00pm";
			$_POST['performancestart'] = "";
		}
		return $form_errors;
	}

	/*
	 * Commit $_POST to database as gig.  
	 * Returns message with success or failure string.
	 */
	function commit_form($tablename) {
		$message = NULL;
		$valid_columns = array( "id", "date", "title", "description", "location", "url", "privateevent", "calltime", "eventstart", "performancestart", "contact", "coordinator", "costume", "advertise", "cancelled", "closedcall", "attendees", "fee", "tentative");
		
		global $wpdb;

		if (strlen($_POST['id']) > 0) {
			$id = $wpdb->escape($_POST['id']);

			$query = "UPDATE " . $tablename . " SET ";

			$values = "";

			foreach ( $_POST as $ind=>$val ) {
				if (in_array($ind, $valid_columns)) {
					if (strlen($values) > 0) {
						$values = $values . ",";
					}	

					// Special processing of date and time
					$value = $val;
					if ($ind == "date") {
						$value = carnieForms::form_date_to_mysql($val);
					}
					else if (($ind == "calltime") ||
						($ind == "eventstart") ||
						($ind == "performancestart")) {
						$value = carnieForms::form_time_to_mysql($val);
					}
					$values = $values . "`" . $wpdb->escape($ind) . "` = ";
					if ($value == null) {
						$values = $values . "NULL";
					} else {
						$values = $values . "'" . $wpdb->escape($value) . "'";
					}
				}
			}

			// Special handling for unchecked checkboxes
			if (strlen($_POST['privateevent']) == 0) {
				$values = $values . ",`privateevent`='0'";
			}
			if (strlen($_POST['closedcall']) == 0) {
				$values = $values . ",`closedcall`='0'";
			}
			if (strlen($_POST['advertise']) == 0) {
				$values = $values . ",`advertise`='0'";
			}
			if (strlen($_POST['cancelled']) == 0) {
				$values = $values . ",`cancelled`='0'";
			}
			if (strlen($_POST['tentative']) == 0) {
				$values = $values . ",`tentative`='0'";
			}

			$query = $query . $values;
			$query = $query . " WHERE id=$id LIMIT 1";

			if ($wpdb->query($query)) {
				$message = "Your Gig has been updated in the database.";
			} else {
				$message = "Error in query: $query"; 
			}


		} else {
			$query = "INSERT INTO " . $tablename;

			$values = "";
			$columns = "";

			foreach ( $_POST as $ind=>$val ) {
				if ((in_array($ind, $valid_columns)) && (strlen($val) > 0)) {
					if (strlen($values) > 0) {
						$values = $values . ",";
						$columns = $columns . ",";
					}	

					// Special processing of date and time
					$value = $val;
					if ($ind == "date") {
						$value = carnieForms::form_date_to_mysql($val);
					}
					else if (($ind == "calltime") ||
						($ind == "eventstart") ||
						($ind == "performancestart")) {
						$value = carnieForms::form_time_to_mysql($val);
					}
					$values = $values . "'" . $wpdb->escape($value) . "'";
					$columns = $columns . $ind;
				}
			}

			$query = $query . "(" . $columns . ") VALUES(" . $values . ")";

			if ($wpdb->query($query)) {
				$message = "Your new Gig has been added to the database.";
			} else {
				$message =  "Error in query: $query"; 
			}	

		}

		return $message;
	}
}

?>
