function authFetch(url, options = {}) {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        return Promise.reject(new Error('No token found in localStorage'));
    }

    const headers = options.headers || {};
    headers['Authorization'] = `Bearer ${token}`;
    headers['Content-Type'] = 'application/json';

    const updatedOptions = {
        ...options,
        headers: headers
    };

    return fetch(url, updatedOptions)
        .then(response => {
            if (!response.ok) {
                return Promise.reject(new Error('Network response was not ok'));
            }
            return response.json();
        });
}
