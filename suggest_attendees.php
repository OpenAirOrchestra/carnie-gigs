<?php

/*
 * This php is called directly with GET to get attendees for autocomplete.
 */
require_once 'ajaxSetup.php';
require_once 'version.php';

if (! is_user_logged_in()) return;

$q = strtolower($_REQUEST["q"]);
if (!$q) return;

function carnieUserCmp($a, $b)  {
	return strcmp($a->user_login, $b->user_login);
}

$users = get_users();
usort($users, "carnieUserCmp");

foreach ($users as $user) {
	$name = $user->display_name;
	if ($name && strpos(strtolower($name), $q) !== false) {
		echo "$name\n";
	}
}

?>
