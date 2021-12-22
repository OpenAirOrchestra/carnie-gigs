import React, { useEffect, useState } from 'react';
import Configuration from './Configuration';
import MockAttendanceService from './MockAttendanceService';
import MockEventService from './MockEventService';
import MockUserService from './MockUserService';
import AttendanceService from './AttendanceService';
import EventService from './EventService';
import UserService from './UserService';

import Header from './Header'
import SearchBar from './SearchBar'
import AttendanceList from './AttendanceList'
import { attendeeKey } from './AttendanceRecord';
import NewAttendeeForm from './NewAttendeeForm'
import Loading from './Loading'

export default AttendanceSheet

/// Minimal error handling function
function handleError(error) {
	if (window.confirm(error + "\nReload?")) {
		window.location.reload();
	}
}

/// Compute attendees from users and attendance records
function possibleAttendees(eventId, users, recents, currentAttendees, pending) {
	let attendanceMap = {}

	// Add users to the map.
	for (const user of users) {
		let attendanceRecord = { user_id: user.id, firstname: '', lastname: '' };
		if (user.first_name) {
			attendanceRecord.firstname = user.first_name;
			if (user.last_name) {
				attendanceRecord.lastname = user.last_name;
			}
		} else if (user.display_name) {
			attendanceRecord.firstname = user.display_name;
		} 

		if (user.email) {
			attendanceRecord.email = user.email;
		}

		if (user.description) {
			attendanceRecord.notes = user.description;
		}

		const key = attendeeKey(attendanceRecord);
		attendanceMap[key] = attendanceRecord;
	}

	// Add recents to the map
	for (const recent of recents) {
		if (recent.event_id === eventId) {
			continue;
		}
		const key = attendeeKey(recent);
		let attendanceRecord = attendanceMap[key];
		if (!attendanceRecord) {
			attendanceRecord = { ...recent };
		} else {
			attendanceRecord = { ...attendanceRecord };
		}

		attendanceRecord.id = recent.id;

		if (!attendanceRecord.notes) {
			attendanceRecord.notes = recent.notes;
		}
		attendanceRecord.event_id = recent.event_id;
		attendanceMap[key] = attendanceRecord;
	}

	// Add currentAttendees to the map
	for (const currentAttendee of currentAttendees) {
		const key = attendeeKey(currentAttendee);
		let attendanceRecord = attendanceMap[key];
		if (!attendanceRecord) {
			attendanceRecord = { ...currentAttendee };
		} else {
			attendanceRecord = { ...attendanceRecord };
		}

		attendanceRecord.id = currentAttendee.id;
		attendanceRecord.event_id = currentAttendee.event_id;
		if (currentAttendee.phone && !attendanceRecord.phone) {
			attendanceRecord.phone = currentAttendee.phone;
		}
		if (currentAttendee.user_id && !attendanceRecord.user_id) {
			attendanceRecord.user_id = currentAttendee.user_id;
		}
		if (currentAttendee.user_id && !attendanceRecord.user_id) {
			attendanceRecord.user_id = currentAttendee.user_id;
		}
		if (currentAttendee.firstname && !attendanceRecord.firstname) {
			attendanceRecord.firstname = currentAttendee.firstname;
		}
		if (currentAttendee.lastname && !attendanceRecord.lastname) {
			attendanceRecord.lastname = currentAttendee.lastname;
		}
		if (currentAttendee.notes && !attendanceRecord.notes) {
			attendanceRecord.notes = currentAttendee.notes;
		}

		attendanceMap[key] = attendanceRecord;
	}

	// Add pending to the map
	for (const pendingAttendee of pending) {
		const key = attendeeKey(pendingAttendee);
		let attendanceRecord = attendanceMap[key];
		if (!attendanceRecord) {
			attendanceRecord = { ...pendingAttendee };
		} else {
			attendanceRecord = { ...attendanceRecord };
		}

		attendanceMap[key] = attendanceRecord;
	}

	// Iterate the map to create result.
	const attendees = Object.values(attendanceMap);
	return attendees ? attendees : [];
}

