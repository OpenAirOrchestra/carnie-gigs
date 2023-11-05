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
				'desc' => 'When the gig is to be, e.g., 2012-02-30',
				'id' => $this->metadata_prefix . 'date',
				'type' => 'date',
			),
			array('name' => 'Location',
				'desc' => 'Where the gig is to be.',
				'id' => $this->metadata_prefix . 'location',
				'type' => 'textarea',
			),
			array('name' => 'Green Room',
				'desc' => 'Band member eyes only information about the gig (Greenroom, etc).',
				'id' => $this->metadata_prefix . 'greenroom',
				'type' => 'textarea',
			),
			array('name' => 'URL',
				'desc' => 'Link to a website or web page associated with the gig.',
				'id' => $this->metadata_prefix . 'url',
				'type' => 'url',
			),
			array('name' => 'Call Time',
				'desc' => 'When Carnies should show up for the gig, e.g., 07:00 PM.',
				'id' => $this->metadata_prefix . 'calltime',
				'type' => 'time',
			),
			array('name' => 'Event Start',
				'desc' => 'When the event starts or what the door time is for the public, e.g., 06:00 PM.',
				'id' => $this->metadata_prefix . 'eventstart',
				'type' => 'time',
			),
			array('name' => 'Performance Start',
				'desc' => 'When the band starts making noise, e.g., 07:30 PM.',
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
				'desc' => 'Who has committed to attending the gig. Please make this a comma separated list.',
				'id' => $this->metadata_prefix . 'attendees',
				'type' => 'list',
				'suggest' => 'suggest_attendees',
			),
			array('name' => 'Fee',
				'desc' => 'How much the band is to be paid.',
				'id' => $this->metadata_prefix . 'fee',
				'type' => 'text',
				'std' => '0'
			),
			array('name' => 'Date Fee Deposited',
				'desc' => 'When the performance fee was deposited.  This is plain text field, so it may include depositor if neccessary.',
				'id' => $this->metadata_prefix . 'datefeedeposited',
				'type' => 'text',
			),
			array('name' => 'Expenses',
				'desc' => 'Itemized Gig Expenses.',
				'id' => $this->metadata_prefix . 'expenses',
				'type' => 'textarea',
			),
			array('name' => 'Net',
				'desc' => 'Performance fee minus total gig expenses (typically).',
				'id' => $this->metadata_prefix . 'net',
				'type' => 'text',
			),
			array('name' => 'Production Comission',
				'desc' => 'Usually 15 percent of Net.',
				'id' => $this->metadata_prefix . 'productioncomission',
				'type' => 'text',
			),
			array('name' => 'Stairs',
				'category' => 'Accessability',
				'desc' => 'Are stairs required to access the venue.',
				'id' => $this->metadata_prefix . 'stairs',
				'type' => 'checkbox',
			),
			array('name' => 'Seating',
				'category' => 'Accessability',
				'desc' => 'Is there seathing available for performers.',
				'id' => $this->metadata_prefix . 'seating',
				'type' => 'checkbox',
			),
			array('name' => 'Parade',
				'category' => 'Accessability',
				'desc' => 'Is this a mobile gig (not stationary).',
				'id' => $this->metadata_prefix . 'parade',
				'type' => 'checkbox',
			),
			array('name' => 'Outdoors',
				'category' => 'Accessability',
				'desc' => 'Is the gig, at least in part, outdoors.',
				'id' => $this->metadata_prefix . 'outdoors',
				'type' => 'checkbox',
			),
			array('name' => 'Outdoor Shelter',
				'category' => 'Accessability',
				'desc' => 'Is there outdoor shelter available.',
				'id' => $this->metadata_prefix . 'outdoorshelter',
				'type' => 'checkbox',
			),
			array('name' => 'Indoors',
				'category' => 'Accessability',
				'desc' => 'Is the gig, at least in part, indoors.',
				'id' => $this->metadata_prefix . 'indoors',
				'type' => 'checkbox',
			),
			array('name' => 'Washroom',
				'category' => 'Accessability',
				'desc' => 'Is there a washroom available.',
				'id' => $this->metadata_prefix . 'washroom',
				'type' => 'checkbox',
			),
			array('name' => 'Gender Neutral Washroom',
				'category' => 'Accessability',
				'desc' => 'Is there a gender neutral washroom available.',
				'id' => $this->metadata_prefix . 'genderneutralwashroom',
				'type' => 'checkbox',
			)
		);
	}
}

?>
