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

	private $count = 0;  //tracks attendee count
	private $sticky_types = array();
	/*
 	 * Processes posted data
	 */
	function process_post() {

		$attendance_nonce = $_REQUEST['attendance_nonce'];
		
		if ( wp_verify_nonce($attendance_nonce, 'attendance_nonce') ) {
			if ($_POST['Update']) {
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
					//echo $user_id . " ". $firstname . " " . $lastname . " ";
					if ($firstname != "First Name (Required)") //don't insert New Folk data if first name not entered
					{
						$data = array();
						$format = array();


						//none of these are required so clear if not changed
						if ($lastname == "Last Name") {$lastname = "";}

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
					// Remember tab type (we'll make it sticky)
					if ($user_id) {
						$sticky_type = $_POST[ 'sticky_type' . $i ];
						$this->sticky_types[$user_id] = $sticky_type;
					}
					
					// Deal with mirror database!
					$verified_attendees_database = new verifiedAttendeesDatabase;
					$verified_attendees_database->mirror_post($gig_id);
				}
			}

		}
	}

	/* 
	 * Returns users in database rows
	 * sorted by first_name or display_name
	 */
	function users($type = NULL) {
		global $wpdb;

		$users_name = $wpdb->prefix . "users";
		$usermeta_name = $wpdb->prefix . "usermeta";
		$gig_attendance_name = $wpdb->prefix . "gig_attendance";
		$gig_name = $wpdb->prefix . "posts";
		$sqlFilter = ""; //$type == "recent"
		$sixMonthFilter = ""; //default unfiltered, i.e. $type = NULL

		if ($type == "remaining" || $type == "recent") {
			// Filtered users sql query.
			if ($type == "remaining")
			{
				$sqlFilter = "NOT";
			}
			$sixMonthFilter = "
				AND u.id " . $sqlFilter . " IN 
				(
					SELECT DISTINCT a.user_ID
					FROM `$gig_attendance_name` a
					JOIN `$gig_name` w ON w.id = a.gigid 
					AND w.post_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
				) ";
		}

		$sql = $wpdb->prepare("SELECT display_name, u.ID, user_email
								FROM  `$users_name` u
								JOIN  `$usermeta_name` m ON u.id = m.user_id AND m.meta_key =  'first_name'
								JOIN  `$usermeta_name` m2 ON u.id = m2.user_id AND m2.meta_key IN ('wp_capabilities')								
								WHERE u.id <> 1 
								$sixMonthFilter 
								AND 
									(
										m2.meta_value LIKE '%%author%%'
										OR m2.meta_value LIKE '%%editor%%'
										OR m2.meta_value LIKE '%%administrator%%'
									)
								ORDER BY COALESCE( NULLIF( m.meta_value,  '' ) , display_name )", $gig_id);
		
		$users = $wpdb->get_results( $sql, ARRAY_A );
		return $users;
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
 	 * render page contents for gig attendees
	 */
	function render_contents( $gig_id ) {

		global $wpdb;


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
	 * render attendee tab, recent or remaining
	 */
	 
	function render_attendees($type, $attendees, $rendered_ids)
	{
?>
		<table>
<?php
		$users = array();
		
		// Use database to filter if we are rendering for the
		// first time, use sticky types from form data if
		// we are processing a post.
		$filter_sticky = false;
		if (count($this->sticky_types)) {
			$filter_sticky = true;
			$users = $this->users();
		} else {
			$users = $this->users($type);
		}





		foreach ($users as $user) {
		
			if (!$filter_sticky || 
			    !$user['ID'] ||
 			    ($type == $this->sticky_types[$user['ID']])) {
				$this->count = $this->count + 1;
				$name = $user['display_name'];
				$user_info = get_userdata($user['ID']);
				$attendance_info = $attendees[$user['ID']];
				$checked = '';
				$class = "absent";
				
				array_push($rendered_ids, $user['ID']);
				
				if ($attendance_info) {
					$checked = 'checked = "checked"';
					$class = "present";
				}
				if ($user_info->first_name || $user_info->last_name) {
					$name = $user_info->first_name . ' ' . $user_info->last_name;
				}

	?>
				<tr onclick="selectRow(this)" class="<?php echo $class; ?>"><td>
					<input onclick="checkClicked(this, event)" type="checkbox" name="attending_<?php echo $this->count; ?>" value="attending" <?php echo $checked; ?> ><?php echo $name; ?></input>
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
				<input type="hidden" name="user_id_<?php echo $this->count; ?>" value="<?php echo $user['ID']; ?>"/>
	<?php
				if ($user_info->first_name) {
	?>
					<input type="hidden" name="firstname_<?php echo $this->count; ?>" value="<?php echo $user_info->first_name; ?>"/>
	<?php
				}
				if ($user_info->last_name && strlen($user_info->last_name)) {
	?>
					<input type="hidden" name="lastname_<?php echo $this->count; ?>" value="<?php echo $user_info->last_name; ?>"/>
	<?php
				} else {
	?>
					<input type="hidden" name="lastname_<?php echo $this->count; ?>" value="<?php echo $user['display_name']; ?>"/>
	<?php
				}
				if ($attendance_info) {
	?>
					<input type="hidden" name="id_<?php echo $this->count; ?>" value="<?php echo $attendance_info["id"]; ?>"/>
	<?php
				}
	?>
					<input type="hidden" name="email_<?php echo $this->count; ?>" value="<?php echo $user['user_email']; ?>"/>
					<input type="hidden" name="sticky_type<?php echo $this->count; ?>" value="<?php echo $type; ?>"/>
				</tr>
	<?php
			}
		}
?>
		</table>
<?php
		return $rendered_ids;
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
		<input id="Update" type="submit" name="Update" value="Update">
<?php
		if ($gig_id) {
?>
		<input type="hidden" name="gig" value="<?php echo $gig_id;?>">
<?php
		}

?>
<br>
<br>
<div class="tabs">
	<ul>
<?php 
	if (isset($_POST['current_tab']))	
	{
		$current_tab = $_POST['current_tab'];
	}
	else
	{
		$current_tab = "#recent";
	}
?>
	<input type="hidden" id="current_tab" name="current_tab" value="<?php echo $current_tab;?>" />
	<li><a href="#recent"><h3>Recent</h3></a></li>
	<li><a href="#remaining"><h3>Remaining</h3></a></li>
	<li><a href="#otherfolks"><h3>Other Folks</h3></a></li>
	</ul>
	<div>
	<div id="recent" class="tab-content">
<?php
	$rendered_ids = array();
	$rendered_ids = $this->render_attendees("recent", $attendees, $rendered_ids);
?>	
	</div>
	<div id="remaining" class="tab-content">
<?php
	$rendered_ids = $this->render_attendees("remaining", $attendees, $rendered_ids);
	$this->count = $this->count + 1;
?>	
	</div>
	<div id="otherfolks" class="tab-content">
	<dl>
				<dd><input type="text" name="firstname_<?php echo $this->count; ?>" title="First Name (Required)"/></dd>
				<dd><input type="text" name="lastname_<?php echo $this->count; ?>" title="Last Name"/></dd>
				<dd>Notes<br><textarea name="notes_<?php echo $this->count; ?>"></textarea></dd>
				<input type="hidden" name="attending_<?php echo $this->count; ?>" value="attending"/>
	</dl>
	<table>
<?php
	$class = "present";
	foreach ($attendee_rows as $attendee) {
		if (! in_array($attendee['user_id'], $rendered_ids)) {
			$this->count = $this->count + 1;
?>
			<tr onclick="selectRow(this)" class="<?php echo $class; ?>"><td>
				<input onclick="checkClicked(this, event)" type="checkbox" checked="checked" name="attending_<?php echo $this->count; ?>" value="attending" <?php echo $checked; ?> ><?php echo $attendee['firstname'] . " " . $attendee['lastname']; ?></input>
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
				<input type="hidden" name="user_id_<?php echo $this->count; ?>" value="<?php echo $attendee['user_id']; ?>"/>
				<input type="hidden" name="firstname_<?php echo $this->count; ?>" value="<?php echo $attendee['firstname']; ?>"/>
				<input type="hidden" name="lastname_<?php echo $this->count; ?>" value="<?php echo $attendee['lastname']; ?>"/>
				<input type="hidden" name="notes_<?php echo $this->count; ?>" value="<?php echo stripslashes($attendee['notes']); ?>"/>
				<input type="hidden" name="id_<?php echo $this->count; ?>" value="<?php echo stripslashes($attendee['id']); ?>"/>
			</tr>
<?php
		}
	}
	$this->count = $this->count + 1;
?>
	</table>
	</div>
	</div>	
</div>
<script type="text/javascript">

$(".tab-content").hide();
$("<?php echo $current_tab; ?>").fadeIn();
var selected_tab = $("a[href='<?php echo $current_tab; ?>']").parent();
selected_tab.addClass("current");

$(document).ready(function(){
	//tab functionality
	$(".tabs li").click(function() {
		$(this).parent().parent().find(".tab-content").hide();
		var selected_tab = $(this).find("a").attr("href");
		$(selected_tab).fadeIn();
		$(this).parent().find("li").removeClass('current');
		$(this).addClass("current");
		$("input#current_tab").val(selected_tab);
		return false;
    });

	//grey input functionality
    var inputs = $('input[type=text]');
    inputs.each(function(){
        $(this).val($(this).attr('title')).addClass('unfocused');
    });
    inputs.focus(function(){
        var input = $(this);
        if(input.val() == input.attr('title')){
            $(this).removeClass('unfocused').val('');
        }
    });
    inputs.blur(function(){
        var input = $(this);
        if(input.val() == ''){ // User has not placed text
            input.val(input.attr('title')).addClass('unfocused');
        }
    }); 	
});</script>
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
		<script type="text/javascript" src="js/formUI.js"></script>
		<link rel="stylesheet" type="text/css" href="css/attendance.css" >
		<link rel="stylesheet" type="text/css" href="css/tab.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
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
	if (current_user_can('edit_post', $gig_id)) {
		$WORKSHOPATTENDEES->render_contents($gig_id);
	} else {
		echo "<p>Security Error: insufficient privileges</p>";
		echo "Current user cannot edit " . $gig_id;
	}
?>
	</body>
</html>
<?php
?>
