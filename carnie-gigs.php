<?php
/*
Plugin Name: Carnie Gigs
Plugin URI: http://members.thecarnivalband.com
Description: A gig calendar plugin for The Carnival Band (in development, not functional yet)
Version: 0.1
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
require_once $include_folder . '/views/gig.php';
require_once $include_folder . '/views/export_csv_form.php';
require_once $include_folder . '/controllers/edit-carnie-gigs.php';
require_once $include_folder . '/controllers/new-carnie-gig.php';
require_once $include_folder . '/controllers/gig-post.php';
require_once $include_folder . '/model/gig.php';
require_once $include_folder . '/utility.php';
require_once $include_folder . '/forms.php';

/*
 * Main class for carnie gigs calenter.  Handles activation, hooks, etc.
 */
class carnieGigsCalendar {

	private $gigsView, 
		$exportCsvFormView, 
		$editGigsController,
		$newGigController;
	
	/*
	 * Constructor
	 */
	function __construct() {
		$this->gigsView = new carnieGigViews;
		$this->exportCsvFormView = new carnieCsvExportView;
		$this->editGigsController = new carnieGigEditController;
		$this->newGigController = new carnieGigNewController;
	}
	   
	/*
	 * Activate the plugin.  
	 * Creates initial database table.
	 * Migrate any data from legacy table
	 */
	function activate () {
		   global $wpdb;
		   $table_name = $wpdb->prefix . "carniegigs";
		   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			   $sql = "CREATE TABLE " . $table_name . " (
				   `id` int(11) NOT NULL auto_increment,
				   `date` date NOT NULL,
				   `title` text,
				   `description` text,
				   `location` text,
				   `url` varchar(2048) default NULL,
				   `privateevent` tinyint(1) default NULL,
				   `calltime` time default NULL,
				   `eventstart` time default NULL,
				   `performancestart` time default NULL,
				   `contact` text,
				   `coordinator` varchar(1024) default NULL,
				   `costume` varchar(1024) default NULL,
				   `advertise` tinyint(1) default NULL,
				   `cancelled` tinyint(1) default NULL,
				   `closedcall` tinyint(1) NOT NULL,
				   `attendees` text NOT NULL,
				   `fee` text character set latin1 collate latin1_bin,
				   `tentative` tinyint(1) NOT NULL default '0' COMMENT 'Is the gig tentative',
				    UNIQUE KEY `id` (`id`),
				    KEY `date` (`date`)
				) DEFAULT CHARSET=latin1 AUTO_INCREMENT=230;";

			   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			   dbDelta($sql);


			   $this->migrate_legacy_data();

			   $sql = "ALTER TABLE  " . $table_name . " ADD  `postid` INT NOT NULL DEFAULT  '0'";

			   $wpdb->query($sql);

			   $this->update_all_posts();

			   add_option("carniegigs_db_version", CARNIE_GIGS_DB_VERSION);
		   }
	}

	/*
	 * Migrates legacy data from gigdb.gigs
	 */
	function migrate_legacy_data () {
		   global $wpdb;
		   $table_name = $wpdb->prefix . "carniegigs";

		   $insert = "INSERT INTO " . $table_name .
			   " SELECT * FROM gigdb.gigs";

		   $results = $wpdb->query( $insert );
	}

	/*
	 * Updates all posts associated with gigs in the database
	 */
	function update_all_posts () {
		$gigPostController = new carnieGigPostController;
		
		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";

		$select = "SELECT 'id' FROM " . $table_name .
			" ORDER BY `date` DESC";

		$results = $wpdb->get_results( $select, ARRAY_A );

		foreach ($results as $gig) {
			$gigPostController->update($gig['id']);
		}
	}

	/*
	 * Handles carniegigs shortcode
	 * examples:
	 * [carniegigs] 
         * [carniegigs time="past"] 
         * [carniegigs time="future"] 
	 */
	function carniegigs_shortcode_handler($atts, $content="null", $code="") {
		   extract( shortcode_atts( array(
			         'time' => 'all',
			         'display' => 'short'
				  ), $atts ) );
		   global $wpdb;
		   $table_name = $wpdb->prefix . "carniegigs";

		   $select = "SELECT * FROM " . $table_name .
			   " ORDER BY `date` DESC";

		   if ($time == 'past') {
			   $select = "SELECT * FROM " . $table_name .
				   ' WHERE `date` < CURDATE() ORDER BY `date` DESC';
		   } else if ($time == 'future') {
			   $select = "SELECT * FROM " . $table_name .
				   ' WHERE `date` >= CURDATE() ORDER BY `date`';
		   }

		   $results = $wpdb->get_results( $select, ARRAY_A );
		   $this->gigsView->shortGigs($results);
	}


	/*
	 * Enqueue style-file, if it exists.
	 */
	function add_stylesheet() {
		$myStyleUrl = carnieUtil::get_url() . 'css/style.css';
		wp_register_style('carnieStyleSheets', $myStyleUrl);
		wp_enqueue_style( 'carnieStyleSheets');
	}

	/*
	 * admin menu
	 */
	function admin_menu() {
		// admin 
		$editpage1 = add_object_page('Carnie Gigs', 'Carnie Gigs', 'publish_pages', 'edit-carnie-gigs', array($this->editGigsController, 'edit_gigs_page'));

		$editPage2 = add_submenu_page('edit-carnie-gigs', 'Edit Carnie Gigs', 'Edit', 'publish_pages', 'edit-carnie-gigs', array($this->editGigsController, 'edit_gigs_page'));
		add_submenu_page('edit-carnie-gigs', 'New Carnie Gig', 'Add New', 'publish_pages', 'new-carnie-gig', array($this->newGigController, 'new_gig_page'));
		add_submenu_page('edit-carnie-gigs', 'Export Carnie Gigs', 'Export', 'publish_pages', 'export-carnie-gigs', array($this, 'export_gigs_page'));

		add_action("admin_print_scripts-$editpage1", array($this->editGigsController, 'edit_gigs_head'));

	}

	/*
	 * export gigs page
	 */
	function export_gigs_page() {
		print '<div class="wrap">';
		echo "<h2>Export Carnie Gigs</h2>";

		global $wpdb;
		$table_name = $wpdb->prefix . "carniegigs";
		
		$select = "SELECT * FROM " . $table_name .
			" LIMIT 1";
		$results = $wpdb->get_results( $select, ARRAY_A );

		$this->exportCsvFormView->exportForm($results[0]);
		print "</div>";
	}
}


$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// shortcodes
add_shortcode('carniegigs', array($CARNIEGIGSCAL, 'carniegigs_shortcode_handler'));

// actions
add_action('wp_print_styles', array($CARNIEGIGSCAL, 'add_stylesheet'));
add_action('admin_menu', array($CARNIEGIGSCAL, 'admin_menu'));


?>
