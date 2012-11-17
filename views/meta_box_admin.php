<?php

/*
 * Renders meta box for carnie gig... admin UI
 */
class carnieGigsMetaFormView {

	/*
	 * Render form for carnie gigs meta box
	 */
	function render($post, $metabox) { 

		$metabox['args']['metadata_prefix'];

		// From http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
		// TODO: http://matth.eu/wordpress-date-field-plugin
		//
	       
		// Use nonce for verification
		echo '<input type="hidden" name="carnie_gig_meta_box_nonce" value="', wp_create_nonce('carnieMetaBox'), '" />';

		echo '<table class="form-table">';
		
		foreach ($metabox['args']['metadata_fields'] as $field) {
			// get current post metadata
			$single = $field['type'] != 'list';
			$meta = get_post_meta($post->ID, $field['id'], $single);
			if ($single) {
				$meta = htmlentities(stripslashes($meta));
			}
			echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>', 
				'<td>';
			
			switch ($field['type']) {
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $option) {
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>';
					break;
				case 'radio':
					foreach ($field['options'] as $option) {
						echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
					}
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' /> <br/>', ' ', $field['desc'];
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', ' ', $field['desc'];
					break;
/*
				case 'date':
					echo '<input type="date" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : '', '" /><br/>', ' ', $field['desc'];
					break;
				case 'time':
					echo '<input type="time" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : '', '" /><br/>', ' ', $field['desc'];
					break;
*/
				case 'date':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? date('d M Y', strtotime($meta)) : $field['std'], '" size="30" style="width:97%" />', ' ', $field['desc'];
					break;
				case 'time':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? date('g:ia', strtotime($meta)) : $field['std'], '" size="30" style="width:97%" />', ' ', $field['desc'];
					break;
				case 'url':
					echo '<input type="url" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" style="width:97%" />', ' ', $field['desc'];
					break;
				case 'list':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">';
					if ($meta) {
						$sep = '';
						sort($meta);
						foreach ($meta as $meta_value) {
							$meta_value = htmlentities(stripslashes($meta_value));
							echo $sep . $meta_value;
							$sep = ', ';
						}
					} 
					echo '</textarea>', ' ', $field['desc'];
					break;
				case 'text':
				default:
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', ' ', $field['desc'];
					break;

			}


			echo '     <td>';
			print "</tr>\n";

			if ($field['suggest'] && strlen($field['suggest'])) {
				// See: http://www.vulgarisoip.com/2007/06/29/jquerysuggest-an-alternative-jquery-based-autocomplete-library/#comment-7228
	?>
	<script type="text/javascript">
				/* <![CDATA[ */
		jQuery(function() {
			jQuery("#<?php echo $field['id']; ?>").suggest("<?php echo carnieUtil::get_url() . $field['suggest'] . ".php"; ?>", {
				multiple: true,
				multiple: true });
		});
				/* ]]> */
	</script>
	<?php
			}
		}

		$this->render_verified_attendees($post);

	    	echo '</table>';


	}

	/*
	 * Render form for carnie gigs meta box
	 */
	function render_verified_attendees($post) { 
		if ($post->ID) {
			echo '<tr>',
				'<th style="width:20%"><label for="verifiedattendees">Verified Attendees</label></th>', 
				'<td>';
			echo '<p>', 'Participants who actually attended the gig:</p>';

			// Get attendees from database
			$verified_attendees_database = new verifiedAttendeesDatabase;
                	$attendees = $verified_attendees_database->verified_attendees($post->ID);

			// Render attendees as a list
			echo '<ul>';
			foreach ($attendees as $attendee) {
				echo "\n<li>";
				if ($attendee['user_id'] == $user_ID) {
                                        echo '<span style="font-weight:bolder">';
				} else if ($attendee['user_id']) {
	   				echo '<span>';
				} else {
                                        echo '<span style="font-style:oblique">';
				}
				echo "\n" . htmlentities(stripslashes($attendee['firstname']));
				if (current_user_can('read_private_posts')) {
                                        echo ' ' .  htmlentities(stripslashes($attendee['lastname']));
                                } else {
                                        echo ' ' .  substr(htmlentities(stripslashes($attendee['lastname'])), 0, 1);
                                }
				echo '</span>';
				if ($attendee['notes'] && strlen($attendee['notes'])) {
					echo "\n<br/>";
					echo "\n" . htmlentities(stripslashes($attendee['notes']));
				}
				echo "\n</li>";
			}
			echo '</ul>';

			$attendance_nonce = wp_create_nonce('attendance_nonce');
			$attendance_url = get_bloginfo('wpurl') . '/wp-content/plugins/' . basename(dirname(dirname(__FILE__))) . "/verified_attendance.php?attendance_nonce=" . $attendance_nonce . '&gig=' . $post->ID;
			

			echo '<a class="button" href="' . $attendance_url . '" target="_blank">Update Verifed Attendees</a>';
			echo '     <td>';
			print "</tr>\n";
		}
	}
}
?>
