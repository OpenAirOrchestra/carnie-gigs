import React, { useState } from 'react';

export default NewAttendeeForm

/// Handle clicking on the add button
function handleAdd(addAttendanceRecord, firstname, setFirstname, lastname, setLastname, email, setEmail, phone, setPhone, notes, setNotes, attAttendanceRecord) {
  // Create record
  const attendee = { firstname: firstname, lastname: lastname, email: email, phone: phone, notes: notes };

  // Add the record
  addAttendanceRecord(attendee);

  // Clear the form.
  setFirstname('');
  setLastname('');
  setEmail('');
  setPhone('');
  setNotes('');
}

function NewAttendeeForm(props) {

  const hideAttendeeForm = props.hideAttendeeForm;
  const addAttendanceRecord = props.addAttendanceRecord;

  const [firstname, setFirstname] = useState('');
  const [lastname, setLastname] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [notes, setNotes] = useState('');

  if (hideAttendeeForm) {
    return null;
  }

  return (
    <div className='NewAttendeeForm'>
      <h2>Add New Attendee</h2>
      <form action="">
        <label htmlFor="firstname">First Name (required):</label>
        <input type="text" name="firstname" id="firstname" className='required' value={firstname} onChange={(event) => setFirstname(event.target.value)} />
        <br />
        <label htmlFor="lastname">Last Name (required): </label>
        <input type="text" name="lastname" id="lastname" className='required' value={lastname} onChange={(event) => setLastname(event.target.value)} />
        <br />
        <label htmlFor="email">Email:</label>
        <input type="text" name="email" id="email" value={email} onChange={(event) => setEmail(event.target.value)} />
        <br />
        <label htmlFor="phone">Phone:</label>
        <input type="text" name="phone" id="phone" value={phone} onChange={(event) => setPhone(event.target.value)} />
        <br />
        <label htmlFor="notes">Notes:</label>
        <br />
        <textarea name="notes" id="notes" value={notes} onChange={(event) => setNotes(event.target.value)} />
        <br />
        <div className='centered'>
          <input type="submit" value="Add" disabled={!(firstname && lastname)}
            onClick={() => {
              handleAdd(addAttendanceRecord, firstname, setFirstname, lastname, setLastname, email, setEmail, phone, setPhone, notes, setNotes);
            }} />
        </div>
      </form>
    </div>
  )
}
