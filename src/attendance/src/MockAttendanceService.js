
/// Mock restful web service for getting attendance records.
/// See:  https://dzone.com/articles/consuming-rest-api-with-reactjs
class MockAttendanceService {

    maxRecordId = 1000;
    pendingRecords = [];

    constructor() {
        this.attendanceRecords = [
            { user_id: 99, firstname: 'Zaphod', lastname: 'Beeblebrox', phone: '', email: '', notes: 'Presedent of the Galaxy', event_id: 300, id: 199 },
            { user_id: 100, firstname: 'Alice', lastname: 'Atalan', phone: '', email: '', notes: 'A Tuba', event_id: 1000, id: 200 },
            { user_id: 101, firstname: 'Bob', lastname: 'Shamilov', phone: '', email: '', notes: 'Bob is a fella', event_id: 1000, id: 201 },
            { user_id: 102, firstname: 'Charlie', lastname: 'Khalil', phone: '', email: '', notes: 'C Saxophone', event_id: 1000, id: 202 },
            { user_id: 103, firstname: 'Denise', lastname: 'Usoyan', phone: '', email: '', notes: '', event_id: 1000, id: 203 },
            { user_id: 104, firstname: 'Ethan', lastname: 'Adi', phone: '', email: '', notes: 'F Flute', event_id: 1000, id: 204 },

            { user_id: 105, firstname: 'Francine', lastname: 'Shavershian', phone: '', email: '', notes: 'C Flute', id: 205 },
            { user_id: 106, firstname: 'Greg', lastname: 'Mori', phone: '', email: '', notes: 'Clarinet', id: 206 },
            { user_id: 107, firstname: 'Harry', lastname: 'Tamoyan', phone: '', email: '', notes: 'Bassoon', id: 207 },
            { user_id: 108, firstname: 'Ichabod', lastname: 'Crane', phone: '', email: '', notes: 'Trombone', id: 208 },

            { user_id: 110, firstname: 'Niel', lastname: 'Armstrong', phone: '', email: '', notes: 'Astronaut', event_id: 10, id: 300 },
            { user_id: 111, firstname: 'Oprah', lastname: 'Khario', phone: '', email: '', notes: 'Celebrety', event_id: 10, id: 310 },
            { user_id: 112, firstname: 'Peter', lastname: 'Serdar', phone: '', email: '', notes: 'Dude', event_id: 10, id: 320 },
            { user_id: 113, firstname: 'Quinn', lastname: 'Evdal', phone: '', email: '', notes: 'Beatles', event_id: 10, id: 330 },

            { firstname: 'Sonny', lastname: 'Bono', phone: '', email: '', notes: 'Celebrity husband', event_id: 1000, id: 340 },
            { firstname: 'Frank', lastname: 'Zappa', phone: '', email: '', notes: 'Guitar', event_id: 1004, id: 350 }

        ];
    }

    async retrieve(page, per_page, event_id) {
        let records = this.attendanceRecords;
    
        if (event_id) {
            records = records.filter(record => record.event_id === event_id);
        }

        if (page) {
            let offset = (page - 1) * per_page;
            records = records.slice(offset, per_page + offset);
        }
        
        await new Promise((res) => setTimeout(res, 1000 * Math.random()));
        return Promise.resolve(records);
    }

    async get(id) {
        for (var i = 0; i < this.items.length; i++) {
            if (this.attendanceRecords[i].id === id) {
                return Promise.resolve(this.items[i]);
            }
        }
        return null;
    }

    async create(attendanceRecord) {

        // Fake delay
        await new Promise((res) => setTimeout(res, 1000 * Math.random()));

        // Add the new record
        ++this.maxRecordId;
        let newRecord = { ...attendanceRecord };
        newRecord.id = this.maxRecordId;
        this.attendanceRecords.push(newRecord);

        //Return the promise
        return Promise.resolve(newRecord);
    }

    async delete(id) {
        // Fake delay
        await new Promise((res) => setTimeout(res, 1000 * Math.random()));

        // Remove the record
        this.attendanceRecords = this.attendanceRecords.filter(attendee => { return id !== attendee.id; });

        //Return the promise
        return Promise.resolve(id);
    }
}

export default MockAttendanceService;
