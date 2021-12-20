/// webservice for getting users from wordpress.
/// See: https://dzone.com/articles/consuming-rest-api-with-reactjs
/// See: https://developer.wordpress.org/rest-api/reference/users/

class UserService {

    /// Get rest api location
    serviceLocation() {
        const pathname = window.location.pathname;
        const pathComponents = pathname.split('/');
        const pluginName = pathComponents[pathComponents.length - 3];

        return "../../../../?rest_route=/" + pluginName + "/v1/users";
    }

    restNonce() {
        const paramString = window.location.search;
        const urlParams = new URLSearchParams(paramString);
        return urlParams.get('_wpnonce');
    }

    /// Retrieve users.
    /// page and per_page are needed because of the limits on the wordpress json api.
    async retrieve(page, per_page) {

        const searchParams = new URLSearchParams({
            page: page,
            per_page: per_page,
            _wpnonce: this.restNonce()
        });
        const url = this.serviceLocation() + "&" + searchParams.toString();
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error("Failed to list users Response: " + response.status + " " + response.statusText);
        }

        return response.json();
    }
}

export default UserService;
