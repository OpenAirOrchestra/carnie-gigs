<?php

// If it cannot locate the wp-config file, you may have to hard code the full path here.
if ( file_exists('../../../wp-load.php') ) {
	require_once('../../../wp-load.php');
} else {
	require_once('../../../wp-config.php');
}

require_once('utility.php');

if ( !defined('WP_CONTENT_URL') ) define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );


$folder = carnieUtil::get_url();

if ( get_magic_quotes_gpc() ) {
	$_POST      = array_map( 'stripslashes_deep', $_POST );
	$_GET       = array_map( 'stripslashes_deep', $_GET );
	$_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
	$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
}
		
?>
