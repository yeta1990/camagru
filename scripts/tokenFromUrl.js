const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
document.addEventListener('DOMContentLoaded', function() {
    const token = urlParams.get("token");
    localStorage.setItem('token', token);
    window.location.replace("/user/edit");
});