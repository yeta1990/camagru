async function authFetch(url, options = {}) {
    const token = localStorage.getItem('token');
    const headers = options.headers || {};

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
        headers['Content-Type'] = 'application/json';
    }else if(!["/home", "/signup"].includes(window.location.pathname)){
        window.location.replace("/home");
    }

    const updatedOptions = {
        ...options,
        headers: headers
    };

    return fetch(url, updatedOptions)
        .then(response => {return {"status": response.status, "data": response.json()}})
        .then(async data => {
            if (data.status != 200){
                const message = (await data["data"]).message;
                return Promise.reject(new Error(message));
            }
            return data["data"];
        });
}
