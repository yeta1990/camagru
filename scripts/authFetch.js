async function authFetch(url, options = {}) {
    const token = localStorage.getItem('token');
    const headers = options.headers || {};

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
        headers['Content-Type'] = 'application/json';
    }else if(window.location.pathname != '/login'){
        window.location.replace("/login");
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
            //TO DO: if response code == 401 redirect to login and remove token from localstorage
            return response.json();
        });
}
