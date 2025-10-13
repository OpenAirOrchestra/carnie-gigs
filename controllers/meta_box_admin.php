<?php

/*
 * Controller for carnie gig... admin UI
 */
class carnieGigsMetaFormController {

	/*
	 * save data in an associative array to carnie gig meta data
	 */
	function save_metadata($post_id, $metabox_fields, $metadata_prefix, $data) {
		foreach ($metabox_fields as $field) {
			$old = get_post_meta($post_id, $field['id'], true);

			$key = $field['id'];

			if (!isset($data[$key]) && $field['type'] != 'checkbox') {
				continue;
			}

			$new = isset($data[$key]) ? $data[$key] : null;

			if (! $new) {
				$key = str_replace($metadata_prefix, '', $key);
				if (array_key_exists($key, $data)) {
					$new = $data[$key];
				}
			}
			
			// special handling of date time fields.
			if ($new && $field['type'] == 'time') {
				$new = date('H:i:s', strtotime($new));
			}
			if ($new && $field['type'] == 'date') {
				$new = date('Y-m-d', strtotime($new));
			}

			// Special handling of lists
			if ($field['type'] == 'list') {
				$new = preg_split("/[,\r\n\t\f]+/", $new);
				delete_post_meta($post_id, $field['id']);
				foreach($new as $value) {
					$value = trim($value);
					add_post_meta($post_id, $field['id'], $value);
				}
			} else {
				if ($new && $new != $old) {
					update_post_meta($post_id, $field['id'], $new);
				} elseif ('' == $new && $old) {
					delete_post_meta($post_id, $field['id'], $old);
				}
			}
		}
	}

	/*
	 * save form data for carnie gigs meta box
	 */
	function save_data($post_id, $metabox_fields, $metadata_prefix) { 

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		// TODO
		/*
		if ('gig' == $_POST['post_type']) {
			if (!current_user_can('edit_gig', $post_id)) {
				return $post_id;
			}
		} else {
			return $post_id;
		}
		 */

		$this->save_metadata($post_id, $metabox_fields, $metadata_prefix, $_POST);
	}
}
?>
