<?php

function carnieGigsIcal($gigs) {
?>
BEGIN:VCALENDAR
VERSION:1.0
PRODID:Carnival Band Web Calendar
TZ:-07
<?php
// see if any rows were returned
foreach ($gigs as $gig) {
	// title
	$title = stripslashes($gig['title']);
	
	// description
	$description = str_replace("\r\n", "=0D=0A=", stripslashes($gig['description']));
	$description = str_replace("\r", "=0D=", $description);
	$description = str_replace("\n", "=0A=", $description);

	// location
	$location = str_replace("\r\n", "=0D=0A=", stripslashes($gig['location']));
	$location = str_replace("\r", "=0D=", $location);
	$location = str_replace("\n", "=0A=", $location);

	// dtstart
	$dtstart = date("Ymd\THi00", strtotime($gig['date'] . " " . $gig['eventstart']) );
	if (strlen($gig['calltime']) > 0) {
		$dtstart = date("Ymd\THi00", strtotime($gig['date'] . " " . $gig['calltime']) );
	}

	// dtend
	$dtend = date("Ymd\THi00", strtotime($gig['date'] . " " . $gig['calltime']) + (60 * 60));
	if (strlen($gig['performancestart']) > 0) {
		$dtend = date("Ymd\THi00", strtotime($gig['date'] . " " . $gig['performancestart']) + (60 * 60));
	}

	// url
	$url = $gig['url'];

	// uid
	$uid = date("Ymd", strtotime($gig['date'])) . "-" . $gig['id'] . "@thecarnivalband.com";

	// status
	$status = "CONFIRMED";
	if ($gig['tentative']) {
		$status = "TENTATIVE";
	}
	if ($gig['cancelled']) {
		$status = "CANCELLED";
	}

	// dtstamp
	$dtstamp = date("Ymd\THi00");

	// output

	echo "BEGIN:VEVENT\n";
	echo "SUMMARY:" . $title . "\n";
	if (strlen($description) > 0) {
		echo "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . $description . "\n";
	}
	if (strlen($location) > 0) {
		echo "LOCATION;ENCODING=QUOTED-PRINTABLE:" . $location . "\n"; 
	}
	echo "DTSTART:" . $dtstart . "\n";
	echo "DTEND:" .  $dtend . "\n";
	echo "UID:" .  $uid . "\n";
	echo "DTSTAMP:" .  $dtstamp . "\n";
	echo "STATUS:" .  $status . "\n";
	if (strlen($url) > 0) {
		echo "URL;VALUE=URI:" . $url . "\n";
	}
	echo "CATEGORIES:GIG\n";
	echo "END:VEVENT\n";
}

?>
END:VCALENDAR
<?php
}
?>
