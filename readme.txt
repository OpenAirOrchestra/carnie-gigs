=== Carnie Gigs ===
Contributors: Darryl F, RichardK
Donate link: http://www.thecarnivalband.com/
Tags: gigs, calendar
Requires at least: 3.0
Tested up to: 3.4

Gig Calendar for The Carnival Band.

== Description ==

A gig calendar specifically tailored for The Carnival Band.

== Installation ==

1. Upload `carnie-gigs.zip` to the `/wp-content/plugins/` directory and unzip.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode [carniegigs] to a page to list all gigs.
4. Add the shortcode [carniegigs time="past"] to a page to list past gigs.
5. Add the shortcode [carniegigs time="future"] to a page to list future gigs.
6. Download and install the "members" plugin so you can modify capabilites for roles. See http://wordpress.org/extend/plugins/members/
7. Add the following capabilites to the administrator role:

	* delete_others_gigs
	* delete_private_gigs
	* delete_published_gigs
	* edit_gigs
	* edit_others_gigs
	* edit_private_gigs
	* edit_published_gigs
	* publish_gigs
	* read_private_gigs

8. Assign the above capabilites to other roles as desired.

== Frequently Asked Questions ==

= Why not use a pre-existing plugin? =

Its the path of least resistance to adapt some pre-existing carnie calendar php tech.

== Changelog ==

= 0.1 =
* Initial version.

= 0.2 =
* Gig attendees shortcode

= 0.3 =
* Rewrite as custom post type

= 0.4 = 
* Change custom post capability type from "page" to "gig"

= 0.5 = 
* Added primitive autocomplete to attendees input and attendance CSV export.

== Upgrade Notice ==

= 0.1 =
This version is the first to provide any funcationality.

= 0.3 =
This version changes to using custom posts. There is still an exported database.
Manual database import from old db may be required.

= 0.4 = 
This version changes the custom post capability type from "page" to "gig". 
You will need to add capabilities for roles for the plugin to work.

= 0.6 =
Added the "Verified Attendees" field

= 0.7 =
Put "Verified Attendees" into its own table.
Changed from comma separate list to checkbox style for verified attendance.

= 0.8 =
Register subscribe2 meta box for notification override for gigs.

= 0.9 =
Remove obsolete attendance export