/// Return the subset of attendees with text that matches searchTerm
function searchAttendees(attendees, searchTerm) {
	const pattern = searchTerm ? searchTerm.toLowerCase().replace(/\s+/g, '') : null;
	let result = attendees;

	if (pattern) {
		result = result.filter(attendee => {
			const line = Object.values(attendee).map(value => {
				return String(value).toLowerCase()
			}).join().replace(/\s+/g, '');
			return line.includes(pattern);
		})
	}

	return result;
}

/// Return the subset of attendees that match the filter criteria
function filterAttendees(eventId, attendees, recentUserKeys, filterRecent, filterOld, filterNew, filterPresent) {
	let result = attendees;

	if (filterRecent || filterNew || filterOld || filterPresent) {
		result = result.filter(attendee => {

			if (filterNew && (!attendee.user_id || attendee.user_id === '0')) {
				return true;
			}

			if (filterPresent && attendee.event_id && attendee.event_id === eventId) {
				return true;
			}

			if (filterRecent || filterOld) {
				const key = attendeeKey(attendee);
				const isRecent = recentUserKeys.has(key);
				if (filterRecent && isRecent) {
					return true;
				}

				if (filterOld && !isRecent) {
					return true;
				}
			}

			return false;
		});
	}

	return result;
}

/// Fetch users from remote
async function fetchUsers() {
	const userService = Configuration.userService;

	let page = 1;
	const per_page = 500;
	let allUsers = [];
	let moreUsers = true;

	do {
		const users = await userService.retrieve(page, per_page);

		allUsers = [...allUsers, ...users];
		moreUsers = users.length >= per_page;
		
		++page;
	} while (moreUsers);

	return Promise.resolve(allUsers);
}

/// Fetch recents
async function fetchCurrentAttendees(eventId) {
	const attendanceService = Configuration.attendanceService;


	let page = 1;
	const per_page = 100;
	let allAttendees = [];
	let moreAttendees = true;
	do {
		const currentAttendees = await attendanceService.retrieve(page, per_page, eventId);
		allAttendees = [...allAttendees, ...currentAttendees];
		moreAttendees = currentAttendees.length >= per_page;
		++page;
	} while (moreAttendees);

	return Promise.resolve(allAttendees);
}


/// Load all data from backend.
async function loadAll(eventId, setIsLoading, setEventRecord, setUsers, setRecents, setRecentUserKeys, setCurrentAttendees) {
	try {

		const eventService = Configuration.eventService;
		const attendanceService = Configuration.attendanceService;

		const eventRecordPromise = eventService.get(eventId);
		const usersPromise = fetchUsers();
		const recentsPromise = attendanceService.retrieve(1, 75);
		const attendeesPromise = fetchCurrentAttendees(eventId);

		const eventRecord = await eventRecordPromise;
		setEventRecord(eventRecord);
		
		const users = await usersPromise;
		setUsers(users);

		const recents = await recentsPromise;
		setRecents(recents);

		let recentUserKeys = new Set();
		for (const recent of recents) {
			const key = attendeeKey(recent);
			recentUserKeys.add(key);
		}
		setRecentUserKeys(recentUserKeys);

		const attendees = await attendeesPromise;
		setCurrentAttendees(attendees);

		setIsLoading(false);
	} catch (error) {
		handleError(error);
	}
}

/// Add an attendance record (add attendee)
async function addAttendanceRecord(eventId, attendee, modificationPromise, pending, setPending, setCurrentAttendees) {
	try {
		const attendanceService = Configuration.attendanceService;

		// Add record to pending
		let newPending = [...pending];
		newPending.push(attendee);
		setPending(newPending);
		attendanceService.pendingRecords = newPending;

		// Create the record to add
		let newAttendee = { ...attendee };
		newAttendee.event_id = eventId;
		newAttendee.id = undefined;

		// Ask for the server to create the attendee record
		await attendanceService.create(newAttendee);

		// Wait for previous modification to complete
		await modificationPromise

		// List current attendees again and set them
		let page = 1;
		let per_page = 100;
		let allAttendees = [];
		let moreAttendees = true;
		do {
			const currentAttendees = await attendanceService.retrieve(page, per_page, eventId);
			allAttendees = [...allAttendees, ...currentAttendees];
			setCurrentAttendees(allAttendees);
			moreAttendees = currentAttendees.length >= per_page;
			++page;
		} while (moreAttendees);

		// Remove record from pending.
		// This could get messed up if we didn't serilize the modification requests.
		newPending = [...attendanceService.pendingRecords].filter(pendingAttendee => {
			return pendingAttendee !== attendee;
		});
		setPending(newPending);
		attendanceService.pendingRecords = newPending;
		Promise.resolve(attendee);
	} catch (error) {
		handleError(error);
	}
}

