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
}
?>
