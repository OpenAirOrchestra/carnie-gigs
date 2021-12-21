import Configuration from './Configuration';

/// Restful web service for getting attendance records.
/// See:  https://dzone.com/articles/consuming-rest-api-with-reactjs
class AttendanceService {

    deleteUnsupported = false;

    /// Get rest api location
    serviceLocation() {
        return "../../../../?rest_route=/" + Configuration.pluginName + "/v1/attendees";
    }

    restNonce() {
        const paramString = window.location.search;
        const urlParams = new URLSearchParams(paramString);
        return urlParams.get('_wpnonce');
    }

    async retrieve(page, per_page, event_id) {
        const searchParams = new URLSearchParams({
            page: page,
            per_page: per_page,
            _wpnonce: this.restNonce()
        });

        if (event_id) {
            searchParams.set('search', event_id);
        }
        const url = this.serviceLocation() + "&" + searchParams.toString();

        const response = await fetch(url);

        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to get attendees Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }

    /// Get request (get an event)
    async get(id) {
        const searchParams = new URLSearchParams({
            _wpnonce: this.restNonce()
        });

        const url = this.serviceLocation() + "/" + id + "&" + searchParams.toString();
        const response = await fetch(url);

        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to get attendee Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }

    async create(attendanceRecord) {
        const searchParams = new URLSearchParams({
            _wpnonce: this.restNonce()
        });

        const url = this.serviceLocation() + "&" + searchParams.toString();
        const response = await fetch(url, {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(attendanceRecord)
        });

        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to create attendance record Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }

    async delete(attendanceRecord) {
        const id = attendanceRecord.id;
        let response = null;

        if (!this.deleteUnsupported) {
            // Firt try normal DELETE
            const searchParams = new URLSearchParams({
                _wpnonce: this.restNonce()
            });

            const url = this.serviceLocation() + "/" + id + "&" + searchParams.toString();

            response = await fetch(url, {
                method: "DELETE",
                mode: "cors"
            })

            if (!response.ok) {
                this.deleteUnsupported = true;
            }
        }

        if (this.deleteUnsupported) {
            // If DELETE failed, try the hack.  DELETE may fail because of server configuration.
            const searchParams = new URLSearchParams({
                method: 'DELETE',
                _wpnonce: this.restNonce()
            });
            const url = this.serviceLocation() + "/" + id + "&" + searchParams.toString();

            response = await fetch(url, {
                method: "POST",
                mode: "cors",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(attendanceRecord)
            });
        }

        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to delete attendee Response: " + response.status + " " + response.statusText);
        }

        return Promise.resolve(response);
    }
}

export default AttendanceService;
