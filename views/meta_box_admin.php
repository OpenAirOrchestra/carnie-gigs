<?php

/*
 * Renders meta box for carnie gig... admin UI
 */
class carnieGigsMetaFormView {

	/*
	 * Render form for carnie gigs meta box
	 */
	function render($post, $metabox) { 
		echo "(http://matth.eu/wordpress-date-field-plugin)"; 
		echo " (http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html) "; 

		$metabox['args']['metadata_prefix'];

		// From http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
	       
		// Use nonce for verification
		echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

		echo '<table class="form-table">';
		
		foreach ($metabox['args']['metadata_fields'] as $field) {
			// get current post metadata
			$meta = get_post_meta($post->ID, $field['id'], true);
			echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>', 
				'<td>';
			
			switch ($field['type']) {
				case 'text':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', ' ', $field['desc'];
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', ' ', $field['desc'];
					break;

			}

			echo '     <td>';
			echo '</tr>';

		}

	    	echo '</table>';


	}
}
?>
