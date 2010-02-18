<?php
/*
Plugin Name: Carnie Gigs
Plugin URI: http://members.thecarnivalband.com
Description: A gig calendar plugin for The Carnival Band
Version: 0.1
Author: Open Air Orchestra Webmonkey
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
	 * Handles carniegigs shortcode
	 * examples:
	 * [carniegigs] 
         * [carniegigs time="past"] 
         * [carniegigs time="future"] 
	 */
	function carniegigs_shortcode_handler($atts, $content="null", $code="") {
		echo "<p>CARNIEGIGS</p>";
	}



}


$CARNIEGIGSCAL = new carnieGigsCalendar;

// activation hook
register_activation_hook(__FILE__, array($CARNIEGIGSCAL, 'activate') );

// shortcodes
add_shortcode('carniegigs', array($CARNIEGIGSCAL, 'carniegigs_shortcode_handler'));

?>