/// Add an attendance record (add attendee)
async function deleteAttendanceRecord(eventId, attendee, modificationPromise, pending, setPending, setRecents, setCurrentAttendees) {
	try {
		const attendanceService = Configuration.attendanceService;

		// Add record to pending
		let newPending = [...pending];
		newPending.push(attendee);
		setPending(newPending);
		attendanceService.pendingRecords = newPending;

		// Delete the attendance record
		await attendanceService.delete(attendee);

		// Wait for previous modification to complete
		await modificationPromise

		// List recent attendees again and set them
		const recents = await attendanceService.retrieve(1, 50);
		setRecents(recents);

		// List current attendees again and set them
		let page = 1;
		let per_page = 100;
		let allAttendees = [];
		let moreAttendees = true;
		do {
			const currentAttendees = await attendanceService.retrieve(page, per_page, eventId);
			allAttendees = [...allAttendees, ...currentAttendees];
			setCurrentAttendees(allAttendees);
			moreAttendees = currentAttendees.length >= per_page;
			++page;
		} while (moreAttendees);

		// Remove record from pending.
		// This could get messed up if we didn't serilize the modification requests.
		newPending = [...attendanceService.pendingRecords].filter(pendingAttendee => {
			return pendingAttendee !== attendee;
		});
		setPending(newPending);
		attendanceService.pendingRecords = newPending;
		Promise.resolve(attendee);
	} catch (error) {
		handleError(error);
	}
}

function configureServices() {
	if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
		// Development code
		if (!Configuration.userService) {
			Configuration.eventService = new MockEventService();
			Configuration.userService = new MockUserService();
			Configuration.attendanceService = new MockAttendanceService();
		}

		if (!Configuration.pluginName) {
			Configuration.pluginName = 'workshop_attendance';
		}
	} else {
		// production code
		if (!Configuration.userService) {
			Configuration.eventService = new EventService();
			Configuration.userService = new UserService();
			Configuration.attendanceService = new AttendanceService();
		}

		if (!Configuration.pluginName) {
			const pathname = window.location.pathname;
			const pathComponents = pathname.split('/');
			Configuration.pluginName = pathComponents[pathComponents.length - 3];
		}
	}
}

async function loadEventId(setEventId) {
	try {
		let eventId = null;

		if (!process.env.NODE_ENV || process.env.NODE_ENV === 'development') {
			// dummy event Id
			eventId = 1000;
		}

		// get id from url param "event_id"
		if (!eventId) {
			const paramString = window.location.search;
			const urlParams = new URLSearchParams(paramString);
			eventId = urlParams.get('event_id');
		}

		// No event id? try getting today's event.
		if (!eventId) {
			configureServices();
			const eventService = Configuration.eventService;

			// try to find today's event
			const events = await eventService.retrieve(1 /* page */, 100 /* limit */, new Date());

			if (events.length === 1) {
				eventId = events[0].id;
			}
		}

		// Still no event id?  Create a new event
		if (!eventId) {
			configureServices();
			const eventService = Configuration.eventService;

			const date = new Date();
			// YYYY-MM-DD, local time please.
			const offset = date.getTimezoneOffset();
			const localDate = new Date(date.getTime() - (offset * 60 * 1000));
			const dateString = localDate.toISOString().substring(0, 10);

			const locale = navigator.languages[0];
			const titleString = localDate.toLocaleDateString(locale, { weekday: 'long' }) + " Workshop " + localDate.toLocaleDateString(locale, { day: 'numeric', month: 'long', year: 'numeric' });
			const event = {
				date: dateString,
				title: titleString
			};

			const createdEvent = await eventService.create(event);
			eventId = createdEvent.id;
		}

		setEventId(eventId);
		return Promise.resolve(eventId);
	} catch (error) {
		handleError(error);
	}
}

