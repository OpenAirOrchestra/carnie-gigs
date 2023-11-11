<?php

class carnieFields {

	public  $metadata_prefix,
		$metadata_categories,
		$metadata_fields;

	/*
	 * Constructor
	 */
	function __construct() {
	
		$this->metadata_prefix = "cbg_";
		$this->metadata_categories = array(
			array('name' => 'Gig Details',
			'id' => $this->metadata_prefix . 'gig_details'
			),
			array('name' => 'Verified Attendees',
			'id' => $this->metadata_prefix . 'gig_verified_attendees'
			),
			array('name' => 'Gig Accessibility',
			'id' => $this->metadata_prefix . 'gig_accessibility'
			)
		);
		$this->metadata_fields = array(
			array('name' => 'Date',
				'category' => 'gig_details',
				'desc' => 'When the gig is to be, e.g., 2012-02-30',
				'id' => $this->metadata_prefix . 'date',
				'type' => 'date',
			),
			array('name' => 'Location',
				'category' => 'gig_details',
				'desc' => 'Where the gig is to be.',
				'id' => $this->metadata_prefix . 'location',
				'type' => 'textarea',
			),
			array('name' => 'Green Room',
				'category' => 'gig_details',
				'desc' => 'Band member eyes only information about the gig (Greenroom, etc).',
				'id' => $this->metadata_prefix . 'greenroom',
				'type' => 'textarea',
			),
			array('name' => 'URL',
				'category' => 'gig_details',
				'desc' => 'Link to a website or web page associated with the gig.',
				'id' => $this->metadata_prefix . 'url',
				'type' => 'url',
			),
			array('name' => 'Call Time',
				'category' => 'gig_details',
				'desc' => 'When Carnies should show up for the gig, e.g., 07:00 PM.',
				'id' => $this->metadata_prefix . 'calltime',
				'type' => 'time',
			),
			array('name' => 'Event Start',				
				'category' => 'gig_details',
				'desc' => 'When the event starts or what the door time is for the public, e.g., 06:00 PM.',
				'id' => $this->metadata_prefix . 'eventstart',
				'type' => 'time',
			),
			array('name' => 'Performance Start',				
				'category' => 'gig_details',
				'desc' => 'When the band starts making noise, e.g., 07:30 PM.',
				'id' => $this->metadata_prefix . 'performancestart',
				'type' => 'time',
			),
			array('name' => 'Contact',
				'category' => 'gig_details',
				'desc' => 'The contact person or organization for this gig.',
				'id' => $this->metadata_prefix . 'contact',
				'type' => 'textarea',
			),
			array('name' => 'Gig Coordinator',
				'category' => 'gig_details',	
				'desc' => 'Which carnie is responsible for organizing and wrangling this gig.',
				'id' => $this->metadata_prefix . 'coordinator',
				'type' => 'text',
			),
			array('name' => 'Costume',
				'category' => 'gig_details',
				'desc' => 'What we want band members to wear for the gig.',
				'id' => $this->metadata_prefix . 'costume',
				'type' => 'text',
			),
			array('name' => 'Advertise',
				'category' => 'gig_details',
				'desc' => 'Check this if the gig is to appear on the public website for the band.',
				'id' => $this->metadata_prefix . 'advertise',
				'type' => 'checkbox',
			),
			array('name' => 'Cancelled',
				'category' => 'gig_details',
				'desc' => 'Check this if the gig is cancelled.',
				'id' => $this->metadata_prefix . 'cancelled',
				'type' => 'checkbox',
			),
			array('name' => 'Tentative',
				'category' => 'gig_details',
				'desc' => 'Check this to tentatively schedule a gig.',
				'id' => $this->metadata_prefix . 'tentative',
				'type' => 'checkbox',
			),
			array('name' => 'Private Event',
				'category' => 'gig_details',
				'desc' => 'Is this a private event, like a wedding.',
				'id' => $this->metadata_prefix . 'privateevent',
				'type' => 'checkbox',
			),
			array('name' => 'Closed Call',
				'category' => 'gig_details',
				'desc' => 'Is this gig only for specific band members to play at.',
				'id' => $this->metadata_prefix . 'closedcall',
				'type' => 'checkbox',
			),
			array('name' => 'Attendees',
				'category' => 'gig_details',
				'desc' => 'Who has committed to attending the gig. Please make this a comma separated list.',
				'id' => $this->metadata_prefix . 'attendees',
				'type' => 'list',
				'suggest' => 'suggest_attendees',
			),
			array('name' => 'Fee',
				'category' => 'gig_details',
				'desc' => 'How much the band is to be paid.',
				'id' => $this->metadata_prefix . 'fee',
				'type' => 'text',
				'std' => '0'
			),
			array('name' => 'Date Fee Deposited',
				'category' => 'gig_details',
				'desc' => 'When the performance fee was deposited.  This is plain text field, so it may include depositor if neccessary.',
				'id' => $this->metadata_prefix . 'datefeedeposited',
				'type' => 'text',
			),
			array('name' => 'Expenses',
				'category' => 'gig_details',
				'desc' => 'Itemized Gig Expenses.',
				'id' => $this->metadata_prefix . 'expenses',
				'type' => 'textarea',
			),
			array('name' => 'Net',
				'category' => 'gig_details',
				'desc' => 'Performance fee minus total gig expenses (typically).',
				'id' => $this->metadata_prefix . 'net',
				'type' => 'text',
			),
			array('name' => 'Production Comission',
				'category' => 'gig_details',
				'desc' => 'Usually 15 percent of Net.',				
				'id' => $this->metadata_prefix . 'productioncomission',
				'type' => 'text',
			),
			array('name' => 'Designated parking',
				'category' => 'gig_accessibility',
				'desc' => 'Is there designated accessibility parking?',
				'id' => $this->metadata_prefix . 'accessibleparking',
				'type' => 'checkbox',
			),
			array('name' => 'Accessible transit',
				'category' => 'gig_accessibility',
				'desc' => 'Is there accessible transit to the venue?',
				'id' => $this->metadata_prefix . 'accessibletransit',
				'type' => 'checkbox',
			),
			array('name' => 'Food/Water provided',
				'category' => 'gig_accessibility',
				'desc' => 'Is there food/water provided? If yes, describe in green room details box and any food accommodations (meat/dairy/gluten-free) if known',
				'id' => $this->metadata_prefix . 'foodwater',
				'type' => 'checkbox',
			),
			array('name' => 'Accessible washroom',
				'category' => 'gig_accessibility',
				'desc' => 'Is there an accessible washroom?',
				'id' => $this->metadata_prefix . 'washroom',
				'type' => 'checkbox',
			),
			array('name' => 'Gender neutral washroom',
				'category' => 'gig_accessibility',
				'desc' => 'Is there a gender neutral washroom available?',
				'id' => $this->metadata_prefix . 'genderneutralwashroom',
				'type' => 'checkbox',
			),
			array('name' => 'Step-free greenroom acces',
				'category' => 'gig_accessibility',
				'desc' => 'Can the greenroom be accessed without the use of stairs?',
				'id' => $this->metadata_prefix . 'stepfreegreenroom',
				'type' => 'checkbox',
			),
			array('name' => 'Step-free performance access',
				'category' => 'gig_accessibility',
				'desc' => 'Can the performance area be accessed without the use of stairs?',
				'id' => $this->metadata_prefix . 'stepfreeperformance',
				'type' => 'checkbox',
			),
			array('name' => 'Indoors',
				'category' => 'gig_accessibility',
				'desc' => 'Is the gig, at least in part, indoors?',
				'id' => $this->metadata_prefix . 'indoors',
				'type' => 'checkbox',
			),
			array('name' => 'Outdoors',
				'category' => 'gig_accessibility',
				'desc' => 'Is the gig, at least in part, outdoors?',
				'id' => $this->metadata_prefix . 'washroom',
				'type' => 'checkbox',
			),
			array('name' => 'Outdoor Shelter',
				'category' => 'gig_accessibility',
				'desc' => 'If the gig is outdoors, is there outdoor shelter available?',
				'id' => $this->metadata_prefix . 'outdoorshelter',
				'type' => 'checkbox',
			),
			array('name' => 'Seating Available',
				'category' => 'gig_accessibility',
				'desc' => 'Is there onstage seating available for at least some performers?',
				'id' => $this->metadata_prefix . 'seating',
				'type' => 'checkbox',
			),
			array('name' => 'Stationary',
				'category' => 'gig_accessibility',
				'desc' => 'Is this a stationary gig?',
				'id' => $this->metadata_prefix . 'stationary',
				'type' => 'checkbox',
			),
			array('name' => 'Parade',
				'category' => 'gig_accessibility',
				'desc' => 'Does this gig include a parade? If so, describe estimated length or duration in gig greenroom details.',
				'id' => $this->metadata_prefix . 'parade',
				'type' => 'checkbox',
			),
			array('name' => 'Paved flat pathways',
				'category' => 'gig_accessibility',
				'desc' => 'Do outdoor pathways have accessibility friendly surfaces? If not, describe describe in gig greenroom details, ex. gravel, sand, wood chips, slopes, uneven terrain.',
				'id' => $this->metadata_prefix . 'accessiblepath',
				'type' => 'checkbox',
			),
			array('name' => 'Wide pathways',
				'category' => 'gig_accessibility',
				'desc' => 'Are all pathways at least 1m wide, 2m turning space?',
				'id' => $this->metadata_prefix . 'widepathways',
				'type' => 'checkbox',
			)
		);
	}
}

?>
