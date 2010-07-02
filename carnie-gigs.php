<?php
/*
Plugin Name: Carnie Gigs
Plugin URI: http://members.thecarnivalband.com
Description: A gig calendar plugin for The Carnival Band 
Version: 0.3
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
require_once $include_folder . '/views/meta_box_admin.php';
require_once $include_folder . '/views/gig.php';
require_once $include_folder . '/controllers/meta_box_admin.php';
require_once $include_folder . '/controllers/attendance.php';
require_once $include_folder . '/model/fields.php';

/*
 * Main class for carnie gigs calenter.  Handles activation, hooks, etc.
 */
class carnieGigsCalendar {

	private $carnie_gigs_meta_form_view,
		$carnie_gigs_meta_form_controller,
		$carnie_gig_attendance_controller,
		$carnie_gig_view,
		$metadata_prefix,
		$metadata_fields;

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
				$this->migrate_legacy_data();
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

		$database_host = "carnivalband.db";
		$database_name = "wordpress_cbm";
		$database_user = "wordpress";
		$database_password = 'wysi11y';
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
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'menu_position' => 5,
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
			$this->carnie_gigs_meta_form_controller->save_data($post_id, $this->metadata_fields, $this->metadata_prefix);
		}
	}

	/*
	 * Filter for the content.  If displaying gig, and not in an
	 * admin page, add our custom metadata
	 */
	function the_content($content) {
		$post = get_post(get_the_id());

		if (! is_admin() && get_post_type($post) == 'gig' ) {
			if ($_POST['gigattendance'] && $_POST['gigid'] == $post->ID) {
				// process gig attendance
				if (! $this->carnie_gig_attendance_controller) {
					$this->carnie_gig_attendance_controller = new carnieGigAttendanceController;
				}
				$this->carnie_gig_attendance_controller->handle_post($post->ID, $this->metadata_prefix);
			}
			if (! $this->carnie_gig_view) {
				$this->carnie_gig_view = new carnieGigView;
			}
			// render metadata for gig
			$content = $this->carnie_gig_view->the_content($content, $this->metadata_prefix);
		}

		return $content;
	}

	/*
	 * Create admin menu(s) for this plugin.  
	 * The admin menu gets us to managing options.
	 */
	function admin_menu() {
		add_options_page('Gigs Options', 'Gigs', 'manage_options', 'carnie-gigs-options', array($this, 'options_page'));
	}

	/*
	 * Call to render options page: TODO
	 */
	function options_page() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		} 
		echo '<div class="wrap">';
		echo '<p>Here is where the form goes for external database.</p>';
		echo '</div>';

	}
}

$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// actions
add_action('init',  array($CARNIEGIGSCAL, 'create_post_type'));
add_action('save_post', array($CARNIEGIGSCAL, 'save_post_data'));
add_action('admin_menu', array($CARNIEGIGSCAL, 'admin_menu'));


// Filters
add_filter( 'pre_get_posts', array($CARNIEGIGSCAL, 'pre_get_posts') );
add_filter( 'the_content', array($CARNIEGIGSCAL, 'the_content') );

?>