/// The actual component!
function AttendanceSheet(props) {
	const [eventId, setEventId] = useState(0);

	const [isLoading, setIsLoading] = useState(true);

	const [searchTerm, setSearchTerm] = useState('');

	const [filterRecent, setFilterRecent] = useState(false);
	const [filterOld, setFilterOld] = useState(false);
	const [filterNew, setFilterNew] = useState(false);
	const [filterPresent, setFilterPresent] = useState(false);

	const [eventRecord, setEventRecord] = useState(null);
	const [users, setUsers] = useState([]);
	const [recents, setRecents] = useState([])
	const [recentUserKeys, setRecentUserKeys] = useState(null);
	const [currentAttendees, setCurrentAttendees] = useState([]);
	const [pending, setPending] = useState([]);

	const [modificationPromise, setModificationPromise] = useState(null);

	let pendingMap = {};
	for (const pendingAttendee of pending) {
		const key = attendeeKey(pendingAttendee);
		pendingMap[key] = pendingAttendee;
	}

	// Set up configuration
	useEffect(() => {
		configureServices();
	});

	// load the current event id
	useEffect(() => {
		if (!eventId) {
			loadEventId(setEventId);
		}
	}, [eventId]);

	// Load initial data.
	useEffect(() => {
		if (isLoading && eventId) {
			loadAll(eventId, setIsLoading, setEventRecord, setUsers, setRecents, setRecentUserKeys, setCurrentAttendees)
		}
	}, [isLoading, eventId]);

	// Attendance
	const attendees = possibleAttendees(eventId, users, recents, currentAttendees, pending);

	// Search
	const searchedAttendees = searchAttendees(attendees, searchTerm);

	// Filter
	const filteredAttendees = filterAttendees(eventId, searchedAttendees, recentUserKeys, filterRecent, filterOld, filterNew, filterPresent);

	const showNewAttendeeForm = !isLoading && (filterNew || filterPresent || !filterRecent);

	// url for workshop or gig
	let eventURL = eventId ? "../../../../wp-admin/admin.php?page=workshop&workshop=" + eventId : null;
	
	if (Configuration.pluginName === 'carnie-gigs') {
		// http://localhost:8888/wordpress/wp-admin/post.php?post=16083&action=edit
	     eventURL = eventId ? "../../../../?page_id=" + eventId : null;
	}

	return (
		<div className="AttendanceSheet">
			<Header name={eventRecord ? eventRecord.title : ''} url={eventURL} />
			<SearchBar
				searchTerm={searchTerm} setSearchTerm={setSearchTerm}
				filterRecent={filterRecent} setFilterRecent={setFilterRecent}
				filterOld={filterOld} setFilterOld={setFilterOld}
				filterNew={filterNew} setFilterNew={setFilterNew}
				filterPresent={filterPresent} setFilterPresent={setFilterPresent}
			/>
			<AttendanceList attendees={filteredAttendees} event_id={eventId} pendingMap={pendingMap}
				addAttendanceRecord={(attendee) => {
					setModificationPromise(addAttendanceRecord(eventId, attendee, modificationPromise, pending, setPending, setCurrentAttendees));
				}}
				deleteAttendanceRecord={(attendee) => {
					setModificationPromise(deleteAttendanceRecord(eventId, attendee, modificationPromise, pending, setPending, setRecents, setCurrentAttendees));
				}}
			/>
			<NewAttendeeForm hideAttendeeForm={!showNewAttendeeForm}
				addAttendanceRecord={(attendee) => {
					setModificationPromise(addAttendanceRecord(eventId, attendee, modificationPromise, pending, setPending, setCurrentAttendees));
				}} />
			<Loading isLoading={isLoading} />
		</div>
	)
}
