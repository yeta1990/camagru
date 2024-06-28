function authFetch(url, options = {}) {
    const token = localStorage.getItem('token');
    const headers = options.headers || {};

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
        headers['Content-Type'] = 'application/json';
    }

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
