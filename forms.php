<?php

// http://www.onlamp.com/pub/a/php/2004/08/26/PHPformhandling.html
//
class carnieForms {


	// print a single-line text box
	function input_text($element_name, $values) {
	     print '<input type="text" style="width:300px" name="' . $element_name .'" value="';
	     print htmlentities(stripslashes($values[$element_name])) . '">';
	}

	//print a textarea
	function input_textarea($element_name, $values) {
	     print '<textarea style="width:300px;height:5em" name="' . $element_name .'">';
	     print htmlentities(stripslashes($values[$element_name])) . '</textarea>';
	}

	//print a radio button or checkbox
	function input_radiocheck($type, $element_name,
				   $values, $element_value) {
	     print '<input type="' . $type . '" name="' .
		   $element_name .'" value="' . $element_value . '" ';
	     if ($element_value == $values[$element_name]) {
		 print ' checked="checked"';
	     }
	     print '/>';
	}

	//print a submit button
	function input_submit($element_name, $label) {
	     print '<input type="submit" class="button-primary" name="' . $element_name .'" value="';
	     print htmlentities($label) .'"/>';
	}

	// print a time entry text box
	function input_time($element_name, $values) {
	     print '<input type="text" name="' . $element_name .'" value="';
	     if (strlen($values[$element_name]) > 0)
	     {
		print htmlentities(date('g:ia', strtotime($values[$element_name])));
	     }
	     print  '">';
	}

	function check_time($time)
	{
		return ereg('(^([0-9]|[0-1][0-9]|[2][0-3]):([0-5][0-9])(\s*)([AM|PM|am|pm]{2,2})$)|(^([0-9]|[1][0-9]|[2][0-3])(\s*)([AM|PM|am|pm]{2,2})$)',$time); 

	}

	//
	// print a date entry text box
	function input_date($element_name, $values) {
	     print '<input type="text" name="' . $element_name .'" value="';
	     if (strlen($values[$element_name]) > 0) {
	     	print htmlentities(date('d M Y', strtotime($values[$element_name]))) . '">';
	     }
	}

	//
	// Convert a date string from a form to a mysql date
	function form_date_to_mysql($form_date)
	{
		return(date('Y-m-d', strtotime($form_date)));
	}

	//
	// Convert a time string from a form to a mysql date
	function form_time_to_mysql($form_time)
	{
		if (strlen($form_time) == 0)
		{
			return(null);
		}
		else
		{
			return(date('H:i:s', strtotime($form_time)));
		}
	}

}
?>
