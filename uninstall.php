<?php
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
} else {
	
	global $wpdb;
	$table_name = $wpdb->prefix . "carniegigs";

	// delete entry from wp_options table
	delete_option('carniegigs_db_version');
	//
	// drop the carniegigs table
	$sql = "DROP TABLE IF EXISTS `" . $table_name . "`";
	$wpdb->query($sql);
}
?>
