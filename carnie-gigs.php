<?php
/*
Plugin Name: Carnie Gigs
Plugin URI: http://members.thecarnivalband.com
Description: A gig calendar plugin for The Carnival Band 
Version: 0.4
Author: Open Air Orchestra Webmonkey
Author URI: mailto://oaowebmonkey@gmail.com
License: GPL2
*/

/*  Copyright 2010  Open Air Orchestra  (email : oaowebmonkey@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USAi
*/

$include_folder = dirname(__FILE__);
require_once $include_folder . '/version.php';
require_once $include_folder . '/utility.php';
require_once $include_folder . '/views/meta_box_admin.php';
require_once $include_folder . '/views/gig.php';
require_once $include_folder . '/views/options.php';
require_once $include_folder . '/views/export_csv_form.php';
require_once $include_folder . '/controllers/meta_box_admin.php';
require_once $include_folder . '/controllers/attendance.php';
require_once $include_folder . '/model/fields.php';
require_once $include_folder . '/model/mirror_database.php';

/*
 * Main class for carnie gigs calenter.  Handles activation, hooks, etc.
 */
class carnieGigsCalendar {

	private $carnie_gigs_meta_form_view,
		$carnie_gigs_meta_form_controller,
		$carnie_mirror_database,
		$carnie_gig_attendance_controller,
		$carnie_gig_view,
		$metadata_prefix,
		$metadata_fields,
		$published_post_ID;

	/*
	 * Constructor
	 */
	function __construct() {
		$carnie_fields = new carnieFields;
	
		$this->metadata_prefix = $carnie_fields->metadata_prefix;
		$this->metadata_fields = $carnie_fields->metadata_fields;
	}
	   
	/*
	 * Activate the plugin.  
	 * Migrate any data from legacy table
	 */
	function activate () {
		$old_version = get_option("carniegigs_db_version");


		if ($old_version) {
			if ($old_version < CARNIE_GIGS_DB_VERSION) {
				// $this->migrate_legacy_data();
				update_option("carniegigs_db_version", CARNIE_GIGS_DB_VERSION);
			}
		} else {
			add_option("carniegigs_db_version", CARNIE_GIGS_DB_VERSION);
		}

	}

	/*
	 * Migrates any legacy data in old gig database table
	 */
	function migrate_legacy_data () {

		$database_host = DB_HOST;
		$database_name = "wordpress_cbm";
		$database_user = DB_USER;
		$database_password = DB_PASSWORD;
		$legacy_wpdb = new wpdb( $database_user, $database_password, $database_name, $database_host ) or wp_die ('could not connect');

		$table_name = "wp_carniegigs";
		$select = "SELECT * FROM " . $table_name;
		$results = $legacy_wpdb->get_results( $select, ARRAY_A );

		foreach ($results as $gig) {
			if ($gig['postid']) {
				// Remove original, legacy post (if any)
				wp_delete_post( $gig[$postid], TRUE );
			}
			// Create new post
			$post = array('post_status' => 'publish', 
				'post_title' => $gig['title'], 
				'post_content' => $gig['description'],
				'post_type' => 'gig'
			);
			$gigtime = strtotime($gig['date']);
			if ($gigtime < time()) {
				$post['post_date'] = date("Y-m-d H:i:s", $gigtime);
			}
			
			$postid = wp_insert_post( $post );

			// Create associated metadata fields
			if (! $this->carnie_gigs_meta_form_controller) {
				$this->carnie_gigs_meta_form_controller = new carnieGigsMetaFormController;
			}
			$this->carnie_gigs_meta_form_controller->save_metadata($postid, $this->metadata_fields, $this->metadata_prefix, $gig);
		}
	}

