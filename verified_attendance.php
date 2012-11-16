<?php

/*
 * this php is called directly to do iphone friendly verified attendance form
 */
require_once 'ajaxSetup.php';

require_once 'model/verified_attendees.php';

/*
 * class to process or render an iphone friendly verified attendance form
 */
class gigAttendees {
	
	/*
 	 * Processes posted data
	 */
	function process_post() {

		$attendance_nonce = $_REQUEST['attendance_nonce'];
		if ( wp_verify_nonce($attendance_nonce, 'attendance_nonce') ) {
			if ($_POST['submit']) {
				global $wpdb;

				$gig_id = $_POST['gig'];
				
				if (! $gig_id) {
					wp_die('Missing Gig ID');	
				}

				$table_name = $wpdb->prefix . "gig_attendance";

				for ($i = 1; $i < count($_POST); ++ $i) {
				
					$id = $_POST[ 'id_' . $i ];
				
					$attending = $_POST[ 'attending_' . $i ];
					$user_id = $_POST[ 'user_id_' . $i ];
					$firstname = $_POST[ 'firstname_' . $i ];
					$lastname = $_POST[ 'lastname_' . $i ];
					$notes = $_POST[ 'notes_' . $i ];

					$data = array();
					$format = array();

					$data['gigid'] = $gig_id;
					array_push($format, "%d");
					$data['user_id'] = $user_id;
					array_push($format, "%d");
					$data['firstname'] = $firstname;
					array_push($format, "%s");
					$data['lastname'] = $lastname;
					array_push($format, "%s");
					$data['notes'] = $notes;
					array_push($format, "%s");


					if ($attending && !$id && ($user_id || $firstname || $lastname)) {
						$wpdb->insert( $table_name,
							$data,
							$format);
					} else if ($attending && $id) {
						$wpdb->update( $table_name,
							$data,
							array ( 'ID' => $id),
							$format);
					} else if (!$attending && $id) {
						$sql = $wpdb->prepare("DELETE FROM `$table_name` WHERE id = %d", $id);
						$wpdb->query( $sql );
					}

				}

				// Deal with mirror database!
				$verified_attendees_database = new verifiedAttendeesDatabase;
				$verified_attendees_database->mirror_post($gig_id);
			}
		}
	}

	/* 
	 * Returns attendees of gig in database rows
	 * sorted by last name
	 */
	function attendee_rows( $gig_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "gig_attendance";

		$sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE gigid = %d ORDER BY `lastname`", $gig_id);
		$rows = $wpdb->get_results( $sql, ARRAY_A );

		return $rows;
	}

	/* 
	 * Returns attendees of gig in an associative array 
	 * indexed by user_id
	 */
	function attendees( $gig_id, $rows ) {

		$attendees = array();

		foreach ($rows as $row) {
			if ($row['user_id']) {
				$attendees[$row['user_id']] = $row;
			}
		}

		return $attendees;
	}

	/* 
 	 * render page contents for workshop attendees
	 */
	function render_contents( $gig_id ) {

		global $wpdb;
		$table_name = $wpdb->prefix . "workshops";

		$this->process_post();

		if (! $gig_id) {
			wp_die('Missing Gig ID');	
		}

		if ($gig_id) {
		
			// Get post
			$post = get_post($gig_id, ARRAY_A);

			$url = get_permalink( $gig_id )
?>
		<h1>
			Attendance for 
			<a href="<?php echo $url;?>"><?php echo htmlentities(stripslashes($post['post_title'])); ?></a>
		</h1>
<?php
		} else {
			wp_die('missing gig id');
		}

		$attendee_rows = array();

		$attendee_rows = $this->attendee_rows( $gig_id );

		$this->render_form($gig_id, $attendee_rows);
	}

