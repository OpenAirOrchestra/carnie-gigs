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

/*
 * Main class for carnie gigs calenter.  Handles activation, hooks, etc.
 */
class carnieGigsCalendar {

	/*
	 * Constructor
	 */
	function __construct() {
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
	 * Create custom post type
	 */
	function create_post_type() {
		register_post_type( 'gig',
			array(
				'labels' => array(
					'name' => __( 'Gigs' ),
					'singular_name' => __( 'Gig' )
				),
				'description' => 'A Carnival Band Gig',
				'public' => true,
				'register_meta_box_cb' => 'carnie_gigs_register_meta_box_cb',
			)
		);
	}

	/*
	 * Create taxonomy
	 */
	function create_taxonomy() {
		register_taxonomy_for_object_type('post_tag', 'gig');
	}

	/*
	 * Callback function that to be called when setting up the meta 
	 * boxes for the edit form. 
	 */
	function register_meta() {
		// TODO: remove_meta_box() and add_meta_box() calls.
	}

	/*
	 * Filter for home page to add gigs
	 */
	function pre_get_posts( $query ) {
		
		if ( is_home() || is_feed() ) {
			$query->set( 'post_type', array( 'post', 'gig' ));
		}
	}
}

$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// actions
add_action('init',  array($CARNIEGIGSCAL, 'create_post_type'));
add_action('init',  array($CARNIEGIGSCAL, 'create_taxonomy'));

// Filters
add_filter( 'pre_get_posts', array($CARNIEGIGSCAL, 'pre_get_posts') );


// Callback functions
function carnie_gigs_register_meta_box_cb() {
}

?>
