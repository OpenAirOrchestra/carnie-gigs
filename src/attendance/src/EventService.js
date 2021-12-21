import Configuration from './Configuration';

/// Restful web service for getting attendance records.
/// See:  https://dzone.com/articles/consuming-rest-api-with-reactjs
/// See:  https://developer.wordpress.org/rest-api/
class EventService {

    /// Get rest api location
    serviceLocation() {
        return "../../../../?rest_route=/" + Configuration.pluginName + "/v1/events";
    }

    restNonce() {
        const paramString = window.location.search;
        const urlParams = new URLSearchParams(paramString);
        return urlParams.get('_wpnonce');
    }

    async retrieve(page, per_page, date) {

        const searchParams = new URLSearchParams({
            page: page,
            per_page: per_page,
            _wpnonce: this.restNonce()
        });
        if (date) {
            // YYYY-MM-DD, local time please.
            const offset = date.getTimezoneOffset();
            const localDate = new Date(date.getTime() - (offset * 60 * 1000));
            const dateString = localDate.toISOString().substring(0, 10);

            searchParams.set('search', dateString);
        }
        const url = this.serviceLocation() + "&" + searchParams.toString();

        const response = await fetch(url);

        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to get workshops Response: " + response.status + " " + response.statusText);
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
            throw new Error("Failed to get workshop Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }

    // Create event (post)
    async create(event) {
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
            body: JSON.stringify(event)
        });
        if (!response.ok) {
            console.log("Failed url fetch: " + response.status + " " + response.statusText);
            const text = await response.text();
            console.log("Response text: " + text);
            throw new Error("Failed to create workshop Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }
}

export default EventService;
