<?php

class carnieFields {

	public  $metadata_prefix,
		$metadata_fields;

	/*
	 * Constructor
	 */
	function __construct() {
	
		$this->metadata_prefix = "cbg_";
		$this->metadata_fields = array(
			array('name' => 'Date',
				'desc' => 'When the gig is to be.',
				'id' => $this->metadata_prefix . 'date',
				'type' => 'date',
			),
			array('name' => 'Location',
				'desc' => 'Where the gig is to be.',
				'id' => $this->metadata_prefix . 'location',
				'type' => 'textarea',
			),
			array('name' => 'URL',
				'desc' => 'Link to a website or web page associated with the gig.',
				'id' => $this->metadata_prefix . 'url',
				'type' => 'url',
			),
			array('name' => 'Call Time',
				'desc' => 'When Carnies should show up for the gig.',
				'id' => $this->metadata_prefix . 'calltime',
				'type' => 'time',
			),
			array('name' => 'Event Start',
				'desc' => 'When the event starts or what the door time is for the public.',
				'id' => $this->metadata_prefix . 'eventstart',
				'type' => 'time',
			),
			array('name' => 'Performance Start',
				'desc' => 'When the band starts making noise.',
				'id' => $this->metadata_prefix . 'performancestart',
				'type' => 'time',
			),
			array('name' => 'Contact',
				'desc' => 'The contact person or organization for this gig.',
				'id' => $this->metadata_prefix . 'contact',
				'type' => 'textarea',
			),
			array('name' => 'Gig Coordinator',
				'desc' => 'Which carnie is responsible for organizing and wrangling this gig.',
				'id' => $this->metadata_prefix . 'coordinator',
				'type' => 'text',
			),
			array('name' => 'Costume',
				'desc' => 'What we want band members to wear for the gig.',
				'id' => $this->metadata_prefix . 'costume',
				'type' => 'text',
			),

			array('name' => 'Advertise',
				'desc' => 'Check this if the gig is to appear on the public website for the band.',
				'id' => $this->metadata_prefix . 'advertise',
				'type' => 'checkbox',
			),
			array('name' => 'Cancelled',
				'desc' => 'Check this if the gig is cancelled.',
				'id' => $this->metadata_prefix . 'cancelled',
				'type' => 'checkbox',
			),
			array('name' => 'Tentative',
				'desc' => 'Check this to tentatively schedule a gig.',
				'id' => $this->metadata_prefix . 'tentative',
				'type' => 'checkbox',
			),
			array('name' => 'Private Event',
				'desc' => 'Is this a private event, like a wedding.',
				'id' => $this->metadata_prefix . 'privateevent',
				'type' => 'checkbox',
			),
			array('name' => 'Closed Call',
				'desc' => 'Is this gig only for specific band members to play at.',
				'id' => $this->metadata_prefix . 'closedcall',
				'type' => 'checkbox',
			),
			array('name' => 'Attendees',
				'desc' => 'Who has committed to attending the gig, or who attended the gig. Please make this a comma separated list.',
				'id' => $this->metadata_prefix . 'attendees',
				'type' => 'list',
			),
			array('name' => 'Fee',
				'desc' => 'How much the band is to be paid.',
				'id' => $this->metadata_prefix . 'fee',
				'type' => 'text',
				'std' => '0'
			)
		);
	}
}

?>
