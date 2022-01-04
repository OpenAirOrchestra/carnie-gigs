<?php

/*
 * This class handles simple rendering the form for exporting to CSV.
 */
class carnieCsvExportView
{

	/*
	 * Given a record, render a form for exporting the
	 * table in csv form
	 */
	function exportForm($gig)
	{
		$today = date("j M o", time() - 8 * 60 * 60 /* we are GMT-8 */);
		$long_ago = ("1 Jan 2001");
		$action = carnieUtil::get_url() . "csv.php"
?>
		<p>Export gig details for a range of dates:</p>
		<form name="export_csv_form" method="POST" <?php
													echo 'action="' . $action . '">';
													echo '<input type="hidden" name="carnie-gigs-csv-verify-key" id="carnie-gigs-csv-verify-key"
						value="' . wp_create_nonce('carnie-gigs') . '" />';
													?> <label for="from">From:</label>
			<input name="from" value="<?php echo $long_ago; ?>" />
			<label for="from">To:</label>
			<input name="to" value="<?php echo $today; ?>" />
			<p class="submit"><input type="submit" name="submit" class="button" value="Download Export File" />
			<?php

			print "</form>\n";
		}

		function exportVerifiedAttendanceForm()
		{
			$today = date("j M o", time() - 8 * 60 * 60 /* we are GMT-8 */);
			$long_ago = ("1 Jan 2001");
			$action = carnieUtil::get_url() . "verified_attendance_csv.php"
			?>
			<p>Export gig attendance for a range of dates:</p>
			<form name="export_csv_form" method="POST" <?php
														echo 'action="' . $action . '">';
														echo '<input type="hidden" name="verified-attendance-csv-verify-key" id="verified-attendance-csv-verify-key"
						value="' . wp_create_nonce('verified-attendance') . '" />';
														?> <label for="from">From:</label>
				<input name="from" value="<?php echo $long_ago; ?>" />
				<label for="from">To:</label>
				<input name="to" value="<?php echo $today; ?>" />

				<p class="submit"><input type="submit" name="submit" class="button" value="Download Exported Verified Attendance File" />
			<?php

			print "</form>\n";
		}
	}
			?>