	/*
	 * Create custom post type 
	 */
	function create_post_type() {
		register_post_type( 'gig',
			array(
				'labels' => array(
					'name' => __( 'Gigs' ),
					'singular_name' => __( 'Gig' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __( 'Add New Gig' ),
					'edit' => __( 'Edit' ),
					'edit_item' => __( 'Edit Gig' ),
					'new_item' => __( 'New Gig' ),
					'view' => __( 'View Gig' ),
					'view_item' => __( 'View Gig' ),
					'search_items' => __( 'Search Gigs' ),
					'not_found' => __( 'No gigs found' ),
					'not_found_in_trash' => __( 'No gigs found in Trash' ),
					'parent' => __( 'Parent Gig' ),
					),
				'description' => 'A gig is a scheduled Carnival Band Performance',
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'gig',
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'menu_position' => 5,
				'menu_icon' => carnieUtil::get_url() . "/images/saxophone16.png",
				'supports' => array( 'title', 'editor', 'revisions', 'author', 'excerpt', 'comments' ),
				'taxonomies' => array( 'post_tag', 'category '),
				'register_meta_box_cb' => array( $this, 'register_meta_box'),

			)
		);
	}

	/*
	 * Filter for home page to add gigs
	 */
	function pre_get_posts( $query ) {
		
		if ( is_home() || is_feed() ) {
			$query->set( 'post_type', array( 'post', 'gig' ));
		}
	}

	/*
	 * Resister meta box
	 */
	function register_meta_box() {
		if (! $this->carnie_gigs_meta_form_view) {
			$this->carnie_gigs_meta_form_view = new carnieGigsMetaFormView;
		}

		// remove_meta_box() and add_meta_box() calls.
		add_meta_box("carnie-gig-meta", 
			"Gig Details", 
			array($this->carnie_gigs_meta_form_view, 'render'),
			"gig", "normal", "high",
			array('metadata_prefix' => $this->metadata_prefix,
			      'metadata_fields' => $this->metadata_fields)
		);
	}

	/*
	 * Save post metadata, only for a carnie gig
	 */
	function save_post_data($post_id) {
		// Is the post a gig?
		$post = get_post($post_id);
		if (get_post_type($post) == 'gig') {

			if (! $this->carnie_gigs_meta_form_controller) {
				$this->carnie_gigs_meta_form_controller = new carnieGigsMetaFormController;
			}
			$this->carnie_gigs_meta_form_controller->save_data($post_id, 
				$this->metadata_fields, 
				$this->metadata_prefix);

			if (! $this->carnie_mirror_database) {
				$this->carnie_mirror_database = new carnieMirrorDatabase;
			}

			// Update the database.
			$the_post_id = wp_is_post_revision( $post );
			if ($the_post_id) {
				$post_id = $the_post_id;
				$post = get_post($post_id);
			}
			if (get_post_status($post_id) == 'publish') {
				$this->carnie_mirror_database->save_post($post, 
					$this->metadata_fields,
					$this->metadata_prefix);
			}
		}
	}

	/*
	 * Called when a post is deleted.
	 */
	function deleted_post($post_id) {
		$post = get_post($post_id);
		if (get_post_type($post) == 'gig') {
			if (! $this->carnie_mirror_database) {
				$this->carnie_mirror_database = new carnieMirrorDatabase;
			}
			$this->carnie_mirror_database->delete_post($post_id); 
		}
	}

	/*
	 * Called when a post is trashed.
	 */
	function trashed_post($post_id) {
		$post = get_post($post_id);
		if (get_post_type($post) == 'gig') {
			if (! $this->carnie_mirror_database) {
				$this->carnie_mirror_database = new carnieMirrorDatabase;
			}
			$this->carnie_mirror_database->delete_post($post_id); 
		}
	}

	/*
	 * Called after a post is removed from the trash
	 */
	function untrashed_post($post_id) {
		$post = get_post($post_id);
		if (get_post_type($post) == 'gig' && get_post_status($post_id) == 'publish') {
			if (! $this->carnie_mirror_database) {
				$this->carnie_mirror_database = new carnieMirrorDatabase;
			}
			$post = get_post($post_id);
			$this->carnie_mirror_database->save_post($post, 
				$this->metadata_fields,
				$this->metadata_prefix);
		}
	}

	/*
	 * Filter for the content.  If displaying gig, and not in an
	 * admin page, add our custom metadata
	 */
	function the_content($content) {
		$post = get_post(get_the_id());

		if ($post && get_post_type($post) == 'gig' ) {
			if ($_POST['gigattendance'] && $_POST['gigid'] == $post->ID) {
				// process gig attendance
				if (! $this->carnie_gig_attendance_controller) {
					$this->carnie_gig_attendance_controller = new carnieGigAttendanceController;
				}
				$this->carnie_gig_attendance_controller->handle_post($post->ID, $this->metadata_fields, $this->metadata_prefix);
			}
			if (! $this->carnie_gig_view) {
				$this->carnie_gig_view = new carnieGigView;
			}
			// render metadata for gig
			$content = $this->carnie_gig_view->the_content($content, $this->metadata_prefix, $this->published_post_ID);
		}

		return $content;
	}

	/*
	 * Create admin menu(s) for this plugin.  
	 * The admin menu gets us to managing options.
	 *
	 * http://codex.wordpress.org/Creating_Options_Pages
	 */
	function create_admin_menu() {
		
		// Add options page
		add_options_page('Carnie Gigs Plugin Settings', 'Carnie Gigs Settings', 'manage_options', 'carnie-gigs-options', array($this, 'options_page'));

		// Add tools page
		add_management_page('Export Carnie Gigs', 'Export Carnie Gigs', 'read_private_gigs', 'export-carnie-gigs-tools', array($this, 'export_gigs_page'));
		
		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));
	}

	/*
	 * Register settings for this plugin
	 */
	function register_settings() {
		register_setting( 'carnie-gigs-settings-group', 'carniegigs_mirror_table' );
	}

	function options_page() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		} 

		$carnie_gigs_options_view = new carnieGigsOptionsView;
		$carnie_gigs_options_view->render();
	}

	function export_gigs_page() {
		if (!current_user_can('read_private_gigs'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		} 

		echo '<div class="wrap">';
		echo "<h2>Export Carnie Gigs</h2>";
		echo "<p>When you click the button below WordPress will create a CSV file for you to save to your computer.</p>";
		echo "<p>Once you have saved the download file, you can load  into a spreadsheet program like Excel.</p>";

		if (! $this->carnie_mirror_database) {
			$this->carnie_mirror_database = new carnieMirrorDatabase;
		}
		   
		if ($this->carnie_mirror_database->mirror_specified()) {
			$gig = $this->carnie_mirror_database->one_gig();
			$exportCsvFormView = new carnieCsvExportView;
			$exportCsvFormView->exportForm($gig[0]);
		} else {
			echo "<p>Create a mirror database to use this feature. See settings for this plugin.</p>";
		}
		echo '</div>';
	}
	/*
	 * Called whenver one of the options related to the mirror
	 * database is changed
	 */
	function mirror_database_changed() {
		if (! $this->carnie_mirror_database) {
			$this->carnie_mirror_database = new carnieMirrorDatabase;
		}
		$this->carnie_mirror_database->rebuild($this->metadata_fields, 
			$this->metadata_prefix);
	}

	/*
	 * Handles carniegigs shortcode
	 * examples:
	 * [carniegigs] 
         * [carniegigs time="past"] 
         * [carniegigs time="future"] 
	 */
	function carniegigs_shortcode_handler($atts, $content=NULL, $code="") {
		extract( shortcode_atts( array(
			'time' => 'all',
			'display' => 'short'), $atts ) );
		if (! $this->carnie_mirror_database) {
			$this->carnie_mirror_database = new carnieMirrorDatabase;
		}
		   
		$gigs = array();
		if ($time == 'past') {
			$gigs = $this->carnie_mirror_database->past_gigs();
		} else if ($time == 'future') {
			$gigs = $this->carnie_mirror_database->future_gigs();
		} else {
			$gigs = $this->carnie_mirror_database->all_gigs();
		}

		if (! $this->carnie_gig_view) {
			$this->carnie_gig_view = new carnieGigView;
		}
		
		$this->carnie_gig_view->shortGigs($gigs);
	}

	/*
	 * Filter for subscribe2 post types
	 * the s2_post_types filter to allow for custom post types in WP 3.0
	 * for the Subscribe2 plugin to send notifications for our
	 * custom post type.
	 */
	function s2_post_types( $s2_post_types ) {
		$carnieTypes = array( 'gig' );

		return array_merge($s2_post_types, $carnieTypes);
	}

	/*
	 * Stash the post when transitioning it to 'publish' for
	 * later use in the_content filter if we are in the
	 * context of the Subscribe2 plugin sending email notification
	 */
	function transition_post_status($new_status, $old_status, $post) {
		if ($new_status == 'publish' && get_post_type($post) == 'gig') {
			// This runs before the
			// save_post hook, before we have our
			// metadata saved.  
			// The subscribe2 hooks are on publish, so
			// they run and send email before we have
			// the metadata saved. 
			//

			// Stash the id of the post for possible later use
			// by the_content hook called by Subscribe2.
			$this->published_post_ID = $post->ID;
		}
	}

	/*
	 * Columns filter for the admin screen listing gigs
	 */
	function manage_gig_columns($columns) {

		unset($columns['categories']);
		unset($columns['tags']);
		unset($columns['comments']);

		$columns['gig_coordinator'] = "Co-ordinator";
		$columns['gig_date'] = "Performance Date";
		$columns['gig_status'] = "Status";

		return $columns;
	}

	/*
	 * Provide data for custom columns
	 */
	function manage_gig_custom_columns($column) {
		global $post;
		if ($column == 'gig_date') {
			$date = get_post_meta($post->ID, $this->metadata_prefix . 'date', true);
			if ($date) {
				echo $date;
			}
			
		} else if ($column == 'gig_coordinator') {
			$coordinator = get_post_meta($post->ID, $this->metadata_prefix . 'coordinator', true);
			echo $coordinator;
		} else if ($column == 'gig_status') {
			$cancelled = get_post_meta($post->ID, $this->metadata_prefix . 'cancelled', true);
			$tentative = get_post_meta($post->ID, $this->metadata_prefix . 'tentative', true);
			$closedcall = get_post_meta($post->ID, $this->metadata_prefix . 'closedcall', true);
			$privateevent = get_post_meta($post->ID, $this->metadata_prefix . 'privateevent', true);

			if (strlen($cancelled)) {
				echo "cancelled ";
			}
			if (strlen($tentative)) {
				echo "tentative ";
			}
			if (strlen($closedcall)) {
				echo "closed&nbsp;call ";
			}
			if (strlen($privateevent)) {
				echo "private&nbsp;event";
			}
		}
	}
}


