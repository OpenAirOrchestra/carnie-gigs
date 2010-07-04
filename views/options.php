<?php

/*
 * Renders options page for carnie gigs plugin... admin UI
 */
class carnieGigsOptionsView {

	/*
	 * Render options page
	 */
	function render() { 
?>
<div class="wrap">
<h2>Carnie Gigs Plugin Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'carnie-gigs-settings-group' ); ?>
    <table class="form-table">
	            <tr valign="top">
		            <th scope="row">Mirror Host</th>
			            <td><input type="text" name="mirror_host" value="<?php echo get_option('mirror_host'); ?>" /></td>
				            </tr>
		            <th scope="row">Mirror Database</th>
			            <td><input type="text" name="mirror_database" value="<?php echo get_option('mirror_database'); ?>" /></td>
				            </tr>
					             
        <tr valign="top">
        <th scope="row">Mirror Table</th>
        <td><input type="text" name="mirror_table" value="<?php echo get_option('mirror_table'); ?>" /></td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php
	}
}
?>
