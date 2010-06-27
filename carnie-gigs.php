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

/*
 * Main class for carnie gigs calenter.  Handles activation, hooks, etc.
 */
class carnieGigsCalendar {

	private $carnie_gigs_meta_form_view,
		$carnie_gigs_meta_form_controller,
		$carnie_gig_view,
		$metadata_prefix,
		$metadata_fields;

	/*
	 * Constructor
	 */
	function __construct() {
	
		$this->metadata_prefix = "cbg_";
		$this->metadata_fields = array(
			array('name' => 'Date',
				'desc' => 'When the gig is to be.',
				'id' => $this->metadata_prefix . 'date',
				'type' => 'date',
			),
			array('name' => 'Location',
				'desc' => 'Where the gig is to be.',
				'id' => $this->metadata_prefix . 'location',
				'type' => 'textarea',
			),
			array('name' => 'URL',
				'desc' => 'Link to a website or web page associated with the gig.',
				'id' => $this->metadata_prefix . 'url',
				'type' => 'url',
			),
			array('name' => 'Call Time',
				'desc' => 'When Carnies should show up for the gig.',
				'id' => $this->metadata_prefix . 'calltime',
				'type' => 'time',
			),
			array('name' => 'Event Start',
				'desc' => 'When the event starts or what the door time is for the public.',
				'id' => $this->metadata_prefix . 'eventstart',
				'type' => 'time',
			),
			array('name' => 'Performance Start',
				'desc' => 'When the band starts making noise.',
				'id' => $this->metadata_prefix . 'performancestart',
				'type' => 'time',
			),
			array('name' => 'Contact',
				'desc' => 'The contact person or organization for this gig.',
				'id' => $this->metadata_prefix . 'contact',
				'type' => 'textarea',
			),
			array('name' => 'Gig Coordinator',
				'desc' => 'Which carnie is responsible for organizing and wrangling this gig.',
				'id' => $this->metadata_prefix . 'coordinator',
				'type' => 'text',
			),
			array('name' => 'Costume',
				'desc' => 'What we want band members to wear for the gig.',
				'id' => $this->metadata_prefix . 'costume',
				'type' => 'text',
			),

			array('name' => 'Advertise',
				'desc' => 'Check this if the gig is to appear on the public website for the band.',
				'id' => $this->metadata_prefix . 'advertise',
				'type' => 'checkbox',
			),
			array('name' => 'Cancelled',
				'desc' => 'Check this if the gig is cancelled.',
				'id' => $this->metadata_prefix . 'cancelled',
				'type' => 'checkbox',
			),
			array('name' => 'Tentative',
				'desc' => 'Check this to tentatively schedule a gig.',
				'id' => $this->metadata_prefix . 'tentative',
				'type' => 'checkbox',
			),
			array('name' => 'Private Event',
				'desc' => 'Is this a private event, like a wedding.',
				'id' => $this->metadata_prefix . 'privateevent',
				'type' => 'checkbox',
			),
			array('name' => 'Closed Call',
				'desc' => 'Is this gig only for specific band members to play at.',
				'id' => $this->metadata_prefix . 'closedcall',
				'type' => 'checkbox',
			),
			array('name' => 'Attendees',
				'desc' => 'Who has committed to attending the gig, or who attended the gig. Please make this a comma separated list.',
				'id' => $this->metadata_prefix . 'attendees',
				'type' => 'list',
			),
			array('name' => 'Fee',
				'desc' => 'How much the band is to be paid.',
				'id' => $prefix . 'fee',
				'type' => 'text',
				'std' => '0'
			)
		);
	}
	   
	/*
	 * Activate the plugin.  
	 * Migrate any data from legacy table
	 */
	function activate () {
			   add_option("carniegigs_db_version", CARNIE_GIGS_DB_VERSION);
	}

	/*
	 * Migrates legacy data from gigdb.gigs
	 */
	function migrate_legacy_data () {
	}

	/*
	 * Create custom post type and taxonomy
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
				'menu_position' => 20,
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
			$this->carnie_gigs_meta_form_controller->save_data($post_id, $this->metadata_fields);
		}
	}

	/*
	 * Filter for the content.  If displaying gig, and not in an
	 * admin page, add our custom metadata
	 */
	function the_content($content) {
		$post = get_post(get_the_id());

		if (! is_admin() && get_post_type($post) == 'gig' ) {
			if (! $this->carnie_gig_view) {
				$this->carnie_gig_view = new carnieGigView;
			}
			$content = $this->carnie_gig_view->the_content($content, $this->metadata_prefix);
		}

		return $content;
	}
}

$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// actions
add_action('init',  array($CARNIEGIGSCAL, 'create_post_type'));
add_action('save_post', array($CARNIEGIGSCAL, 'save_post_data'));

// Filters
add_filter( 'pre_get_posts', array($CARNIEGIGSCAL, 'pre_get_posts') );
add_filter( 'the_content', array($CARNIEGIGSCAL, 'the_content') );

?>
