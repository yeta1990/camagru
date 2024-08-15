
document.addEventListener('DOMContentLoaded', function() {
    authFetch(`/api/image/pages`)
        .then(data => {
            document.getElementById('id').value = data.id;
            document.getElementById('email').value = data.email;
            document.getElementById('username').value = data.username;
        })
        .catch(error => {
        });
});
