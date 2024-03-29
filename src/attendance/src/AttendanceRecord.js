import React from 'react';
import Configuration from './Configuration';

export default AttendanceRecord

export function attendeeKey(attendee) {
  let key = attendee.user_id;

  if (!key || key === '0') {
    key = (attendee.firstname + '.' + attendee.lastname);
  }
  return key.toString();
}

function AttendanceRecord(props) {
  const attendee = props.attendee;
  const pendingMap = props.pendingMap ? props.pendingMap : {};
  const event_id = props.event_id;
  const addAttendanceRecord = props.addAttendanceRecord;
  const deleteAttendanceRecord = props.deleteAttendanceRecord;

  const key = attendeeKey(attendee);

  const pending = pendingMap[key];
  const present = attendee.event_id && (attendee.event_id === event_id);

  const maxNoteLen = 100;
  const shortNotes = !attendee.notes || attendee.notes.length < maxNoteLen ? attendee.notes : `${attendee.notes.substring(0, maxNoteLen)}\u2026`;
  const notes = shortNotes ? <div> {shortNotes} </div> : '';
  const phone = attendee.phone ? <span> {attendee.phone} </span> : '';
  const email = attendee.email ? <span> {attendee.email} </span> : '';

  const attendanceIcon = present ? '\u2714' : '\u274c';
  const attendanceClassName = present ? 'present' : 'absent';
  const pendingClassName = pending ? 'pending' : '';

  const showEmailAndPhone = Configuration.pluginName === 'workshop_attendance';

  const attendeeDetails = <div>
    {notes}
    {showEmailAndPhone ? email : ''}
    {showEmailAndPhone ? phone : ''}
  </div>;

  const pendingSpinner = pending ? (<span className="pending-spinner" />) : '';

  const lastname = attendee.lastname;
  return (
    <tr className={'AttendanceRecord  ' + attendanceClassName + ' ' + pendingClassName}
      onClick={() => {
        if (present && !pending) {
          deleteAttendanceRecord(attendee);
        } else if (!pending) {
          addAttendanceRecord(attendee);
        }
      }} >
      <td className='attendee' >
        {attendee.firstname + ' ' + lastname}
        <div className="details">
          {attendeeDetails}
        </div>
      </td>
      <td className='presence' >{pendingSpinner}<span className='presenceIcon'>{attendanceIcon}</span></td>
    </tr>
  )
}
