import React from 'react';

import AttendanceRecord, { attendeeKey } from './AttendanceRecord.js'

export default AttendanceList

function AttendanceList(props) {
  const attendees = props.attendees;
  const pendingMap = props.pendingMap ? props.pendingMap : {}
  const event_id = props.event_id;

  const addAttendanceRecord = props.addAttendanceRecord;
  const deleteAttendanceRecord = props.deleteAttendanceRecord;

  const rows = attendees
    .sort((a, b) => {
      let aFirstname = a.firstname.toLowerCase();
      let bFirstname = b.firstname.toLowerCase();

      if (aFirstname.toLowerCase() > bFirstname.toLowerCase()) {
        return 1;
      }

      if (aFirstname.toLowerCase() < bFirstname.toLowerCase()) {
        return -1;
      }

      let aLastname = a.lastname ? a.lastname.toLowerCase() : '';
      let bLastname = b.lastname ? b.lastname.toLowerCase() : '';

      if (aLastname.toLowerCase() > bLastname.toLowerCase()) {
        return 1;
      }

      if (aLastname.toLowerCase() < bLastname.toLowerCase()) {
        return -1;
      }

      return 0;
    })
    .map((attendee) =>
      <AttendanceRecord attendee={attendee} event_id={event_id} pendingMap={pendingMap} key={attendeeKey(attendee)}
        addAttendanceRecord={addAttendanceRecord}
        deleteAttendanceRecord={deleteAttendanceRecord} />
    );
  return (
    <div className='AttendanceList'>
      <table>
        <tbody>
          {rows}
        </tbody>
      </table>
    </div>
  )
}
