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

    <h3>Mirror Database</h3>

	<p>
	The Carnie Gigs plugin can mirror it's gig data to a flat table in 
	another database.  The wordpress database user must have permissions
	to connect to and write to that database.
	</p>
	<p>
	Certain features of the Carnie Gigs plugin, such as export to 
	csv and iCal entries are only available if there is a mirror database
	set up.  That is because it is much more efficient to query a flat
	database to extract that data, and because the code for those features
	has not been updated to search the custom posts and their associated
	post metadata yet.
	</p>

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