	/*
	 * renders form
	 */
	function render_form( $gig_id, $attendee_rows ) {

		$attendees = $this->attendees( $gig_id, $attendee_rows );

		global $wpdb;

		$attendance_nonce = wp_create_nonce('attendance_nonce');
?>
	<form method="post" action="<?php echo plugins_url( 'verified_attendance.php', __FILE__ );?>">
		<input type="hidden" name="attendance_nonce" value="<?php echo $attendance_nonce; ?>" />
<?php
		if ($gig_id) {
?>
		<input type="hidden" name="gig" value="<?php echo $gig_id;?>">
<?php
		}

?>
	<h2><a name="#participants">Participants</a></h2>
	<table>
<?php
	$authors = get_users('orderby=nicename');
	$count = 0;
	$rendered_ids = array();
	foreach ($authors as $user) {
		if ($user->ID != 1) {
			$count = $count + 1;
			$name = $user->display_name;
			$user_info = get_userdata($user->ID);
			$attendance_info = $attendees[$user->ID];
			$checked = '';
			$class = "absent";

			array_push($rendered_ids, $user->ID);
			
			if ($attendance_info) {
				$checked = 'checked = "checked"';
				$class = "present";
			}
			if ($user_info->first_name || $user_info->last_name) {
				$name = $user_info->first_name . ' ' . $user_info->last_name;
			}

			
?>
			<tr onclick="selectRow(this)" class="<?php echo $class; ?>"><td>
				<input onclick="checkClicked(this, event)" type="checkbox" name="attending_<?php echo $count; ?>" value="attending" <?php echo $checked; ?> ><?php echo $name; ?></input>
<?php
			if ($user_info->user_description) {
?>
			<div class="details">

				<?php echo $user_info->user_description; ?>
			</div>
<?php
			}
?>
			</td>
			<input type="hidden" name="user_id_<?php echo $count; ?>" value="<?php echo $user->ID; ?>"/>
<?php
			if ($user_info->first_name) {
?>
				<input type="hidden" name="firstname_<?php echo $count; ?>" value="<?php echo $user_info->first_name; ?>"/>
<?php
			}
			if ($user_info->last_name && strlen($user_info->last_name)) {
?>
				<input type="hidden" name="lastname_<?php echo $count; ?>" value="<?php echo $user_info->last_name; ?>"/>
<?php
			} else {
?>
				<input type="hidden" name="lastname_<?php echo $count; ?>" value="<?php echo $user->display_name; ?>"/>
<?php
			}
			if ($attendance_info) {
?>
				<input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo $attendance_info["id"]; ?>"/>
<?php
			}
?>

			</tr>
<?php
		}
	}
?>
	</table>

	<h2><a name="#otherfolks">Other Folks</a></h2>
	<table>
<?php
	$class = "present";
	foreach ($attendee_rows as $attendee) {
		if (! in_array($attendee['user_id'], $rendered_ids)) {
			$count = $count + 1;
?>
			<tr onclick="selectRow(this)" class="<?php echo $class; ?>"><td>
				<input onclick="checkClicked(this, event)" type="checkbox" checked="checked" name="attending_<?php echo $count; ?>" value="attending" <?php echo $checked; ?> ><?php echo $attendee['firstname'] . " " . $attendee['lastname']; ?></input>

<?php
			if ($attendee['notes']) {
?>
			<div class="details">

				<?php echo $attendee['notes']; ?>
			</div>
<?php
			}
?>

</td>
				<input type="hidden" name="firstname_<?php echo $count; ?>" value="<?php echo $attendee['firstname']; ?>"/>
				<input type="hidden" name="lastname_<?php echo $count; ?>" value="<?php echo $attendee['lastname']; ?>"/>
				<input type="hidden" name="notes_<?php echo $count; ?>" value="<?php echo stripslashes($attendee['notes']); ?>"/>
				<input type="hidden" name="id_<?php echo $count; ?>" value="<?php echo stripslashes($attendee['id']); ?>"/>


			</tr>
<?php
		}
	}
	$count = $count + 1;
?>
	</table>
	<dl>
				<dt>First Name</dt>
				<dd><input type="text" name="firstname_<?php echo $count; ?>"/></dd>
				<dt>Last Name</dt>
				<dd><input type="text" name="lastname_<?php echo $count; ?>"/></dd>
				<dt>Notes</dt>
				<dd><textarea name="notes_<?php echo $count; ?>"></textarea></dd>
				<input type="hidden" name="attending_<?php echo $count; ?>" value="attending"/>
	</dl>
			<p><input name="submit" type="submit" value="Update Attendance"></p>
		</form>
<?php
	}

}

// instantiate class
$WORKSHOPATTENDEES = new gigAttendees;

header("Content-Type: text/html");

$gig_id = $_REQUEST['gig'];

?>
<html>
	<head>
		<script type="text/javascript" src="js/attendance.js"></script>
		<LINK href="css/attendance.css" rel="stylesheet" type="text/css">
		<meta name="viewport" content="width=device-width" />
	</head>
<?php
		if ($_POST['submit']) {
?>
	<body onload="window.scrollTo(0, document.height);">
<?php
		} else {
?>
	<body>
<?php
		}
?>
<?php
	$gig_id = $_POST['gig'];

	// TODO: check instead if user can edit post associated with gig_id
	if (current_user_can('edit_post', $gig_id)) {
		$WORKSHOPATTENDEES->render_contents($gig_id);
	} else {
		echo "<h1>Security Error: insufficient privileges</h1>";
		echo "Current user cannot edit " . $gig_id;
	}
?>
	</body>
</html>
<?php
?>