$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// shortcodes
add_shortcode('carniegigs', array($CARNIEGIGSCAL, 'carniegigs_shortcode_handler'));

// actions
add_action('init',  array($CARNIEGIGSCAL, 'create_post_type'));
add_action('save_post', array($CARNIEGIGSCAL, 'save_post_data'));
add_action('deleted_post', array($CARNIEGIGSCAL, 'deleted_post'));
add_action('trashed_post', array($CARNIEGIGSCAL, 'trashed_post'));
add_action('untrashed_post', array($CARNIEGIGSCAL, 'untrashed_post'));
add_action('admin_menu', array($CARNIEGIGSCAL, 'create_admin_menu'));
add_action('update_option_carniegigs_mirror_table', array($CARNIEGIGSCAL, 'mirror_database_changed'));
add_action('transition_post_status', array($CARNIEGIGSCAL, 'transition_post_status'), 10, 3);
add_action("manage_posts_custom_column", array($CARNIEGIGSCAL, 'manage_gig_custom_columns') );

// Filters
add_filter( 'pre_get_posts', array($CARNIEGIGSCAL, 'pre_get_posts') );
add_filter( 'the_content', array($CARNIEGIGSCAL, 'the_content') );
add_filter("manage_edit-gig_columns", array($CARNIEGIGSCAL, 'manage_gig_columns') );
add_filter( 's2_post_types', array($CARNIEGIGSCAL, 's2_post_types') );

?>
