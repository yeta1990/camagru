import { isValidPassword, validLength, isValidEmail } from './formValidation.js';

document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('token'); 
});

document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const username = document.getElementById('username').value;

    if (!validLength(email) || !validLength(password) || !validLength(username)){
        document.getElementById("signupFeedback").textContent = "All fields are mandatory but length is limited, what are you trying to do?";
        document.getElementById("signupFeedback").style.visibility = "visible";
        return;
    }
    else if (!isValidEmail(email)){
        document.getElementById("signupFeedback").textContent = "Please, insert a valid email.";
        document.getElementById("signupFeedback").style.visibility = "visible";
        return;
    }
    else if (!isValidPassword(password)) {
        document.getElementById("signupFeedback").textContent = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.";
        document.getElementById("signupFeedback").style.visibility = "visible";
        return;
    }

    authFetch('/api/user/signup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, username, password })
    })
    .then(data => {
        document.getElementById("signupFeedback").textContent = "Created successfully. Before login, you must confirm your account, check your mail!";
        document.getElementById("signupFeedback").style.visibility = "visible";
    })
    .catch(error => {
        document.getElementById("signupFeedback").textContent = error;
        document.getElementById("signupFeedback").style.visibility = "visible";
    });
});
