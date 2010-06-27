<?php

/*
 * Renders carnie gig post stiff
 */
class carnieGigView {

	/*
	 * content filter for gigs.
	 */
	function the_content($content) { 
		return $content . ' This is a beautiful gig';
	}
}
?>
