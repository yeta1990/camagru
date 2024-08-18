document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('token'); 
});
document.getElementById('recoverForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    fetch('/api/user/recover', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email })
    })
    .then(data => {
        document.getElementById("recoverFormBanner").textContent = "If the email is valid, it will receive a recovery email";
        document.getElementById("recoverFormBanner").style.visibility= "visible";
    })
    .catch(error => {
    });
});