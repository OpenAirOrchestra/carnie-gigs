<?php

/*
 * Controller for carnie gig... admin UI
 */
class carnieGigsMetaFormController {

	/*
	 * save form data for carnie gigs meta box
	 */
	function save_data($post_id, $metabox_fields) { 

		// verify nonce
		if (!wp_verify_nonce($_POST['carnie_gig_meta_box_nonce'], carnieMetaBox)) {
			return $post_id;
		}

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		// TODO
		/*
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
		 */

		foreach ($metabox_fields as $field) {
			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
			
			// special handling of date time fields.
			if ($new && $field['type'] == 'time') {
				$new = date('H:i:s', strtotime($new));
			}
			if ($new && $field['type'] == 'date') {
				$new = date('Y-m-d', strtotime($new));
			}

			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		}
	}
}
?>
