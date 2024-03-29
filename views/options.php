<?php

/*
 * Renders options page for carnie gigs plugin... admin UI
 */
class carnieGigsOptionsView
{

	/*
	 * Render options page
	 */
	function render()
	{
?>
		<div class="wrap">
			<h2>Carnie Gigs Plugin Settings</h2>

			<form method="post" action="options.php">

				<h3>Mirror Database</h3>

				<p>
					The Carnie Gigs plugin can mirror it's gig data to a flat table.
				</p>
				<p>
					Certain features of the Carnie Gigs plugin, such as export to
					csv and iCal entries are only available if there is a mirror table
					set up. That is because it is much more efficient to query a flat
					database to extract that data, and because the code for those features
					has not been updated to search the custom posts and their associated
					post metadata yet.
				</p>

				<?php settings_fields('carnie-gigs-settings-group'); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">Mirror Table</th>
						<td><input type="text" name="carniegigs_mirror_table" value="<?php echo get_option('carniegigs_mirror_table'); ?>" /></td>
					</tr>
				</table>

				<h3>Recents History Length</h3>

				<p>
					The number of entries in the attendance table that are used when building the
					"Recents" list for taking attendance.
				</p>

				<table class="form-table">
					<tr valign="top">
						<th scope="row">Recents History Length</th>
						<td><input type="number" name="carniegigs_recents_history_length" value="<?php echo get_option('carniegigs_recents_history_length'); ?>" /></td>
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