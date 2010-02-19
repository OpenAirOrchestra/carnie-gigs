<?php
/*
 * This class is a bunch of utility functiona.
 */
class carnieUtil {

	/*
	 * Utility: get the URL of this plugin
	 */
	function get_url() {
		return ( get_bloginfo('wpurl')) . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/';
	}

	
	/*
	 * Enqueue style-file, if it exists.
	 */
	function add_stylesheet() {
		$myStyleUrl = carnieUtil::get_url() . 'css/style.css';
		wp_register_style('carnieStyleSheets', $myStyleUrl);
		wp_enqueue_style( 'carnieStyleSheets');
	}
}
?>
