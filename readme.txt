=== Carnie Gigs ===
Contributors: Darryl F, Richard K
Donate link: http://www.thecarnivalband.com/
Tags: gigs, calendar
Requires at least: 5.3
Tested up to: 6.1
Stable tag: 1.3.1

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

= 1.0 =
3 Tab attendance form
Users sorted by first name in attendance form

= 1.1 =
Limit fields sent in verified attendance form to avoid php max_input_vars limit

= 1.1.1 =
Null user id bug fix

= 1.1.2 =
Fix for custom menus bug

= 1.1.3 =
Set up for auto updating from GitHub

= 1.2 =
New react component for attendance

= 1.2.1 =
Speed up react component load

= 1.2.2 =
Load initial data in parallel

= 1.2.3 =
Added event categories.

= 1.2.4 =
Use special date and time input fields.

= 1.2.5 =
React attendance component UI tweaks

= 1.2.6 =
Configurable recents in attendance component

= 1.2.7 =
Removed legacy attendance component

= 1.2.8 = 
Fix bug with costume call and coordinator (issue #17)

= 1.2.9 = 
Minor bug fix release

= 1.2.9 = 
Minor bug fixes

= 1.2.10 =
Fix shortcode rendering.

= 1.3.0 =
Add greenroom info

= 1.3.1 =
Don't deisplay 00:00:00 times in shortcode gig table display.
